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

    class TP_Best_Seller_Store {
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

			$order_items_table  = MP_ORDER_ITEMS_TABLE;
			$order_items        = MP_ORDERS_TABLE;
			$product_table      = TP_PRODUCT_TABLE;
			$tp_revs_table      = TP_REVISIONS_TABLE;
			$tp_store_table      = TP_STORES_TABLE;

            $table_brgy = DV_BRGY_TABLE;
            $table_city = DV_CITY_TABLE;
            $table_province = DV_PROVINCE_TABLE;
            $table_country = DV_COUNTRY_TABLE;
            $table_dv_revs = DV_REVS_TABLE;
            $table_add = DV_ADDRESS_TABLE;

            $result =  $wpdb->get_results("SELECT
                    mp_ord.ID,
                    Count( mp_ord.stid ) AS cnt,
                    ( SELECT child_val FROM $tp_revs_table WHERE id = tp_str.title ) AS title,
                    ( SELECT child_val FROM $tp_revs_table WHERE id = tp_str.short_info ) AS short_info,
                    ( SELECT child_val FROM $tp_revs_table WHERE id = tp_str.long_info ) AS long_info,
                    ( SELECT child_val FROM $tp_revs_table WHERE id = tp_str.logo ) AS avatar,
                    ( SELECT child_val FROM $tp_revs_table WHERE id = tp_str.banner ) AS banner,
                    ( SELECT child_val FROM $tp_revs_table WHERE id = tp_str.`status` ) AS status,
                    ( SELECT child_val FROM $table_dv_revs WHERE id = dv_add.street ) AS street,
                    ( SELECT brgy_name FROM $table_brgy WHERE ID = ( SELECT child_val FROM $table_dv_revs WHERE id = dv_add.brgy ) ) AS brgy,
                    ( SELECT citymun_name FROM $table_city WHERE city_code = ( SELECT child_val FROM $table_dv_revs WHERE id = dv_add.city ) ) AS city,
                    ( SELECT prov_name FROM $table_province WHERE prov_code = ( SELECT child_val FROM $table_dv_revs WHERE id = dv_add.province ) ) AS province,
                    ( SELECT country_name FROM $table_country WHERE id = ( SELECT child_val FROM $table_dv_revs WHERE id = dv_add.country ) ) AS country 
                FROM
                    $order_items mp_ord
                LEFT JOIN $tp_store_table tp_str ON tp_str.ID = mp_ord.stid
                LEFT JOIN $table_add dv_add ON tp_str.address = dv_add.ID
                GROUP BY
                mp_ord.stid Desc");

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
