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

	class TP_Product_popular {
        public static function listen(){
            global $wpdb;

             // Step 1 : Verfy if Datavice Plugin is Activated
			if (TP_Globals::verify_datavice_plugin() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Plugin Missing!",
                    )
                );
			}


            // Step6 : Sanitize all Request if emply
			if (empty($_POST["wpid"]) || empty($_POST["snky"])  ) {
				return rest_ensure_response( 
					array(
						"status" => "unknown",
						"message" => "Required fields cannot be empyty.",
					)
                );
                
            }

            $table_product = TP_PRODUCT_TABLE;
            $tp_revs = TP_REVISION_TABLE;
            $mp_order_items = MP_ORDER_ITEMS_TABLE;

            $result = $wpdb->get_results("SELECT
                    Count( mp_oi.pdid ) AS cnt,
                    mp_oi.pdid,
                    tp_prod.ID,
                    ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.title ) AS `title`,
                    ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.preview ) AS `preview`,
                    ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.short_info ) AS `short_info`,
                    ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.long_info ) AS `long_info`,
                    ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.`status` ) AS `status`,
                    ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.sku ) AS `sku`,
                    ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.price ) AS `price`,
                    ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.weight ) AS `weight`,
                    ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.dimension ) AS `dimension` 
                FROM
                    $mp_order_items mp_oi 
                    INNER JOIN $table_product tp_prod ON mp_oi.pdid = tp_prod.ID 
                    INNER JOIN $tp_revs tp_rev ON tp_prod.title = tp_rev.ID 
                GROUP BY
                    mp_oi.pdid 
                ORDER BY
                    cnt DESC
                ");

            if(is_null($result)){
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator.",
                    )
                );

            }else{
                return rest_ensure_response( 
                    array(
                        "status" => "success",
                        "data" => array(
                            'list' => $result, 
                        
                        )
                    )
                );

            }

        }
    }
        