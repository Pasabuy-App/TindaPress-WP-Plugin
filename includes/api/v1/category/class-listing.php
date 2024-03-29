<?php
	// Exit if accessed directly
	if ( ! defined( 'ABSPATH' ) )
	{
		exit;
	}

	/**
        * @package tindapress-wp-plugin
        * @version 0.1.0
	*/
    class TP_Category_Listing {

        public static function listen(){
            return rest_ensure_response(
                TP_Category_Listing:: list_type()
            );
        }

        public static function list_type(){

            // 2nd Initial QA 2020-08-24 5:16 PM - Miguel
            global $wpdb;
            $table_revisions = TP_REVISIONS_TABLE;
            $table_categories = TP_CATEGORIES_TABLE;
            $table_store = TP_STORES_TABLE;
            $table_product = TP_PRODUCT_TABLE;

            // Step 1: Check if prerequisites plugin are missing
            $plugin = TP_Globals::verify_prerequisites();
            if ($plugin !== true) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. ".$plugin." plugin missing!",
                );
            }

			// Step 2: Validate user
			if (DV_Verification::is_verified() == false) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. verification issues!",
                );

            }

            // Concatination query for category
            $sql = "SELECT
                    cat.ID,
                    cat.types,
                    cat.parent,
                    cat.groups,
                    IF ((SELECT child_val FROM $table_revisions WHERE revs_type = 'categories' AND cat.ID = parent_id AND child_key = 'avatar' ) is null, 'None',
                    (SELECT child_val FROM $table_revisions WHERE revs_type = 'categories' AND cat.ID = parent_id AND child_key = 'avatar' ) ) as `avatar`,
                IF  (
                    cat.`types` = 'store',
                    ( SELECT COUNT( ctid ) FROM $table_store WHERE ctid = cat.ID ),
                    ( SELECT COUNT( ctid ) FROM $table_product WHERE ctid = cat.ID )
                    ) AS `total`,
                    ( SELECT rev.child_val FROM $table_revisions rev WHERE `revs_type` = 'categories' AND ID = cat.title ) AS title,
                    ( SELECT rev.child_val FROM $table_revisions rev WHERE `revs_type` = 'categories' AND ID = cat.info ) AS info,
                IF
                    ( rev.child_val = 1, 'Active', 'Inactive' ) AS `status`,
                    'None' as categories
                FROM
                    $table_categories cat
                    INNER JOIN $table_revisions rev ON rev.ID = cat.`status` ";

            // Ternary for isset listener
            isset($_POST['stid'])   ? $std = $_POST['stid']   : $std = NULL  ;
            isset($_POST['catid'])  ? $cat = $_POST['catid']  : $cat = NULL  ;
            isset($_POST['status']) ? $sts = $_POST['status'] : $sts = NULL  ;
            isset($_POST['type'])   ? $typ = $_POST['type']   : $typ = NULL  ;

            // Ternary for value of varables
            $store_id    = $std  == '0' ? NULL: $store_id    = $std;
            $category_id = $cat  == '0' ? NULL: $category_id = $cat;
            $type        = $typ  == '0' ? NULL: ($typ == '1'? $type = 'store': ($typ == '2'? $type = 'product' : ($typ == '3'? $type = 'branch': $type = 'tags' ))  );
            $status      = $sts  == '0' || $sts == NULL ? NULL : ($sts == '2' && $sts !== '0'? '0':'1');

            // Condition for store ID
            if (isset($_POST['stid'])) {
                if ( $store_id != NULL ) {
                    $sql .= " WHERE cat.stid = '$store_id' ";
                }
            }

            // Condition for category ID
            if (isset($_POST['catid'])) {

                if ($store_id != NULL && $category_id != NULL) {

                    if ($category_id === "all" ) {
                        $sql .= " AND cat.ID NOT IN ('2','1','9')  AND cat.groups = 'inhouse' ";
                    }
                    else if ($category_id === "robinson" ) {
                        $sql .= " AND cat.ID NOT IN ('2','1','9')   AND  cat.groups = 'robinson'";
                    }
                    else{
                        $sql .= " AND cat.ID = '$category_id' ";
                    }

                }else{
                    if (!empty($category_id) ) {
                        if ($category_id === "all" ) {
                            $sql .= " WHERE cat.ID NOT IN ('2','1','9') AND cat.groups = 'inhouse' ";
                        }
                        else if ($category_id === "robinson" ) {
                            $sql .= " WHERE cat.ID NOT IN ('2','1','9') AND  cat.groups = 'robinson'  ";
                        }
                        else{
                            $sql .= " WHERE cat.ID = '$category_id' ";
                        }
                    }
                }
            }

            // Condition for status of category
            if (isset($_POST['status'])) {

                if ($status != NULL) {
                    if ($store_id != NULL || $category_id != NULL ) {
                        $sql .= " AND rev.child_val = '$status'  ";

                    }else{
                        if ($status != NULL) {
                            $sql .= " WHERE rev.child_val = '$status'  ";
                        }

                    }
                }
            }

            // Condition for category type
            if (isset($_POST['type'])) {

                if ($type != NULL) {

                    if (  $type != 'product' && $type != 'store' && $type != 'tags' && $type != 'branch') {
                        return array(
                            "status" => "failed",
                            "message" => "Invalid type of category.",
                        );
                    }

                    if ($type != NULL && $status != NULL || $store_id != NULL || $category_id != NULL  ) {
                        $sql .= " AND cat.types = '$type'  ";
                    }else{
                        $sql .= " WHERE cat.types = '$type'  ";

                    }
                }
            }
            
            if (isset($_POST['pid'])) {
                if (!empty($_POST['pid'])) {

                    $parent_id = $_POST['pid'];
                    if ($type != NULL && $status != NULL || $store_id != NULL || $category_id != NULL   ) {
                        $sql .= " AND cat.parent = '$parent_id'  ";
                    }else{
                        $sql .= " WHERE cat.parent = '$parent_id'  ";

                    }
                }
            }

            //return $sql;

            // Execute mysql query
            $results =  $wpdb->get_results($sql);

            return array(
                "status" => "success",
                "data" => $results,
            );
        }

        public static function catch_post(){
            $cur_user = array();

            $cur_user["store_id"] = $_POST["stid"];
            $cur_user["type"]     = $_POST["type"];
            $cur_user["status"]   = $_POST["status"];

            return  $cur_user;
        }
    }