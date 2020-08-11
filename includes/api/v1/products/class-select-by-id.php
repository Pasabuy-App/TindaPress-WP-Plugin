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

    class TP_Product_Select {

        //REST API Call
        public static function listen(){
            
            return rest_ensure_response( 
                TP_Product_Select:: select_by_id_product()
            );
        }

        public static function select_by_id_product(){
            global $wpdb;

            $product_table     = TP_PRODUCT_TABLE;
            $table_revs        = TP_REVISIONS_TABLE;
            $table_store        = TP_STORES_TABLE;
            $table_category       = TP_CATEGORIES_TABLE;

            // Step 1: Check if prerequisites plugin are missing
            $plugin = TP_Globals::verify_prerequisites();
            if ($plugin !== true) {
                return array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. ".$plugin." plugin missing!",
                );
            }

            // Step2 : Validate if user is exist
			if (DV_Verification::is_verified() == false) {
                return array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Verification issues!",
                );
            }

            // Step3 : Sanitize all Request
			if (!isset($_POST['pid']) ) {
				return array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Request unknown!",
                );
                
            }

            // Step4 : Sanitize all Request
			if ( empty($_POST['pid']) ) {
				return array(
						"status" => "unknown",
						"message" => "Required fields cannot be empty!",
                );
            }

            // Step4 : Sanitize all Request
			if ( !is_numeric($_POST['pid']) ) {
				return array(
						"status" => "unknown",
						"message" => "Please contact your administrator. ID not in valid format!",
                );
            }

            $pdid = $_POST['pid'];

            $get_product = $wpdb->get_row("SELECT `status` FROM $product_table WHERE ID = $pdid  ");

            if ( empty($get_product)  ) {
                return array(
                    "status" => "failed",
                    "message" => "This product does not exists.",
                );
            }

            $result = $wpdb->get_row("SELECT
                tp_prod.ID,
                ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_str.title ) AS `store_name`,
                ( SELECT tp_cat.types     FROM $table_category tp_cat WHERE ID = tp_prod.ctid ) AS `category`,
                ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.title ) AS `title`,
                ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.preview ) AS `preview`,
                ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.short_info ) AS `short_info`,
                ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.long_info ) AS `long_info`,
                ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.`status` ) AS `status`,
                ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.sku ) AS `sku`,
                ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.price ) AS `price`,
                ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.weight ) AS `weight`,
                ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.dimension ) AS `dimension`
            FROM
                $product_table tp_prod
                INNER JOIN $table_revs tp_rev ON tp_rev.ID = tp_prod.title
                INNER JOIN $table_store tp_str ON tp_str.ID = tp_prod.stid 
            WHERE
                tp_prod.ID = $pdid
            GROUP BY
                tp_prod.ID");

            if (!$result ) {
                return array(
                    "status" => "failed",
                    "message" => "No product found."
                );
            }else{
                return array(
                    "status" => "success",
                    "data" => $result
                );
            }
        }
    }