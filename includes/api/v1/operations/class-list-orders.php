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
    class TP_List_Orders {
        
        public static function listen(){
            return rest_ensure_response( 
                TP_List_Orders:: list_orders()
            );
        }
        
        public static function list_orders(){
           
            global $wpdb;

            //  Step1 : Verify if Datavice Plugin is Active
			if (TP_Globals::verify_datavice_plugin() == false) {
                return  array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Plugin Missing!",
                );
                
			}
			
			//  Step2 : Validate if user is exist
			if (DV_Verification::is_verified() == false) {
                return array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Verification Issues!",
                );
                
            }

            if (!isset($_POST["ops_id"]) ) {
				return array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Request unknown!",
                );
            }

            if (empty($_POST["ops_id"]) ) {
				return array(
						"status" => "failed",
						"message" => "Required fields cannot be empty.",
                );
            }

            $operations_id = $_POST["ops_id"];
            
            $get_operation = $wpdb->get_row("SELECT `ID`
                FROM
                    `mp_operations`
                WHERE
                    `ID` = $operations_id
            ");

            if (!$get_operation) {
                return array(
						"status" => "failed",
						"message" => "This operation id does not exists.",
                );
            }
             

            $table_revs = TP_REVISION_TABLE;
            $table_categories = TP_CATEGORIES_TABLE;

            //Get results
            $orders = $wpdb->get_results("SELECT st.ID, ops.id as operation_id, o.id as order_id,
                ( SELECT rev.child_val FROM $table_revs rev WHERE ID = st.title ) AS `store_name`,
                ( SELECT rev.child_val FROM $table_revs rev WHERE ID = p.title ) AS `product_name`,
                ( SELECT rev.child_val FROM $table_revs rev WHERE ID = p.price ) AS `product_price`,
                oi.quantity as quantity
                FROM
                    tp_stores st
                INNER JOIN 
                    $table_revs rev ON rev.ID = st.`status` 
                INNER JOIN
                    mp_operations ops ON ops.stid = st.ID
                INNER JOIN
                    mp_orders o	ON o.opid = ops.id
                INNER JOIN
                    mp_order_items oi ON oi.odid = o.id
                INNER JOIN
                    tp_products p ON p.id = oi.pdid
                WHERE 
                    rev.child_val = 1
                AND
                    ops.id = $operations_id");


            if ( !$orders) {
                return array(
                    "status" => "failed",
                    "message" => "No results found.",
                );
            }

            return array(
                "status" => "success",
                "data" => $orders
            );

        
        }

    }