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
?>
<?php

    class TP_Select_Byid_Product {

        public static function listen(){
            global $wpdb;
                
            //Check if prerequisites plugin are missing
            $plugin = TP_Globals::verify_prerequisites();
            if ($plugin !== true) {
                return array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. ".$plugin." plugin missing!",
                );
            }

			//  Step2 : Validate if user is exist
			if (DV_Verification::is_verified() == false) {
                return array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Verification issues!",
                );
                
            }

            // Step2 : Sanitize all Request
			if ( !isset($_POST['pdid'])  ) {
				return array(
					"status" => "unknown",
					"message" => "Please contact your administrator. Request unknown!",
                );
                
            }

            // Step5 : Sanitize all Request
			if ( empty($_POST['pdid']) ) {
				return array(
					"status" => "unknown",
					"message" => "Required field cannot be empty",
                );
                
            }

            $user = TP_Select_Byid_Product::catch_post();
            $table_product = TP_PRODUCT_TABLE;
            $table_stores = TP_STORES_TABLE;
            $table_categories = TP_CATEGORIES_TABLE;
            $table_revs = TP_REVISIONS_TABLE;

            $result =  $wpdb->get_results("SELECT
                    tp_products.ID as product_id,
                    (SELECT tp_revisions.child_val FROM tp_revisions WHERE tp_revisions.ID = tp_stores.title) as product_store_name,
                    (SELECT tp_revisions.child_val FROM tp_revisions WHERE tp_revisions.ID = (SELECT tp_categories.title FROM tp_categories WHERE tp_categories.ID = tp_products.ctid)) as product_category_name,
                    tp_revisions.child_val as product_name,
                    (SELECT tp_revisions.child_val FROM tp_revisions WHERE ID = tp_products.preview) as product_preview,
                    (SELECT tp_revisions.child_val FROM tp_revisions WHERE ID = tp_products.short_info) as product_short_information,
                    (SELECT tp_revisions.child_val FROM tp_revisions WHERE ID = tp_products.long_info) as product_long_information,
                    (SELECT tp_revisions.child_val FROM tp_revisions WHERE ID = tp_products.`status`) as product_status,
                    (SELECT tp_revisions.child_val FROM tp_revisions WHERE ID = tp_products.sku) as product_code,
                    (SELECT tp_revisions.child_val FROM tp_revisions WHERE ID = tp_products.price) as product_price,
                    (SELECT tp_revisions.child_val FROM tp_revisions WHERE ID = tp_products.weight) as product_weight,
                    (SELECT tp_revisions.child_val FROM tp_revisions WHERE ID = tp_products.dimension) as product_dimension,
                    tp_products.date_created
                FROM
                    tp_products
                    INNER JOIN tp_revisions ON tp_revisions.ID = tp_products.title
                    INNER JOIN tp_stores ON tp_stores.ID = tp_products.stid
                WHERE tp_products.ID = {$user["product_id"]}
                    ");

            if(empty($result)){
                //Step 6: Return result
                return array(
                    "status" => "failed",
                    "message" => "No results found"
                );   
            }else {
                //Step 6: Return result
                return array(
                    "status" => "success",
                    "data" => $result
                );
            }
        }

        // Catch Post 
        public static function catch_post()
        {
              $cur_user = array();
               
              $cur_user['created_by'] = $_POST["wpid"];
              $cur_user['product_id'] = $_POST["pdid"];

              return  $cur_user;
        }
    }
