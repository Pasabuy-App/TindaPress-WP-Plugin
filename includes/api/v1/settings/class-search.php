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

    class TP_Search {

        public static function listen(){
            return rest_ensure_response(
                self::list_open()
            );
        }

        public static function list_open(){
            global $wpdb;
            $tbl_store = TP_STORES_VIEW;
            $tbl_product = TP_PRODUCTS_VIEW;

            // Step1 : Check if prerequisites plugin are missing
            $plugin = TP_Globals::verify_prerequisites();
            if ($plugin !== true) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. ".$plugin." plugin missing!",
                );
            }

            // Step2 : Check if wpid and snky is valid
            if (DV_Verification::is_verified() == false) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Verification Issues!",
                );
            }

            if (!isset($_POST['search'])) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Request unknown!",
                );
            }


            $search = $_POST['search'];

            $store_query = $wpdb->get_results(
                "SELECT
                    ID,
                    title,
                    city
                FROM
                    $tbl_store sv
                WHERE
                    title LIKE '%$search%' OR city LIKE '%$search%'");

            $product_query = $wpdb->get_results(
                "SELECT
                    ID,
                    stid,
                    catid,
                    product_name
                FROM
                    $tbl_product
                WHERE
                    product_name LIKE '%$search%' OR cat_name LIKE '%$search%'   ");

            return array(
                "status" => "success",
                "data" => array_merge($store_query, $product_query)
            );
        }
    }