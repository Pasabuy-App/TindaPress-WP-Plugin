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
    class TP_List_Date {
        
        public static function listen(){
            return rest_ensure_response( 
                TP_List_Date:: list_orders_date()
            );
        }
        
        public static function list_orders_date(){
            
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
                        "message" => "Please contact your administrator. Verification Issues!",
                );
                
            }

            if (!isset($_POST["start"]) || !isset($_POST["end"])) {
                return array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Request unknown!",
                );
            }

            // Step4 : sanitize if all variables is empty
            if (empty($_POST["start"]) || empty($_POST["end"])) {
                return array(
                        "status" => "failed",
                        "message" => "Required fields cannot be empty.",
                );
            }

            //Using global function, convert date to user specific timezone
            $start = TP_Globals::convert_date($_POST["wpid"],$_POST["start"]);
            $end = TP_Globals::convert_date($_POST["wpid"],$_POST["end"]);


            
            if ( TP_List_Date::validateDate($start) == false || TP_List_Date::validateDate($end) == false ) {
                return array(
                    "status" => "failed",
                    "message" => "Date not in valid format",
                );
            }
            
            //get current date base on user timezone

            $table_revs = TP_REVISION_TABLE;
            $table_categories = TP_CATEGORIES_TABLE;

            //Get results
            $list_date = $wpdb->get_results("SELECT o.date_created as date, st.ID, ops.id as operation_id, o.id as order_id,
            ( SELECT rev.child_val FROM $table_revs rev WHERE ID = st.title ) AS `store_name`,
            ( SELECT rev.child_val FROM $table_revs rev WHERE ID = p.title ) AS `product_name`,
            ( SELECT rev.child_val FROM $table_revs rev WHERE ID = p.price ) AS `product_price`,
            oi.quantity as quantity
            FROM
                mp_orders o
            INNER JOIN
                mp_operations ops ON ops.id = o.opid
            INNER JOIN
                tp_stores st ON st.id = ops.stid
            INNER JOIN 
                tp_revisions rev ON rev.ID = st.`status` 
            INNER JOIN
                mp_order_items oi ON oi.odid = o.id
            INNER JOIN
                tp_products p ON p.id = oi.pdid
            WHERE 
                rev.child_val = 1
            AND
                o.date_created BETWEEN '$start' AND '$end'");

            if ( !$list_date) {
                return array(
                    "status" => "failed",
                    "message" => "No results found.",
                );
            }

            return array(
                "status" => "success",
                "data" => $list_date
            );

        
        }

        public static function validateDate($date, $format = 'Y-m-d H:i:s'){
            $d = DateTime::createFromFormat($format, $date);
            return $d && $d->format($format) == $date;
        }

    }