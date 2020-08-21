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

            global $wpdb;
            $table_revs = TP_REVISIONS_TABLE;
            $table_categories = TP_CATEGORIES_TABLE;

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

            $sql = "SELECT
                    cat.ID,
                    cat.types,
                IF  (
                    cat.`types` = 'store',
                    ( SELECT COUNT( ctid ) FROM tp_stores WHERE ctid = cat.ID ),
                    ( SELECT COUNT( ctid ) FROM tp_products WHERE ctid = cat.ID ) 
                    ) AS `total`,
                    ( SELECT rev.child_val FROM tp_revisions rev WHERE `revs_type` = 'categories' AND ID = cat.title ) AS title,
                    ( SELECT rev.child_val FROM tp_revisions rev WHERE `revs_type` = 'categories' AND ID = cat.info ) AS info,
                IF
                    ( rev.child_val = 1, 'Active', 'Inactive' ) AS `status` 
                FROM
                    tp_categories cat
                    INNER JOIN tp_revisions rev ON rev.ID = cat.`status` ";

            isset($_POST['stid'])   ? $std = $_POST['stid']   : $std = NULL  ;
            isset($_POST['catid'])  ? $cat = $_POST['catid']  : $cat = NULL  ;
            isset($_POST['status']) ? $sts = $_POST['status'] : $sts = NULL  ;
            isset($_POST['type'])   ? $typ = $_POST['type']   : $typ = NULL  ;
                
            $store_id    = $std  == '0' ? NULL: $store_id    = $std;
            $category_id = $cat  == '0' ? NULL: $category_id = $cat;

            $type        = $typ  == '0' ? NULL: ($typ == '1'? $type = 'store': ($typ == '2'? $type = 'product' : $type = 'tags' ) );

            $status      = $sts  == '0' || $sts == NULL ? NULL : ($sts == '2' && $sts !== '0'? '0':'1');

            if (isset($_POST['stid'])) {
                if ( $store_id != NULL ) {
                    $sql .= " WHERE cat.stid = '$store_id' ";
                }
            }

            if (isset($_POST['catid'])) {
                
                if ($store_id != NULL && $category_id != NULL) {
                    $sql .= " AND cat.ID = '$category_id' ";

                }else{
                    if (!empty($category_id) ) {
                        $sql .= " WHERE cat.ID = '$category_id' ";
                    }

                }
            }

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

            if (isset($_POST['type'])) {

                if ($type != NULL) {

                    if (  $type != 'product' && $type != 'store' && $type != 'tags') {
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

            // return $sql;
            $results =  $wpdb->get_results($sql);
            if (!$results) {
                return array(
                    "status" => "success",
                    "message" => "No results found.",
                );
            } else {
                return array(
                    "status" => "success",
                    "data" => $results,
                );
            }
        
        }

        public static function catch_post(){
            $cur_user = array();

            $cur_user["store_id"] = $_POST["stid"];
            $cur_user["type"]     = $_POST["type"];
            $cur_user["status"]   = $_POST["status"];

            return  $cur_user;
        }
    }