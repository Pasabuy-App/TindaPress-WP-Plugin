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
            $plugin = TP_Globals::verify_prerequisites();
            if ($plugin !== true) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. ".$plugin." plugin missing!",
                );
            }

            // Step 2: Validate user
            // if (DV_Verification::is_verified() == false) {
            //     return array(
            //         "status" => "unknown",
            //         "message" => "Please contact your administrator. Verification Issues!",
            //     );
            // }

            if (!isset($_POST['title']) || !isset($_POST['info'])  ) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Request unknown!"
                );
            }

            $user = self::catch_post();

            $category_data = $wpdb->get_row("SELECT * FROM $tbl_category  WHERE hsid = '{$_POST["pcid"]}' ");

            isset($_POST['title']) && !empty($_POST['title'])? $user['title'] =  $_POST['title'] :  $user['title'] = $product_data->title ;
            isset($_POST['info']) && !empty($_POST['info'])? $user['info'] =  $_POST['info'] :  $user['info'] = $product_data->info ;
            isset($_POST['status']) && !empty($_POST['status'])? $user['status'] =  $_POST['status'] :  $user['status'] = $product_data->pcid;

            if(isset($_POST['status'])){
                if ($user['status'] != null) {
                    if ($category_data->status == $user['status']) {
                        return array(
                            "status" => "failed",
                            "message" => "Status is already $category_data->status"
                        );
                    }
                }
            }

            $import_data = $wpdb->query("INSERT INTO
                $tbl_category
                    ($tbl_category_filed)
                VALUES
                    #`stid`, `title`, `info`, `created_by`
                    ( '$product_data->stid', '{$user["title"]}', '{$user["info"]}', '{$user["wpid"]}' ) ");
            $import_data_id = $wpdb->insert_id;

            if ($import_data < 1) {
                return array(
                    "status" => "failed",
                    "message" => "An error occured while submitting data."
                );
            }else{
                return array(
                    "status" => "success",
                    "message" => "Data has been added successfully."
                );
            }
        }
    }