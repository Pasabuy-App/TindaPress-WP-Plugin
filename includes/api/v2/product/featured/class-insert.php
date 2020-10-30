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

    class TP_Featured_Products_Insert_v2 {

        //REST API Call
        public static function listen($request){

            return rest_ensure_response(
                self::listen_open($request)
            );
        }

        public static function catch_post(){
            $curl_user = array();

            $curl_user['store_id'] = $_POST["stid"];
            $curl_user['pdid'] = $_POST["pdid"];
            $curl_user['wpid'] = $_POST["wpid"];

            return $curl_user;
        }

        public static function listen_open($request){

            global $wpdb;
            $tbl_store = TP_STORES_v2;
            $tbl_product = TP_PRODUCT_v2;
            $tbl_featured_product = TP_FEATURED_PRODUCT_v2;
            $tbl_featured_product_filed = TP_FEATURED_PRODUCT_FIELDS_v2;
            $files = $request->get_file_params();

            if (!isset($_POST['pdid']) || !isset($_POST['stid']) ) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your admininstrator. Request unknown!"
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


            // Check if store exists
                $check_store = $wpdb->get_row("SELECT ID FROM $tbl_store WHERE hsid = '{$user["store_id"]}' AND `status` = 'active'");
                if (empty($check_store)) {
                    return array(
                        "status" => "failed",
                        "message" => "This store does not exists."
                    );
                }
            // End

            // Check if product exists
                $check_product = $wpdb->get_row("SELECT `status` FROM $tbl_product WHERE hsid = '{$user["pdid"]}' AND `status` = 'active' AND stid = '{$user["store_id"]}' ");
                if (empty($check_product)) {
                    return array(
                        "status" => "failed",
                        "message" => "This product is does not exists.",
                    );
                }
            // End

             // Check if featured product exists
                $check_product_featured = $wpdb->get_row("SELECT `status` FROM $tbl_featured_product WHERE pdid = '{$user["pdid"]}' AND `status` = 'active' AND stid = '{$user["store_id"]}' ");
                if (!empty($check_product_featured)) {
                    return array(
                        "status" => "failed",
                        "message" => "This product is already exists.",
                    );
                }
            // End


            $wpdb->query("START TRANSACTION");

            $import_data = $wpdb->query("INSERT INTO
                $tbl_featured_product
                    ($tbl_featured_product_filed)
                VALUES
                    ('{$user["store_id"]}', '{$user["pdid"]}', '{$user["wpid"]}')");
            $import_data_id = $wpdb->insert_id;

            $hsid = TP_Globals_v2::generating_pubkey($import_data_id, $tbl_featured_product, 'hsid', false, 64);

            // Optional Upload for avatar
            if (isset($files['avatar']) ) {

                if (empty($files['avatar']['name'])) {
                    unset($files['avatar']);
                }

                $image = TP_Globals_v2::upload_image( $request, $files);
                if ($image['status'] != 'success') {
                    return array(
                        "status" => $image['status'],
                        "message" => $image['message']
                    );
                }

                if (!empty($files['avatar']['name'])) {
                    $avatar = $wpdb->query("UPDATE $tbl_featured_product SET `avatar` =  '{$image["data"][0]["avatar"]}' WHERE ID = '$import_data_id' ");
                    if ($avatar < 1) {
                        return array(
                            "status" => "failed",
                            "message" => "An error occured while submitting data to server."
                        );
                    }
                }
            }

            if ($import_data < 1) {
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