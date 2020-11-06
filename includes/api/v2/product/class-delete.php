<?php
	// Exit if accessed directly
	if ( ! defined( 'ABSPATH' ) )
	{
		exit;
	}

	/**
        * @package tindapress-wp-plugin
        * @version 0.2.0
	*/

    class TP_Product_Delete_v2 {

        //REST API Call
        public static function listen($request){

            return rest_ensure_response(
                self::listen_open($request)
            );
        }

        public static function catch_post(){
            $curl_user = array();

            $curl_user['pdid'] = $_POST["pdid"];
            $curl_user['wpid'] = $_POST["wpid"];

            return $curl_user;
        }

        public static function listen_open($request){

            global $wpdb;
            $tbl_product = TP_PRODUCT_v2;
            $tbl_product_field = TP_PRODUCT_FIELDS_v2;

            // Step 1: Check if prerequisites plugin are missing
            $plugin = TP_Globals_v2::verify_prerequisites();
            if ($plugin !== true) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. ".$plugin." plugin missing!",
                );
            }

            // Step 2: Validate user
            if (DV_Verification::is_verified() == false) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Verification Issues!",
                );
            }

            if (!isset($_POST['pdid']) ) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Request unknown!"
                );
            }

            if (empty($_POST['pdid']) ) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Request unknown!"
                );
            }

            $user = self::catch_post();

            $product_data = $wpdb->get_row("SELECT * FROM $tbl_product WHERE hsid = '{$_POST["pdid"]}' ");

            if ($product_data->status == "inactive") {
                return array(
                    "status" => "failed",
                    "message" => "This product is already in $product_data->status"
                );
            }

            $import_data = $wpdb->query("INSERT INTO
                $tbl_product
                    (`hsid`,$tbl_product_field, `status`)
                VALUES
                    ( '$product_data->hsid', '$product_data->stid', '$product_data->pcid', '$product_data->title', '$product_data->info', '$product_data->price', '$product_data->discount',  '$product_data->inventory', '{$user["wpid"]}', 'inactive' ) ");
            $import_data_id = $wpdb->insert_id;

            if ($import_data < 1) {
                return array(
                    "status" => "failed",
                    "message" => "An error occured while submitting data."
                );
            }else{
                return array(
                    "status" => "success",
                    "message" => "Data has been deleted successfully."
                );
            }
        }
    }