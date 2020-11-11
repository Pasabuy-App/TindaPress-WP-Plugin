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

    class TP_Products_Ratings_Delete_v2 {

        //REST API Call
        public static function listen(){

            return rest_ensure_response(
                self::listen_open()
            );
        }

        public static function catch_post(){
            $curl_user = array();

            $curl_user['rtid'] = $_POST["rtid"];
            $curl_user['wpid'] = $_POST["wpid"];

            return $curl_user;
        }

        public static function listen_open(){

            global $wpdb;
            $tbl_ratings = TP_PRODUCT_RATING_v2;
            $tbl_ratings_field = TP_PRODUCT_RATING_FIELDS_v2;


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

            if (!isset($_POST["rtid"])) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Request unknown",
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

            // Check if product exists
                $get_rating = $wpdb->get_row("SELECT * FROM $tbl_ratings WHERE hsid = '{$user["rtid"]}' AND ID IN ( SELECT MAX( ID ) FROM $tbl_ratings r WHERE r.hsid = hsid GROUP BY hsid ) ");
                if (empty($get_rating)) {
                    return array(
                        "status" => "failed",
                        "message" => "This rate does not exists."
                    );
                }
            // End

            $wpdb->query("START TRANSACTION");

            $import_data = $wpdb->query("INSERT INTO
                $tbl_ratings
                    (`hsid`, $tbl_ratings_field, `status`)
                VALUES
                    ( '$get_rating->hsid', '$get_rating->pdid', '$get_rating->rates', '$get_rating->comments', '{$user["wpid"]}', 'inactive' )");

            if ($import_data < 1) {
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