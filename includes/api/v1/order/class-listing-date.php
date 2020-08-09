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

            // variables for query
            $table_store = TP_STORES_TABLE;
            $table_products = TP_PRODUCT_TABLE;
            $table_revs = TP_REVISION_TABLE;
            $table_orders = MP_ORDERS_TABLE;
            $table_ordes_items = MP_ORDER_ITEMS_TABLE;
            $dt = TP_Globals::convert_date($_POST["wpid"],$_POST["date"]);

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
            if (!isset($_POST["stid"]) || !isset($_POST["date"])) {
                return array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Request unknown!",
                );
            }

            // Step4 : sanitize if all variables is empty
            if (empty($_POST["stid"]) || empty($_POST["date"])) {
                return array(
                        "status" => "failed",
                        "message" => "Required fields cannot be empty.",
                );
            }

            // Step5 : Validation of store id
            $stid = $_POST["stid"];
            $get_store = $wpdb->get_row("SELECT ID FROM $table_store  WHERE ID = $stid  ");
                
             if ( !$get_store ) {
                return array(
                        "status" => "failed",
                        "message" => "No store found.",
                );
            }

            // Step6 : Validate date with format
            return $valdt= TP_OrdersByDate::validateDate($dt);   
            if ( !$valdt ) {
               return array(
                       "status" => "failed",
                       "message" => "Date is not in valid format!",
               );
           }
        
            // Step7 : Query
           $result = $wpdb->get_results("SELECT
           mp_ordtem.ID,
           (select child_val from $table_revs where id = (select title from $table_store where id = mp_ord.stid)) AS store,
           (select child_val from $table_revs where id = (select title from $table_products where id = mp_ordtem.pdid)) AS product,
           mp_ordtem.quantity,
           mp_ord.date_created
           FROM
           $table_ordes_items as mp_ordtem
           INNER JOIN $table_orders as mp_ord ON mp_ord.ID = mp_ordtem.odid
           WHERE mp_ord.stid = '$stid' and DATE(mp_ord.date_created) = '$dt'
            ");
            
            // Step8 : Check if no result
            if (!$result)
            {
                return array(
                        "status" => "failed",
                        "message" => "No order found by this value.",
                );
            }
            
            // Step9 : Return Result 
            return array(
                    "status" => "success",
                    "data" => array($result,
                )
            );
            
        }
        
        public static function validateDate($date, $format = 'Y-m-d H:i:s')
        {
            $d = DateTime::createFromFormat($format, $date);
            return $d && $d->format($format) == $date;
        }

    }