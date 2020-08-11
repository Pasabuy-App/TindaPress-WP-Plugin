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

    class TP_Product_Select_Category {

        public static function listen(){
            return rest_ensure_response( 
                TP_Product_Select_Category:: get_prods_by_cat()
            );
        }

        public static function get_prods_by_cat(){

            global $wpdb;
            // Variables for Table
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
                        "message" => "Please contact your administrator. Verification Issues!",
                );
                
            }

            // Step3 : Sanitize all Request
			if (!isset($_POST['catid']) ) {

				return array(
					"status" => "unknown",
					"message" => "Please contact your administrator. Request unknown!",
                );
            }

            // Step6 : Sanitize all Request if emply
			if (empty($_POST['catid']) ) {

				return array(
					"status" => "unknown",
					"message" => "Required fields cannot be empty.",
                );
            }

            if (  !is_numeric($_POST['catid'])  ) {
                return array(
					"status" => "unknown",
					"message" => "Please contact your administrator. ID is not in valid format!",
                );
            }

            $category_id = $_POST['catid'];
            
            $get_category = $wpdb->get_row("SELECT `ID` FROM $table_categories WHERE ID = $category_id  ");

            if ( empty($get_category)  ) {
                return array(
                    "status" => "failed",
                    "message" => "This category does not exists.",
                );
            }
            
            // query
            $result = $wpdb->get_results("SELECT
                tp_prod.ID,
                ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.title ) AS `category_name`,
                ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE tp_rev.ID = tp_prod.title ) AS product_name,
                ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.short_info ) AS `short_info`,
                ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.long_info ) AS `long_info`,
                ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.sku ) AS `sku`,
                ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.price ) AS `price`,
                ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.weight ) AS `weight`,
                ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.dimension ) AS `dimension`
            FROM
                $table_product tp_prod
            INNER JOIN 
                $tp_revs tp_rev ON tp_rev.ID = tp_prod.title 
            WHERE
                tp_prod.ctid = $category_id
            GROUP BY
                tp_prod.ID ");
            // Return results 
            if(!$result){

                return array(
                    "status" => "failed",
                    "message" => "No product found.",
                );

            }else{

                return array(
                    "status" => "success",
                    "data" => $result
                );
            }
        }

    }