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

    class TP_Products_Ratings_Insert_v2 {

        //REST API Call
        public static function listen(){

            return rest_ensure_response(
                self::listen_open()
            );
        }

        public static function catch_post(){
            $curl_user = array();

            $curl_user['pdid'] = $_POST["pdid"];
            $curl_user['rates'] = $_POST["rates"];
            $curl_user['comments'] = $_POST["comments"];
            $curl_user['wpid'] = $_POST["wpid"];

            return $curl_user;
        }

        public static function listen_open(){

            global $wpdb;
            $tbl_ratings = TP_PRODUCT_RATING_v2;
            $tbl_ratings_field = TP_PRODUCT_RATING_FIELDS_v2;
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

            if (!isset($_POST["pdid"]) || !isset($_POST["comments"]) || !isset($_POST["rates"])) {
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
                $check_product = $wpdb->get_row("SELECT ID FROM $tbl_product WHERE hsid = '{$user["pdid"]}' ");
                if (empty($check_product)) {
                    return array(
                        "status" => "failed",
                        "message" => "This product does not exists."
                    );
                }
            // End

            if($user['rates'] > 5){
                return array(
                    "status" => "failed",
                    "message" => "Rates must be  between 1 to 5 only."
                );
            }

            $wpdb->query("START TRANSACTION");

            $import_data = $wpdb->query("INSERT INTO
                $tbl_ratings
                    ($tbl_ratings_field)
                VALUES
                    ( '{$user["pdid"]}', '{$user["rates"]}', '{$user["comments"]}', '{$user["wpid"]}')");
            $import_data_id = $wpdb->insert_id;

            $wpdb->query("UPDATE $tbl_ratings SET hsid = sha2($import_data_id, 256) WHERE ID = '$import_data_id' ");

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