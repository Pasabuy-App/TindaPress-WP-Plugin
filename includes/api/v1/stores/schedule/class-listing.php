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
    class TP_Store_Schedule_Listing {

        public static function listen(){
            return rest_ensure_response(
                self:: list_open()
            );
        }

        public static function list_open(){

            // 2nd Initial QA 2020-08-24 10:57 PM - Miguel
            global $wpdb;
            $table_schedule = TP_SCHEDULE;

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

            $stid = $_POST['stid'];

            $data = $wpdb->get_results("SELECT * FROM $table_schedule WHERE stid = '$stid' ");

            return array(
                "status" => "success",
                "data" => $data
            );
        }
    }