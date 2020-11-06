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

    class TP_Product_Category_Update_v2 {

        //REST API Call
        public static function listen($request){

            return rest_ensure_response(
                self::listen_open($request)
            );
        }

        public static function catch_post(){
            $curl_user = array();

            $curl_user['pcid'] = $_POST["pcid"];
            $curl_user['title'] = $_POST["title"];
            $curl_user['info'] = $_POST["info"];
            $curl_user['wpid'] = $_POST["wpid"];

            return $curl_user;
        }

        public static function listen_open($request){

            global $wpdb;
            $tbl_category = TP_PRODUCT_CATEGORY_v2;
            $tbl_category_filed = TP_PRODUCT_CATEGORY_FIELDS_v2;

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

            $user = self::catch_post();
            $category_data = $wpdb->get_row("SELECT * FROM $tbl_category WHERE  hsid = '{$user["pcid"]}' AND id IN ( SELECT MAX( id ) FROM $tbl_category  ct WHERE ct.hsid = hsid GROUP BY hsid )   ");

            if (empty($category_data)) {
                return array(
                    "status" => "failed",
                    "message" => "This product category does not exists!"
                );
            }

            isset($_POST['title']) && !empty($_POST['title'])? $user['title'] =  $_POST['title'] :  $user['title'] = $category_data->title ;
            isset($_POST['info']) && !empty($_POST['info'])? $user['info'] =  $_POST['info'] :  $user['info'] = $category_data->info ;
            isset($_POST['status']) && !empty($_POST['status'])? $user['status'] =  $_POST['status'] :  $user['status'] = $category_data->status;

            if(isset($_POST['status'])  ){
                if (!empty($_POST['status'])) {
                    if ($user['status'] != null) {
                        if ($category_data->status == $user['status']) {
                            return array(
                                "status" => "failed",
                                "message" => "Status is already $category_data->status"
                            );
                        }
                    }
                }
            }

            $import_data = $wpdb->query("INSERT INTO
                $tbl_category
                    (`hsid`, $tbl_category_filed, `status`)
                VALUES
                    ('$category_data->hsid', '$category_data->stid', '{$user["title"]}', '{$user["info"]}', '{$user["wpid"]}', '{$user["status"]}') ");
            $import_data_id = $wpdb->insert_id;

            if ($import_data < 1) {
                return array(
                    "status" => "failed",
                    "message" => "An error occured while submitting data."
                );
            }else{

                return array(
                    "status" => "success",
                    "message" => "Data has been updated successfully."
                );
            }
        }
    }