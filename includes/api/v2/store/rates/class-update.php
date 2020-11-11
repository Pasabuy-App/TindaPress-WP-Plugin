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

    class TP_Store_Rates_Update_v2 {

        //REST API Call
        public static function listen($request){
            return rest_ensure_response(
                self::listen_open($request)
            );
        }

        public static function catch_post(){
            $curl_user = array();

            $curl_user['rate_id'] = $_POST['rtid'];
            $curl_user['wpid'] = $_POST['wpid'];

            return $curl_user;
        }

        public static function listen_open(){

			// Initialize WP global variable
            global $wpdb;

            $table_ratings = TP_STORES_RATINGS_v2;
            $table_ratings_fields = TP_STORES_RATINGS_FIELDS_v2;
            $tbl_store = TP_STORES_v2;

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
                    "message" => "Please contact your administrator. Verification issues!",
                );
            }

            if (!isset($_POST['rtid'])) {
                return  array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Request Unknown!",
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

            $get_data = $wpdb->get_row("SELECT * FROM $table_ratings r WHERE hsid = '{$user["rtid"]}'  WHERE  id IN ( SELECT MAX( id ) FROM $table_ratings WHERE r.hsid = hsid  GROUP BY title )  ");

            if (empty($get_data)) {
                return array(
                    "status" => "failed",
                    "message" => "This rate does not exists."
                );
            }

            isset($_POST['comments']) && !empty($_POST['comments'])? $user['comments'] =  $_POST['comments'] :  $user['comments'] = $check_store->comments;
            isset($_POST['rates']) && !empty($_POST['rates'])? $user['rates'] =  $_POST['rates'] :  $user['rates'] = null ;

            if($user['rates'] != null){
                if ($user['rates'] > 5) {
                    return array(
                        "status" => "failed",
                        "message" => "Rates must be  between 1 to 5 only."
                    );
                }

            }else{
                $user["rates"] = $check_store->rates;

            }

            $wpdb->query("START TRANSACTION");

            $result = $wpdb->query("INSERT INTO $table_ratings (`hsid`, $table_ratings_fields, `status`) VALUES ( '$get_data->hsid', '$get_data->stid', '{$user["rates"]}', '{$user["comments"]}', '$get_data->rated_by', '$get_data->status' )  ");
            $result_id = $wpdb->insert_id;

            if ($result < 1 ) {
                $wpdb->query("ROLLBACK");
                return array(
                    "status" => "failed",
                    "message" => "An error occurred while submitting data to server."
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