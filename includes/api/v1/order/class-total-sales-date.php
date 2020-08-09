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

    class TP_Total_sales_date {

        public static function listen(){
			global $wpdb;

			// Step1 : check if datavice plugin is activated
            if (TP_Globals::verify_datavice_plugin() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Plugin Missing!",
                    )
                );
            }

            //  Step2 : Validate if user is exist
			if (DV_Verification::is_verified() == false) {
				return rest_ensure_response(  
					array(
						"status" => "unknown",
                        "message" => "Please contact your administrator. Request Unknown!",
					)
				);
            }

             // Step3 : Sanitize all Request
			if (!isset($_POST["stid"])  ) {
				return array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Request unknown!",
                );
                
            }

            // Step 1: Check if ID is in valid format (integer)
            if (!is_numeric($_POST["stid"]) ) {
                return array(
                        "status" => "failed",
                        "message" => "Please contact your administrator. ID not in valid format!",
                );
                
            }

            // Step6 : Sanitize all Request
			if ( empty($_POST['stid']) ) {
				return array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Request unknown!",
                );
			}
			$store_id = $_POST['stid'];
            $get_store = $wpdb->get_row("SELECT ID FROM tp_stores  WHERE ID = $store_id  ");
                
             if ( !$get_store ) {
                return rest_ensure_response( 
                    array(
                        "status" => "error",
                        "message" => "An error occurred while fetching data to the server.",
                    )
                );
			}
			$date = TP_Globals::get_user_date($_POST['wpid']);
			return substr ($date, 5, 2);
			$store_id = $_POST["stid"];
			$result = $wpdb->get_row("SELECT COALESCE
					( FORMAT( sum( ( SELECT tp_rev.child_val FROM tp_revisions tp_rev WHERE ID = tp_prod.price ) ), 2 ), 0 ) AS total_sales 
				FROM
					mp_orders mp_ord
					LEFT JOIN mp_order_items mp_ord_itms ON mp_ord_itms.odid = mp_ord.ID
					LEFT JOIN tp_products tp_prod ON tp_prod.ID = mp_ord_itms.pdid 
				WHERE
					mp_ord.stid = $store_id");


			if ($result->total_sales <  1) {
				return array(
					"status" => "unknown",
					"message" => "No sales found.",
				);

			}else {
				return array(
					"status" => "success",
					"data" => array(
						'list' => $result
					)
			);
			}


        }
    }