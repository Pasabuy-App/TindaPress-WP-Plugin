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

    class TP_Product_Select_Store {

        public static function listen(){
            return rest_ensure_response( 
                TP_Product_Select_Store:: list_by_store()
            );
        }

        public static function list_by_store(){
            
            global $wpdb;
            // Variables for Table
            $table_revs = TP_REVISIONS_TABLE;
            $table_product = TP_PRODUCT_TABLE;
            $table_categories = TP_CATEGORIES_TABLE;
            $table_stores = TP_STORES_TABLE;
            
            // Step 1: Check if prerequisites plugin are missing
            $plugin = TP_Globals::verify_prerequisites();
            if ($plugin !== true) {

                return array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. ".$plugin." plugin missing!",
                );
            }

			//  Step 2: Validate user
			if (DV_Verification::is_verified() == false) {
                return array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Verification Issues!",
                );
                
            }

            // Step 3: Validate request
            if (!isset($_POST["stid"])) {
                return array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Request unknown!",
                );
            }

            // Step 4: Check if parameters passed is empty
            if ( empty($_POST["stid"]) ){
                return array(
                        "status" => "failed",
                        "message" => "Required fields cannot be empty.",
                );
            }


            $store_id = $_POST['stid'];

            // Step 5: Check is store exists
             $get_store = $wpdb->get_row("SELECT
                    tp_prod.ID, tp_prod.ctid, tp_prod.status as status_id,
                    ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE tp_rev.ID = tp_prod.title ) AS product_name,
                    ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.status ) AS `status`
                FROM
                    $table_product tp_prod
                INNER JOIN 
                    $table_revs tp_rev ON tp_rev.ID = tp_prod.title
                WHERE
                    tp_prod.stid = $store_id
                GROUP BY
                    tp_prod.ID
            ");  
            
            if ( !$get_store ) {
                return array(
                        "status" => "failed",
                        "message" => "This store does not exists.",
                );
            }
            
            if ( $get_store->status == 0 ) {
                return array(
                        "status" => "failed",
                        "message" => "This store is currently inactive.",
                );
			}

            // Step 6: Start mysql query
            $result = $wpdb->get_results("SELECT
                tp_prod.ID,
                tp_prod.ctid as catid,
                tp_prod.stid,
                ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = s.title ) AS `store_name`,
                ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = c.title ) AS `category_name`,
                ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE tp_rev.ID = tp_prod.title ) AS product_name,
                ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.short_info ) AS `short_info`,
                ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.long_info ) AS `long_info`,
                ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.sku ) AS `sku`,
                ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.price ) AS `price`,
                ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.weight ) AS `weight`,
                ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.dimension ) AS `dimension`,
                IF (( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.status ) = 1, 'Active' , 'Inactive' ) AS `status`
            FROM
                $table_product tp_prod
            INNER JOIN 
                $table_revs tp_rev ON tp_rev.ID = tp_prod.title
            INNER JOIN
                $table_stores s ON s.ID = tp_prod.stid
            INNER JOIN
                $table_categories c ON c.ID = tp_prod.ctid
            WHERE
                s.id = $store_id
            GROUP BY
                tp_prod.ID ");
            
            // Step 7: Check if 0 rows found
            if(!$result){

                return array(
                        "status" => "failed",
                        "message" => "No results found.",
                );
                
            // Step 8: Return a success status and complete object
            }else{

                return array(
                    "status" => "success",
                    "data" => $result
                );
            }
        }

    }