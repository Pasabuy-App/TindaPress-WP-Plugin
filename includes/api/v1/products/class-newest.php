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

    class TP_Product_Newest {
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

            // Step6 : Sanitize all Request
			if (empty($_POST["wpid"]) || empty($_POST["snky"]) ) {
				return rest_ensure_response( 
					array(
						"status" => "unknown",
						"message" => "Required fields cannot be empty",
					)
                );
                
            }

            // step 7: Put to variables all needed data
            $date_now = TP_Globals::date_stamp();
            $date=date_create($date_now);
            date_sub( $date, date_interval_create_from_date_string("7 days"));
            $date_expected =  date_format($date,"Y-m-d");

            $store_table           = TP_STORES_TABLE;
            $categories_table      = TP_CATEGORIES_TABLE;
            $product_table         = TP_PRODUCT_TABLE;
            $table_revs            = TP_REVISIONS_TABLE;
            
            // step 8: fetch data from databse
            $result = $wpdb->get_results("SELECT
                    tp_prod.ID as product_id,
                    (SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE tp_rev.ID = tp_str.title) as product_store_name,
                    (SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE tp_rev.ID = (SELECT tp_cat.title FROM $categories_table tp_cat WHERE tp_cat.ID = tp_prod.ctid)) as product_category_name,
                    tp_rev.child_val as product_name,
                    (SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.preview) as product_preview,
                    (SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.short_info) as product_short_information,
                    (SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.long_info) as product_long_information,
                    (SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.`status`) as product_status,
                    (SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.sku) as product_code,
                    (SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.price) as product_price,
                    (SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.weight) as product_weight,
                    (SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.dimension) as product_dimension,
                    tp_prod.date_created
                FROM
                    $product_table tp_prod
                    INNER JOIN $table_revs tp_rev ON tp_rev.ID = tp_prod.title
                    INNER JOIN $store_table tp_str  ON tp_str.ID = tp_prod.stid
                WHERE
                SUBSTRING( tp_prod.date_created, 1, 10 ) BETWEEN '$date_expected' 
                    AND '$date_now'
                GROUP BY
                    tp_prod.ID 
                ORDER BY
                    RAND() 
                LIMIT 10");
           
           // step 9: return result
            return array(
                "status" => "success",
                "data" => array(
                    'list' => $result, 
                )
            );

        }
    }