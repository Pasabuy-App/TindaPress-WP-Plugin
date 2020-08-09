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
    class TP_OrdersByDate {

        public static function listen(){
            return rest_ensure_response( 
                TP_OrdersByDate:: list_open()
            );
        }

        public static function list_open(){

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

            // Step6 : Validation of store id
            $stid = $_POST["stid"];
            $get_store = $wpdb->get_row("SELECT ID FROM $table_store  WHERE ID = $stid  ");
                
             if ( !$get_store ) {
                return array(
                        "status" => "failed",
                        "message" => "No store found.",
                );
			}

            // variables for query
            $table_store = TP_STORES_TABLE;
            $table_products = TP_PRODUCT_TABLE;
            $table_revs = TP_REVISION_TABLE;
            $table_orders = MP_ORDERS_TABLE;
            $table_ordes_items = MP_ORDER_ITEMS_TABLE;
            $stage = $_POST['stage'];
        
            // Step5 : Query
           $result = $wpdb->get_results("SELECT
           mp_ordtem.ID,
           (select child_val from $table_revs where id = (select title from $table_store where id = mp_ord.stid)) AS store,
           (select child_val from $table_revs where id = (select title from $table_products where id = mp_ordtem.pdid)) AS orders,
           mp_ordtem.quantity as qty,
           mp_ord.date_created as date_ordered
           FROM
           $table_ordes_items as mp_ordtem
           INNER JOIN $table_orders as mp_ord ON mp_ord.ID = mp_ordtem.odid
           WHERE mp_ord.`status` = '$stage'
            ");
            
            // Step6 : Check if no result
            if (!$result)
            {
                return array(
                        "status" => "failed",
                        "message" => "No order found by this value.",
                );
            }
            
            // Step7 : Return Result 
            return array(
                    "status" => "success",
                    "data" => array($result,
                )
            );
            
        }

    }