<?php
	// Exit if accessed directly
	if ( ! defined( 'ABSPATH' ) )
	{
		exit;
	}

	/**
        * @package tindapress-wp-plugin
        * @version 0.1.0
	*/

    class TP_Activate_Personnel {

        //REST API Call
        public static function listen(){

            return rest_ensure_response(
                self::listen_open()
            );
        }

        public static function catch_post(){
            $curl_user = array();
            $curl_user['user_id'] = $_POST['user_id'];
            $curl_user['created_by'] = $_POST['wpid'];
            return $curl_user;
        }

        //QA Done 2020-08-12 4:10 pm
        public static function listen_open(){
            global $wpdb;

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
                    "message" => "Please contact your administrator. verification issues!",
                );

            }

            if (!isset($_POST['user_id'])) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Request unknown!",
                );
            }
            
            $check_personnel = $wpdb->get_results("SELECT * FROM tp_personnels WHERE wpid = '{$user["user_id"]}'");

            if (!$check_personnel) {
                return array(
                    "status" => "failed",
                    "message" => "This personnel does not exists."
                );
            }

            if ($check_personnel->status == 'active') {
                return array(
                    "status" => "failed",
                    "message" => "This personnel currently active."
                );
            }

            $user = self::catch_post();
            $update = $wpdb->query("UPDATE tp_personnels SET `status` = 'active' WHERE wpid = {$user["user_id"]}");

            if ($update == false) {
                return array(
                    "status" => "failed",
                    "message" => "An error occured while submitting data to server."
                );
            }else{
                return array(
                    "status" => "success",
                    "message" => "Data has been added successfully."
                );
            }
        }
    }