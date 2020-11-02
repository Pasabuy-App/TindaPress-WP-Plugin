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

    class TP_Product_Update_v2 {

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
            $curl_user['price'] = $_POST["price"];
            $curl_user['discount'] = $_POST["discount"];
            $curl_user['inventory'] = $_POST["inventory"];
            $curl_user['wpid'] = $_POST["wpid"];

            return $curl_user;
        }

        public static function listen_open($request){

            global $wpdb;
            $tbl_product = TP_PRODUCT_v2;

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

            if (!isset($_POST['title']) || !isset($_POST['info']) || !isset($_POST['price']) || !isset($_POST['discount']) || !isset($_POST['inventory']) || !isset($_POST['pdid']) ) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Request unknown!"
                );
            }

            $user = self::catch_post();

            $product_data = $wpdb->get_row("SELECT * FROM $tbl_product WHERE hsid = '{$_POST["pdid"]}' ");

            isset($_POST['title']) && !empty($_POST['title'])? $user['title'] =  $_POST['title'] :  $user['title'] = $product_data->title ;
            isset($_POST['info']) && !empty($_POST['info'])? $user['info'] =  $_POST['info'] :  $user['info'] = $product_data->info ;
            isset($_POST['price']) && !empty($_POST['price'])? $user['price'] =  $_POST['price'] :  $user['price'] = $product_data->price ;
            isset($_POST['discount']) && !empty($_POST['discount'])? $user['discount'] =  $_POST['discount'] :  $user['discount'] = $product_data->discount ;
            isset($_POST['inventory']) && !empty($_POST['inventory'])? $user['inventory'] =  $_POST['inventory'] :  $user['inventory'] = $product_data->inventory ;
            isset($_POST['pcid']) && !empty($_POST['pcid'])? $user['pcid'] =  $_POST['pcid'] :  $user['pcid'] = $product_data->pcid;

            $import_data = $wpdb->query("INSERT INTO
                $tbl_product
                    ($tbl_product_filed)
                VALUES
                    ( '$product_data->stid', '{$user["pcid"]}', '{$user["title"]}', '{$user["info"]}', '{$user["price"]}', '{$user["discount"]}',  '{$user["inventory"]}', '{$user["wpid"]}' ) ");
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