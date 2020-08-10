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

    class TP_Select_By_StoreId_Products {

        public static function listen(){
            return rest_ensure_response( 
                TP_Select_By_StoreId_Products:: get_products_by_storeid()
            );
        }

        public static function get_products_by_storeid(){
            
            global $wpdb;

            // Variables for Tables
            $tp_revs = TP_REVISIONS_TABLE;
            $table_product = TP_PRODUCT_TABLE;
            $table_categories = TP_CATEGORIES_TABLE;
            
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
                        "message" => "Please contact your administrator. Verification issues!",
                );
            }

            // Step3 : Sanitize all Request
			if (!isset($_POST['stid']) ) {
				return array(
					"status" => "unknown",
					"message" => "Please contact your administrator. Request unknown!",
                );
                
            }
            
            // Step6 : Sanitize all Request if emply
			if (empty($_POST['stid']) ) {
				return array(
					"status" => "unknown",
					"message" => "Required fields cannot be empyty.",
                );
            }

            $stid = $_POST['stid'];

            $result = $wpdb->get_results("SELECT
                tp_prod.ID,
                ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = ( SELECT title FROM $table_categories WHERE ID = tp_prod.ctid ) ) AS category_title,
                ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.title ) AS `title`,
                ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.preview ) AS `preview`,
                ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.short_info ) AS `short_info`,
                ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.long_info ) AS `long_info`,
                ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.`status` ) AS `status`,
                ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.sku ) AS `sku`,
                ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.price ) AS `price`,
                ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.weight ) AS `weight`,
                ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.dimension ) AS `dimension`,
                ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.short_info ) AS `short_info` 
            FROM
                $table_product tp_prod
                INNER JOIN $tp_revs tp_rev ON tp_rev.ID = tp_prod.title 
            WHERE
                tp_prod.stid = $stid
            GROUP BY
                tp_prod.ID ");

            // Return results
            if(empty($result)){

                return array(
                        "status" => "failed",
                        "message" => "No results found.",
                );
                
            }else{

                return array(
                    "status" => "success",
                    "data" => $result
                );
            }

        }

    }