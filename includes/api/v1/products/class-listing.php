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

            if (isset($_POST['stid']) && $_POST['stid'] > 0  ) {

                $stid = $_POST['stid'];

                $sql .= "WHERE tp_prod.`stid` = '$stid'  ";

                $type_all = $_POST['status'] == '0'? true:false; 

            }

            if (isset($_POST['catid']) && $_POST['catid'] > 0  ) {
                
                $cat_stats = $_POST['catid'] == '0' ? false:true;

                if($cat_stats == true){
                    $ctid = $_POST['catid'];
                    $sql .="AND tp_prod.ctid = $ctid ";
                }

            }

            if (isset($_POST['status']) && $_POST['status'] > 0 ) {
                
                $all = $_POST['status'] == '0'? true:false; 

                $status = $_POST['status'] == '2'? '0':'1';

                if ($all == false  ) {
                    if (isset($type_all) && $type_all == true) {
                        $sql .= "WHERE ";

                    }else{
                        $sql .= "AND ";

                    }
                    $sql .= " ( SELECT tp_rev.child_val FROM tp_revisions tp_rev WHERE ID = tp_prod.STATUS ) = $status  ";
                    
                }

            }

            $results =  $wpdb->get_results($sql);
            if (!$results) {
                return array(
                    "status" => "failed",
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
