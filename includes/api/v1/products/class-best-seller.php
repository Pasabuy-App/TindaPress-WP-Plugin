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

    class TP_Best_Seller_Product {

        //REST API Call
        public static function listen(){
            
            return rest_ensure_response( 
                TP_Best_Seller_Product:: best_seller_product_global()
            );
        }

        public static function best_seller_product_global(){
            
            global $wpdb;

            // Table Variables 
            $order_items_table = MP_ORDER_ITEMS_TABLE;
			$order_items = MP_ORDERS_TABLE;
			$product_table = TP_PRODUCT_TABLE;
			$tp_revs_table = TP_REVISION_TABLE;

            // Step 1 : Check if prerequisites plugin are missing
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

            // Step 3 : Query  
            $result =  $wpdb->get_results("SELECT
                Count( mp_ord_itms.odid ) AS cnt,
                ( SELECT tp_rev.child_val FROM $tp_revs_table tp_rev WHERE ID = (SELECT title       FROM $product_table WHERE ID = mp_ord_itms.pdid )) AS `product_name`,
                ( SELECT tp_rev.child_val FROM $tp_revs_table tp_rev WHERE ID = (SELECT preview     FROM $product_table WHERE ID = mp_ord_itms.pdid )) AS `preview`,
                ( SELECT tp_rev.child_val FROM $tp_revs_table tp_rev WHERE ID = (SELECT short_info  FROM $product_table WHERE ID = mp_ord_itms.pdid )) AS `short_info`,
                ( SELECT tp_rev.child_val FROM $tp_revs_table tp_rev WHERE ID = (SELECT long_info   FROM $product_table WHERE ID = mp_ord_itms.pdid )) AS `long_info`,
                ( SELECT tp_rev.child_val FROM $tp_revs_table tp_rev WHERE ID = (SELECT `status`    FROM $product_table WHERE ID = mp_ord_itms.pdid )) AS `status`,
                ( SELECT tp_rev.child_val FROM $tp_revs_table tp_rev WHERE ID = (SELECT `sku`       FROM $product_table WHERE ID = mp_ord_itms.pdid )) AS `sku`,
                ( SELECT tp_rev.child_val FROM $tp_revs_table tp_rev WHERE ID = (SELECT `price`     FROM $product_table WHERE ID = mp_ord_itms.pdid )) AS `price`,
                ( SELECT tp_rev.child_val FROM $tp_revs_table tp_rev WHERE ID = (SELECT `weight`    FROM $product_table WHERE ID = mp_ord_itms.pdid )) AS `weight`,
                ( SELECT tp_rev.child_val FROM $tp_revs_table tp_rev WHERE ID = (SELECT `dimension` FROM $product_table WHERE ID = mp_ord_itms.pdid )) AS `dimension`
            FROM
                $order_items_table mp_ord_itms
            GROUP BY
                mp_ord_itms.odid");

            // Step 4 : Return results 
            if (!$result) {
                return array(
                    "status" => "failed",
                    "message" =>  "No results found.",
                );

            }else{
                return array(
                    "status" => "success",
                    "data" =>  max($result),
                );

            }
            
        }
    }
