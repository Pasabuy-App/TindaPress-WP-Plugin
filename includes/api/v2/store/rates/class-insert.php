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

    class TP_Store__Rates_Insert_v2 {

        //REST API Call
        public static function listen($request){

            return rest_ensure_response(
                self::listen_open($request)
            );
        }

        public static function catch_post(){
            $curl_user = array();
            $curl_user['store_id'] = $_POST['stid'];
            $curl_user['rates'] = $_POST['rates'];
            $curl_user['comments'] = $_POST['comments'];
            $curl_user['rated_by'] = $_POST['wpid'];
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

            if (!isset($_POST['stid']) || !isset($_POST['rates']) || !isset($_POST['comments'])) {
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

            if($user['rates'] > 5){
                return array(
                    "status" => "failed",
                    "message" => "Rates must be  between 1 to 5 only."
                );
            }

            $wpdb->query("START TRANSACTION");

            $check_store = $wpdb->get_row("SELECT ID FROM $tbl_store WHERE hsid = '{$user["store_id"]}' AND `status` = 'active' ");
            if (empty($check_store)) {
                return array(
                    "status" => "failed",
                    "message" => "This mover does not exists"
                );
            }

            $result = $wpdb->query("INSERT INTO $table_ratings ($table_ratings_fields) VALUES ('{$user["store_id"]}', '{$user["rates"]}', '{$user["comments"]}', '{$user["rated_by"]}')  ");
            $result_id = $wpdb->insert_id;

            $wpdb->query("UPDATE $table_ratings SET hsid = sha2($result_id, 256) WHERE ID = '$result_id' ");

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