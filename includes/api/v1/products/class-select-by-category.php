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

    class TP_Select_Store_Category_Product {

        public static function listen(){
            return rest_ensure_response( 
                TP_Select_Store_Category_Product:: get_prods_by_cat()
            );
        }

        public static function get_prods_by_cat(){
            global $wpdb;
            
            // Step 1 : Verfy if Datavice Plugin is Activated
			if (TP_Globals::verify_datavice_plugin() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Plugin Missing!",
                    )
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
					"message" => "Required fields cannot be empyty.",
                );
            }

            $category_id = $_POST['catid'];

            $tp_revs = TP_REVISION_TABLE;
            $table_product = TP_PRODUCT_TABLE;
            $table_categories = TP_CATEGORIES_TABLE;

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

            if(!$result){
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