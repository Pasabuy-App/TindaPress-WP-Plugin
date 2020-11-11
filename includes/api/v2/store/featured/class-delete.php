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

    class TP_Featured_Store_Delete_v2 {

        //REST API Call
        public static function listen($request){

            return rest_ensure_response(
                self::listen_open($request)
            );
        }

        public static function catch_post(){
            $curl_user = array();

            $curl_user['fstid'] = $_POST["fstid"];
            $curl_user['wpid'] = $_POST["wpid"];

            return $curl_user;
        }

        public static function listen_open($request){

            global $wpdb;
            $tbl_featured_store = TP_FEATURED_STORES_v2;
            $tbl_featured_store_field = TP_FEATURED_STORES_FIELDS_v2;

            $files = $request->get_file_params();

            // Step 1: Check if prerequisites plugin are missing
            $plugin = TP_Globals_v2::verify_prerequisites();
            if ($plugin !== true) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. ".$plugin." plugin missing!",
                );
            }

            // // Step 2: Validate user
            if (DV_Verification::is_verified() == false) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Verification issues!",
                );
            }

            if(!isset($_POST['fstid'])){
                return  array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Request Unknown!",
                );
            }

            $user = self::catch_post();



            // Check if Featured Groups exists
            $get_data = $wpdb->get_row("SELECT * FROM $tbl_featured_store fs WHERE hsid = '{$user["fstid"]}' AND id IN ( SELECT MAX( id ) FROM $tbl_featured_store WHERE  hsid = fs.hsid GROUP BY stid ) ");

            if (empty($check_featured_groups)) {
                return array(
                    "status" => "failed",
                    "message" => "This featured store does not exists. ",
                );
            }
            // End

            // Start mysql transaction
            $wpdb->query("START TRANSACTION");

            $import_data = $wpdb->query("INSERT INTO
                $tbl_featured_store
                    (`hsid`, $tbl_featured_store_field, `status`, `avatar`, `banner`)
                VALUES
                    ('$get_data->hsid', '$get_data->stid', '$get_data->groups', '$get_data->created_by', 'inactive', '$get_data->avatar', '$get_data->banner' )");
            $import_data_id = $wpdb->insert_id;

            if($import_data < 1){
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