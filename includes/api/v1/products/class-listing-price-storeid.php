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

    class TP_Listing_Product_Price_by_storeId {

        //REST API Call
        public static function listen(){
            
            return rest_ensure_response( 
                TP_Listing_Product_Price_by_storeId:: listing_price_by_Store_ID()
            );
        }

        public static function listing_price_by_Store_ID(){
            global $wpdb;

            if (TP_Globals::verify_datavice_plugin() == false) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Plugin Missing!",
                );
            }

			//  Step2 : Validate if user is exist
			if (DV_Verification::is_verified() == false) {
                return array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Request Unknown!",
                );
                
            }
            
            if (!isset($_POST['stid'])) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Missing Paramiters",
                );
            }

            if (empty($_POST['stid'])) {
                return array(
                    "status" => "failed",
                    "message" => "Required fields cannot be empty.",
                );
            }

            if (!is_numeric($_POST['stid'])) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. ID is not in valid format.",
                );
            }
            $stid = $_POST['stid'];
            $get_store = $wpdb->get_row("SELECT ID FROM tp_stores  WHERE ID = $stid ");
                
            //Check if this store id exists
             if ( !$get_store ) {
                return rest_ensure_response( 
                    array(
                        "status" => "error",
                        "message" => "An error occurred while fetching data to the server.",
                    )
                );
            }
            

            $table_product = TP_PRODUCT_TABLE;
            $table_stores = TP_STORES_TABLE;
            $table_categories = TP_CATEGORIES_TABLE;
            $table_revs = TP_REVISION_TABLE;

            $result = $wpdb->get_results("SELECT
                    tp_prod.ID,
                    tp_rev.child_val as price,
                    ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = ( SELECT title FROM $table_stores WHERE ID = tp_prod.stid ) ) AS store_title,
                    ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = ( SELECT title FROM $table_categories WHERE ID = tp_prod.ctid ) ) AS category_title,
                    ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.title ) AS product_title,
                    ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.preview ) AS product_preview,
                    ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.short_info ) AS product_short_info,
                    ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.long_info ) AS product_long_info,
                    ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.sku ) AS product_sku,
                    ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.weight ) AS product_weight,
                    ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.dimension ) AS product_dimension,
                    tp_prod.date_created 
                FROM
                    $table_product tp_prod 
                    INNER JOIN $table_revs tp_rev ON tp_rev.ID = tp_prod.price WHERE tp_prod.stid = $stid
            ");

            if(empty($result)){
                //Step 6: Return result
                return array(
                    "status" => "success",
                    "message" => "Please Contact your Administrator. Product fetch failed"
                );   

            }else {

                //Step 6: Return result
                return array(
                    "status" => "success",
                    "data" => array(
                        'list' => $result, 
                    )
                );

            }
     
        }
    }