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
    class TP_Product_Nearme {

        public static function listen(){
            return rest_ensure_response( 
                TP_Product_Nearme::listen_open()
            );
        }
        
        public static function listen_open(){

            // 2nd Initial QA 2020-08-24 6:45 PM - Miguel
            global $wpdb;

            $table_revisions = TP_REVISIONS_TABLE;
            $table_product = TP_PRODUCT_TABLE;
            $table_categories = TP_CATEGORIES_TABLE;
            $table_variants = TP_VARIANTS_TABLE;
            $table_store = TP_STORES_TABLE;
            $table_address = DV_ADDRESS_TABLE;
            $table_dv_revision = DV_REVS_TABLE;

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
                    "message" => "Please contact your administrator. Verification issues!",
                );
            }
            // Step 3: Validate listen
            if (!isset($_POST['lat']) || !isset($_POST['long']) ) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Request unknown missing paramiters!",
                );
            }
            // Step 3: Validate listen
            if (empty($_POST['lat']) || empty($_POST['long']) ) {
                return array(
                    "status" => "failed",
                    "message" => "Required fileds cannot be empty.",
                );
            }

            // Step 4: Variable declaration 
            $lat2 = $_POST['lat'];
            $long2 = $_POST['long'];

            // Step 5: Start query
            $results = $wpdb->get_results("SELECT
               tp_prod.ID,
                tp_prod.stid,
                tp_prod.ctid AS catid,
                ( SELECT COUNT(pdid) FROM $table_variants WHERE pdid = tp_prod.ID AND parent_id = 0 ) as `total`,
                ( SELECT tp_rev.child_val FROM $table_revisions tp_rev WHERE ID = ( SELECT `title` FROM tp_stores WHERE ID = tp_prod.stid ) AND revs_type = 'stores' AND child_key ='title' AND tp_rev.ID = (SELECT MAX(ID) FROM $table_revisions WHERE ID = tp_rev.ID )  ) AS `store_name`,
                ( SELECT tp_rev.child_val FROM $table_revisions tp_rev WHERE ID = c.title AND revs_type = 'categories' AND child_key ='title' AND tp_rev.ID = (SELECT MAX(ID) FROM $table_revisions WHERE ID = tp_rev.ID ) ) AS `cat_name`,
                ( SELECT tp_rev.child_val FROM $table_revisions tp_rev WHERE tp_rev.ID = tp_prod.title  AND revs_type = 'products' AND child_key ='title' AND tp_rev.ID = (SELECT MAX(ID) FROM $table_revisions WHERE ID = tp_rev.ID )  ) AS product_name,
                ( SELECT tp_rev.child_val FROM $table_revisions tp_rev WHERE ID = tp_prod.short_info  AND revs_type = 'products' AND child_key ='short_info' AND tp_rev.ID = (SELECT MAX(ID) FROM $table_revisions WHERE ID = tp_rev.ID ) ) AS `short_info`,
                ( SELECT tp_rev.child_val FROM $table_revisions tp_rev WHERE ID = tp_prod.long_info AND revs_type = 'products' AND child_key ='long_info' AND tp_rev.ID = (SELECT MAX(ID) FROM $table_revisions WHERE ID = tp_rev.ID )  ) AS `long_info`,
                ( SELECT tp_rev.child_val FROM $table_revisions tp_rev WHERE ID = tp_prod.sku   AND revs_type = 'products' AND child_key ='sku' AND tp_rev.ID = (SELECT MAX(ID) FROM $table_revisions WHERE ID = tp_rev.ID ) ) AS `sku`,
                ( SELECT tp_rev.child_val FROM $table_revisions tp_rev WHERE ID = tp_prod.price AND revs_type = 'products' AND child_key ='price' AND tp_rev.ID = (SELECT MAX(ID) FROM $table_revisions WHERE ID = tp_rev.ID )  ) AS `price`,
                ( SELECT tp_rev.child_val FROM $table_revisions tp_rev WHERE ID = tp_prod.weight AND revs_type = 'products' AND child_key ='weight' AND tp_rev.ID = (SELECT MAX(ID) FROM $table_revisions WHERE ID = tp_rev.ID )  ) AS `weight`,
                ( SELECT tp_rev.child_val FROM $table_revisions tp_rev WHERE ID = tp_prod.dimension AND revs_type = 'products' AND child_key ='dimension' AND tp_rev.ID = (SELECT MAX(ID) FROM $table_revisions WHERE ID = tp_rev.ID ) ) AS `dimension`,
                IF  ( ( SELECT tp_rev.child_val FROM $table_revisions tp_rev WHERE ID = tp_prod.`status` AND revs_type = 'products' AND child_key = 'status' AND tp_rev.ID = ( SELECT MAX(ID) FROM $table_revisions WHERE ID = tp_rev.ID )  ) = 1, 'Active', 'Inactive' ) AS `status`,
                ROUND(  (SELECT `distance_kilometer`( 
                        (SELECT child_val FROM $table_dv_revision WHERE ID = 	( SELECT `latitude` FROM $table_address WHERE ID = ( SELECT `address` FROM $table_store WHERE ID = tp_prod.stid ) AND types = 'business' ) AND revs_type ='address' ), 
                        (SELECT child_val FROM $table_dv_revision WHERE ID = 	( SELECT `longitude` FROM $table_address WHERE ID = ( SELECT `address` FROM $table_store WHERE ID = tp_prod.stid ) AND types = 'business' ) AND revs_type ='address' ), 
                        '$lat2', 
                        '$long2'  
                        )),
                    3 ) as distance
            FROM
                $table_product tp_prod
                INNER JOIN $table_revisions tp_rev ON tp_rev.ID = tp_prod.title
                INNER JOIN $table_categories c ON c.ID = tp_prod.ctid ");

            // Step 3: Return results
            return array(
                "status" => "success",
                "data" => $results
            );
            
        }
    }