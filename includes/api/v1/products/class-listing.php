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
    class TP_Product_Listing {
        public static function listen(){
            return rest_ensure_response( 
                TP_Product_Listing:: list_type()
            );
        }

        public static function list_type(){
            global $wpdb;
            $table_revs = TP_REVISIONS_TABLE;
            $table_product = TP_PRODUCT_TABLE;
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
            
            if (!isset($_POST['stid']) || !isset($_POST['catid']) || !isset($_POST['status'])  ) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your admininstrator. Missing paramiters!"
                );
            }

            $sql = "SELECT
                tp_prod.ID,
                tp_prod.stid,
                tp_prod.ctid AS catid,
                ( SELECT tp_rev.child_val FROM tp_revisions tp_rev WHERE ID = ( SELECT `title` FROM tp_stores WHERE ID = tp_prod.stid ) ) AS `store_name`,
                ( SELECT tp_rev.child_val FROM tp_revisions tp_rev WHERE ID = c.title ) AS `cat_name`,
                ( SELECT tp_rev.child_val FROM tp_revisions tp_rev WHERE tp_rev.ID = tp_prod.title ) AS product_name,
                ( SELECT tp_rev.child_val FROM tp_revisions tp_rev WHERE ID = tp_prod.short_info ) AS `short_info`,
                ( SELECT tp_rev.child_val FROM tp_revisions tp_rev WHERE ID = tp_prod.long_info ) AS `long_info`,
                ( SELECT tp_rev.child_val FROM tp_revisions tp_rev WHERE ID = tp_prod.sku ) AS `sku`,
                ( SELECT tp_rev.child_val FROM tp_revisions tp_rev WHERE ID = tp_prod.price ) AS `price`,
                ( SELECT tp_rev.child_val FROM tp_revisions tp_rev WHERE ID = tp_prod.weight ) AS `weight`,
                ( SELECT tp_rev.child_val FROM tp_revisions tp_rev WHERE ID = tp_prod.dimension ) AS `dimension`,
            IF
                ( ( SELECT tp_rev.child_val FROM tp_revisions tp_rev WHERE ID = tp_prod.STATUS ) = 1, 'Active', 'Inactive' ) AS `status` 
            FROM
                tp_products tp_prod
                INNER JOIN tp_revisions tp_rev ON tp_rev.ID = tp_prod.title
                INNER JOIN tp_categories c ON c.ID = tp_prod.ctid  ";

            isset($_POST['status']) ? $sts = $_POST['status'] : $sts = NULL  ;
            isset($_POST['stid'])   ? $std = $_POST['stid']   : $std = NULL  ;
            isset($_POST['catid'])  ? $ctd = $_POST['catid']  : $ctd = NULL  ;

            (int)$status = $sts == '0'? NULL:($sts == '2'? '0':'1')  ;
            (int)$catid = $ctd == '0'? '0': $catid = $ctd;
            (int)$stid = $std == '0'? '0': $stid = $std;


            if($status != NULL){

                $sql .= " WHERE  ( SELECT tp_rev.child_val FROM tp_revisions tp_rev WHERE ID = tp_prod.STATUS ) = $status";
                
            }

            if ($catid != NULL && $catid != '0') {

                if ($status !== NULL ) {

                    $sql .= " AND tp_prod.ctid = $catid ";
                }else{
                    $sql .= " WHERE tp_prod.ctid = $catid ";

                }

            }

            if ($stid != NULL && $stid != '0' ) {
                if ($status !== NULL ) {

                    $sql .= " AND tp_prod.stid = $stid ";
                }else{
                    $sql .= " WHERE tp_prod.stid = $stid ";

                }
            }

            // return $sql;
            $results =  $wpdb->get_results($sql);
            if (!$results) {
                return array(
                    "status" => "success",
                    "message" => "No resuls found",
                );

            }else{
                return array(
                    "status" => "success",
                    "data" => $results,
                );

            }
        }
        
    }
