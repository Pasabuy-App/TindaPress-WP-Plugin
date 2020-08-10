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
    class TP_OrdersByStage {

        public static function listen(){
            return rest_ensure_response( 
                TP_OrdersByStage:: list_open()
            );
        }

        public static function list_open(){

            //Initial QA done 2020-08-10 11:06 am
            //Added validation on stages
            
            global $wpdb;
            
            // Step1 : check if datavice plugin is activated
            if (TP_Globals::verify_datavice_plugin() == false) {
                return array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Plugin Missing!",
                );
            }
           
            // Step2 : Check if wpid and snky is valid
            if (DV_Verification::is_verified() == false) {
                return array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Verification Issues!",
                );
            }
            
            // Step3 : Sanitize all Request
            if (!isset($_POST["stage"])) {
                return array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Request unknown!",
                );
            }

            // Step4 : sanitize if all variables is empty
            if (empty($_POST["stage"])) {
                return array(
                        "status" => "failed",
                        "message" => "Required fields cannot be empty.",
                );
            }


            //Ensures that `stage` is correct
            if ( !($_POST['stage'] === 'pending') && !($_POST['stage'] === 'received') && !($_POST['stage'] === 'delivered') && !($_POST['stage'] === 'shipping') && !($_POST['stage'] === 'cancelled') ) {
                return array(
                    "status" => "failed",
                    "message" => "Invalid stage.",
                );
            }

            // variables for query
            $table_store = TP_STORES_TABLE;
            $table_products = TP_PRODUCT_TABLE;
            $table_revs = TP_REVISION_TABLE;
            $table_orders = 'mp_orders';
            $table_order_items = 'mp_order_items';
            $stage = $_POST['stage'];
        
            // Step5 : Query
            $result = $wpdb->get_results("SELECT
                    mp_ordtem.ID,
                    mp_ord.`status` as status,
                    (select child_val from $table_revs where id = (select title from $table_store where id = mp_ord.stid)) AS store,
                    (select child_val from $table_revs where id = (select title from $table_products where id = mp_ordtem.pdid)) AS orders,
                    mp_ordtem.quantity as qty,
                    mp_ord.date_created as date_ordered
                FROM
                    $table_order_items as mp_ordtem
                INNER JOIN 
                    $table_orders as mp_ord ON mp_ord.ID = mp_ordtem.odid
                WHERE 
                    mp_ord.`status` = '$stage'
            ");
            
            // Step6 : Check if no result
            if (!$result)
            {
                return array(
                        "status" => "failed",
                        "message" => "No orders found",
                );
            }
            
            // Step7 : Return Result 
            return array(
                    "status" => "success",
                    "data" => $result
                
            );
            
        }

    }