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

    class TP_Products_Category_Insert_v2 {

        //REST API Call
        public static function listen(){

            return rest_ensure_response(
                self::listen_open()
            );
        }

        public static function catch_post(){
            $curl_user = array();

            $curl_user['stid'] = $_POST["stid"];
            $curl_user['title'] = $_POST["title"];
            $curl_user['wpid'] = $_POST["wpid"];

            return $curl_user;
        }

        public static function listen_open(){

            global $wpdb;
            $tbl_stores = TP_STORES_v2;
            $tbl_product_category_v2 = TP_PRODUCT_CATEGORY_v2;
            $tbl_product_category_fields_v2 = TP_PRODUCT_CATEGORY_FIELDS_v2;

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

            if (!isset($_POST['title']) || !isset($_POST['stid'])  ) {
                return array(
                    "status" => "unknwon",
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
            isset($_POST['info']) && !empty($_POST['info'])? $user['info'] =  $_POST['info'] :  $user['info'] = null ;

            $check_store = $wpdb->get_row("SELECT `status` FROM $tbl_stores WHERE `hsid` = '{$user["stid"]}' AND `status` = 'active' ");
            if (empty($check_store)) {
                return array(
                    "status" => "failed",
                    "message" => "This Store does not Exist.",
                );
            }

            $check_category = $wpdb->get_row("SELECT `status` FROM $tbl_product_category_v2 WHERE title LIKE '%{$user["title"]}%' AND `status` = 'active' AND stid = '{$user["stid"]}' AND  WHERE
            ID IN ( SELECT MAX( pdd.ID ) FROM $tbl_product_category_v2  pdd WHERE pdd.hsid = hsid GROUP BY hsid )");
            if (!empty($check_category)) {
                return array(
                    "status" => "failed",
                    "message" => "This Store category is already exists.",
                );
            }

           $import_data = $wpdb->query("INSERT INTO
                $tbl_product_category_v2
                    ($tbl_product_category_fields_v2)
                values
                    ('{$user["stid"]}', '{$user["title"]}', '{$user["info"]}', '{$user["wpid"]}' )");
            $import_data_id = $wpdb->insert_id;

            $hsid = TP_Globals_v2::generating_pubkey($import_data_id, $tbl_product_category_v2, 'hsid', false, 64);

            if ($import_data < 1) {
                $wpdb->query("ROLLBACK");
                return array(
                    "status" => "failed",
                    "message" => "An error occured while submitting data to server"
                );
            }else{
                $wpdb->query("COMMIT");
                return array(
                    "status" => "success",
                    "message" => "Data has been added successfully"
                );
            }
        }
    }