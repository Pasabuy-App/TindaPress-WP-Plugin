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

    class TP_OrdersByStage {
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
            
           
            // Step2 : Check if wpid and snky is valid
            if (DV_Verification::is_verified() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Verification Issues!",
                    )
                );
            }
            

            if (!isset($_POST["stages"])) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Request unknown!",
                    )
                );
            }
            

            if (empty($_POST["stages"])) {
                return rest_ensure_response( 
                    array(
                        "status" => "failed",
                        "message" => "Required fields cannot be empty.",
                    )
                );
            }


            // variables for query
            $table_store = TP_STORES_TABLE;
            $table_products = TP_PRODUCT_TABLE;
            $table_revs = TP_REVISION_TABLE;
            $table_orders = MP_ORDERS_TABLE;
            $table_ordes_items = MP_ORDER_ITEMS_TABLE;
            $stages = $_POST['stages'];
            
     
            
           $result = $wpdb->get_results("SELECT
           mp_ordtem.ID,
           (select child_val from $table_revs where id = (select title from $table_store where id = mp_ord.stid)) AS store,
           (select child_val from $table_revs where id = (select title from $table_products where id = mp_ordtem.pdid)) AS orders,
           mp_ordtem.quantity as qty,
           mp_ord.date_created as date_ordered
           FROM
           $table_ordes_items as mp_ordtem
           INNER JOIN $table_orders as mp_ord ON mp_ord.ID = mp_ordtem.odid
           WHERE mp_ord.`status` = '$stages'
           #GROUP BY mp_ordtem.ID
            ");

            if (!$result ) {
                return array(
                    "status" => "failed",
                    "message" => "An error occured while fetching data to database.",
                );
            }else{
                return  array(
                    "status" => "success",
                    "data" => array(
                        'list' => $result, 
                        
                    )
                );
            }
            
        }

    }