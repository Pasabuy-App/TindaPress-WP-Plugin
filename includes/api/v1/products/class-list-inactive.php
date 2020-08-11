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

    class TP_Product_List_Inactive {

        public static function listen(){
            return rest_ensure_response( 
                TP_Product_List_Inactive:: list_inactive()
            );
        }

        public static function list_inactive(){
            
            global $wpdb;
            // Variables for Table
            $table_revs = TP_REVISIONS_TABLE;
            $table_product = TP_PRODUCT_TABLE;
            $table_categories = TP_CATEGORIES_TABLE;
            
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

            
            // Step 3: Start mysql query
            $result = $wpdb->get_results("SELECT
                tp_prod.ID,
                ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = c.title ) AS `category_name`,
                ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE tp_rev.ID = tp_prod.title ) AS product_name,
                ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.short_info ) AS `short_info`,
                ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.long_info ) AS `long_info`,
                ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.sku ) AS `sku`,
                ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.price ) AS `price`,
                ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.weight ) AS `weight`,
                ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.dimension ) AS `dimension`
            FROM
                $table_product tp_prod
            INNER JOIN 
                $table_revs tp_rev ON tp_rev.ID = tp_prod.title
            INNER JOIN
                $table_categories c ON c.ID = tp_prod.ctid
            WHERE
                ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.status ) = 0
            GROUP BY
                tp_prod.ID ");
            
            // Step 4: Check if 0 rows found
            if(!$result){

                return array(
                        "status" => "failed",
                        "message" => "No results found.",
                );
                
            // Step 5: Return a success status and complete object
            }else{

                return array(
                    "status" => "success",
                    "data" => $result
                );
            }
        }

    }