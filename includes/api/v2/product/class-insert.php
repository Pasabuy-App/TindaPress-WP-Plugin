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

    class TP_Product_Insert_v2 {

        //REST API Call
        public static function listen($request){

            return rest_ensure_response(
                self::listen_open($request)
            );
        }

        public static function catch_post(){
            $curl_user = array();

            $curl_user['store_id'] = $_POST["stid"];
            $curl_user['price'] = $_POST["price"];
            $curl_user['pcid'] = $_POST["pcid"];
            $curl_user['title'] = $_POST["title"];
            $curl_user['info'] = $_POST["info"];
            $curl_user['discount'] = $_POST["discount"];
            $curl_user['inventory'] = $_POST["inventory"];
            $curl_user['wpid'] = $_POST["wpid"];

            return $curl_user;
        }

        public static function listen_open($request){

            global $wpdb;
            $tbl_store = TP_STORES_v2;
            $tbl_product = TP_PRODUCT_v2;
            $tbl_product_category = TP_PRODUCT_CATEGORY_v2;
            $tbl_product_filed = TP_PRODUCT_FIELDS_v2;

            $files = $request->get_file_params();

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

            if (!isset($_POST['stid']) || !isset($_POST['title']) || !isset($_POST['info']) || !isset($_POST['pcid'])
                || !isset($_POST['discount']) || !isset($_POST['price']) ) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Request unknown"
                );
            }

            $user = self::catch_post();

            $validate = TP_Globals_v2::check_listener($user);
            if ($validate !== true) {
                return array(
                    "status" => "failed",
                    "message" => "Required fileds cannot be empty "."'".ucfirst($validate)."'"."."
                );
            }

            if ($_POST['inventory'] != "true" && $_POST['inventory'] != "false") {
                return array(
                    "status" => "failed",
                    "message" => "Invalid value of inventory."
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

            // Check if product category exists
                $check_product_category = $wpdb->get_row("SELECT ID FROM $tbl_product_category WHERE hsid = '{$user["pcid"]}' AND `status` = 'active'");
                if (empty($check_product_category)) {
                    return array(
                        "status" => "failed",
                        "message" => "This product category does not exists."
                    );
                }
            // End

            // Check if product exists
                $check_product = $wpdb->get_row("SELECT `status` FROM $tbl_product WHERE title LIKE '%{$user["title"]}%' AND `status` = 'active' AND stid = '{$user["store_id"]}' ");
                if (!empty($check_product)) {
                    return array(
                        "status" => "failed",
                        "message" => "This product is already exists.",
                    );
                }
            // End

            $import_data = $wpdb->query("INSERT INTO
                $tbl_product
                    ($tbl_product_filed)
                VALUES
                    ( '{$user["store_id"]}', '{$user["pcid"]}', '{$user["title"]}', '{$user["info"]}', '{$user["price"]}', '{$user["discount"]}',  '{$user["inventory"]}', '{$user["wpid"]}' ) ");
            $import_data_id = $wpdb->insert_id;

            // Optional Upload for avatar and banner
            if (isset($files['avatar']) || isset($files['banner'])) {

                if (empty($files['banner']['name'])) {
                    unset($files['banner']);
                }

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
                    $avatar = $wpdb->query("UPDATE $tbl_product SET `avatar` =  '{$image["data"][0]["avatar_id"]}' WHERE ID = '$import_data_id' ");
                    if ($avatar < 1) {
                        return array(
                            "status" => "failed",
                            "message" => "An error occured while submitting data to server."
                        );
                    }
                }

                if (!empty($files['banner']['name'])) {
                    $banner = $wpdb->query("UPDATE $tbl_product SET `banner` =  '{$image["data"][0]["banner_id"]}' WHERE ID = '$import_data_id' ");
                    if ($banner < 1) {
                        return array(
                            "status" => "failed",
                            "message" => "An error occured while submitting data to server."
                        );
                    }
                }
            }

            $hsid = TP_Globals_v2::generating_pubkey($import_data_id, $tbl_product, 'hsid', false, 64);

            if ($import_data < 1) {
                $wpdb->query("ROLLBACK");
                return array(
                    "status" => "failed",
                    "message" => "An error occured while submitting data to server."
                );
            }else{
                $wpdb->query("ROLLBACK");
                return array(
                    "status" => "success",
                    "message" => "Data has been added successfully."
                );
            }
        }
    }