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

    class TP_Best_Seller_Product_Local {
        public static function listen(){
            global $wpdb;

            //Check if prerequisites plugin are missing
            $plugin = TP_Globals::verify_prerequisites();
            if ($plugin !== true) {
                return array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. ".$plugin." plugin missing!",
                );
            }}

            //  Step2 : Validate if user is exist
			if (DV_Verification::is_verified() == false) {
                return array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Verification issues!",
                );
            }

            if (!isset($_POST['stid'])) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Request unknown!",
                );
            }



            if (empty($_POST['stid'])) {
                return array(
                    "status" => "failed",
                    "message" => "Required fields cannot be empty.",
                );
            }

            $store_id = $_POST['stid'];
            

			$order_items_table = MP_ORDER_ITEMS_TABLE;
			$order_items = MP_ORDERS_TABLE;
			$product_table = TP_PRODUCT_TABLE;
			$tp_revs_table = TP_REVISIONS_TABLE;

            $result =  $wpdb->get_results("SELECT
                Count( mp_ord_itms.odid ) AS cnt,
                    ( SELECT tp_rev.child_val FROM $tp_revs_table tp_rev WHERE ID = (SELECT title FROM $product_table WHERE ID = mp_ord_itms.pdid ) ) AS `product_name`,
                    ( SELECT tp_rev.child_val FROM $tp_revs_table tp_rev WHERE ID = (SELECT preview  FROM $product_table WHERE ID = mp_ord_itms.pdid )) AS `preview`,
                    ( SELECT tp_rev.child_val FROM $tp_revs_table tp_rev WHERE ID = (SELECT short_info   FROM $product_table WHERE ID = mp_ord_itms.pdid )) AS `short_info`,
                    ( SELECT tp_rev.child_val FROM $tp_revs_table tp_rev WHERE ID = (SELECT long_info    FROM $product_table WHERE ID = mp_ord_itms.pdid )) AS `long_info`,
                    ( SELECT tp_rev.child_val FROM $tp_revs_table tp_rev WHERE ID = (SELECT `status`    FROM $product_table WHERE ID = mp_ord_itms.pdid ) ) AS `status`,
                    ( SELECT tp_rev.child_val FROM $tp_revs_table tp_rev WHERE ID = (SELECT `sku`    FROM $product_table WHERE ID = mp_ord_itms.pdid ) ) AS `sku`,
                    ( SELECT tp_rev.child_val FROM $tp_revs_table tp_rev WHERE ID = (SELECT `price`    FROM $product_table WHERE ID = mp_ord_itms.pdid ) ) AS `price`,
                    ( SELECT tp_rev.child_val FROM $tp_revs_table tp_rev WHERE ID =(SELECT `weight`    FROM $product_table WHERE ID = mp_ord_itms.pdid ) ) AS `weight`,
                    ( SELECT tp_rev.child_val FROM $tp_revs_table tp_rev WHERE ID = (SELECT `dimension`    FROM $product_table WHERE ID = mp_ord_itms.pdid ) ) AS `dimension`
                FROM
                    $order_items mp_od
                    LEFT JOIN $order_items_table mp_ord_itms ON mp_ord_itms.odid = mp_od.ID
                WHERE mp_od.stid = $store_id
                GROUP BY
                mp_ord_itms.odid");

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
