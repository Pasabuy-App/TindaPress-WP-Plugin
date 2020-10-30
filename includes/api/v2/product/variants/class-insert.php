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

    class TP_Product_Variants_Insert_v2 {

        //REST API Call
        public static function listen($request){

            return rest_ensure_response(
                self::listen_open($request)
            );
        }

        public static function catch_post(){
            $curl_user = array();

            $curl_user['pdid'] = $_POST["pdid"];
            $curl_user['title'] = $_POST["title"];
            $curl_user['info'] = $_POST["info"];
            $curl_user['price'] = $_POST["price"];
            $curl_user['required'] = $_POST["required"];
            $curl_user['wpid'] = $_POST["wpid"];
            isset($_POST['pid']) && !empty($_POST['pid'])? $curl_user['pid'] =  $_POST['pid'] :  $curl_user['pid'] = null ;

            return $curl_user;
        }

        public static function listen_open($request){

            global $wpdb;
            $tbl_product = TP_PRODUCT_v2;
            $tbl_variants =  TP_PRODUCT_VARIANTS_v2;
            $tbl_variants_filed =  TP_PRODUCT_VARIANTS_FILEDS_v2;

            // Step 1: Check if prerequisites plugin are missing
            $plugin = TP_Globals::verify_prerequisites();
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

            if (!isset($_POST['pdid']) || !isset($_POST['title']) || !isset($_POST['info']) || !isset($_POST['price']) ) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Request unknown!",
                );
            }

            $user = self::catch_post();

            $validate = HP_Globals_v2::check_listener($user);
            if ($validate !== true) {
                return array(
                    "status" => "failed",
                    "message" => "Required fileds cannot be empty "."'".ucfirst($validate)."'"."."
                );
            }

            if ($user["required"] != "true" && $user["required"] != "false") {
                return array(
                    "status" => "failed",
                    "message" => "Invalid value of required.",
                );
            }

            // Check if product exists
                $check_product = $wpdb->get_row("SELECT ID FROM $tbl_product WHERE hsid = '{$user["pdid"]}' ");
                if (empty($check_product)) {
                    return array(
                        "status" => "failed",
                        "message" => "This product does not exists."
                    );
                }
            // End

            // Verify parent id
                if ($user["pid"] != null) {
                    $check_variant = $wpdb->get_row("SELECT ID FROM $tbl_variants WHERE hsid = '{$user["pdid"]}' ");
                    if (empty($check_variant)) {
                        return array(
                            "status" => "failed",
                            "message" => "This product does not exists."
                        );
                    }
                }
            // End

            // Check if variants exists
                $check_variants = $wpdb->get_row("SELECT title FROM $tbl_variants WHERE title LIKE '%{$user["title"]}%' AND `status` = 'active'  ");
                if (!empty($check_variants)) {
                    return array(
                        "status" => "failed",
                        "message" => "This variants already exists. $check_variants->title "
                    );
                }
            // End
            $wpdb->query("START TRANSACTION");
            $import_data = $wpdb->query("INSERT INTO
                $tbl_variants
                    ($tbl_variants_filed)
                VALUES
                    ('{$user["pdid"]}', '{$user["title"]}', '{$user["info"]}', '{$user["price"]}', '{$user["required"]}', '{$user["wpid"]}' ) ");
            $import_data_id = $wpdb->insert_id;

            // Update variant parent column of variant is option
            if ($user["pid"] != null) {
                $update_parent = $wpdb->query("UPDATE $tbl_variants SET parent = '{$user["pid"]}' WHERE ID = '$import_data_id' ");

                if ($update_parent < 1) {
                    return array(
                        "status" => "failed",
                        "message" => "An error occured while submitting data to server."
                    );
                }
            }

            $hsid = TP_Globals_v2::generating_pubkey($import_data_id, $tbl_variants, 'hsid', false, 64);

            if ($import_data < 1 ||  $hsid == false) {
                $wpdb->query("ROLLBACK");
                return array(
                    "status" => "failed",
                    "message" => "An error occured while submitting data to server."
                );
            }else{
                $wpdb->query("COMMIT");
                return array(
                    "status" => "success",
                    "message" => "Data has been added successfully."
                );
            }
        }
    }