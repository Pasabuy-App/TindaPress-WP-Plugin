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

    class TP_Store_Delete_v2 {

        //REST API Call
        public static function listen($request){

            return rest_ensure_response(
                self::listen_open($request)
            );
        }

        public static function catch_post(){
            $curl_user = array();

            $curl_user['stid'] = $_POST["stid"];
            $curl_user['wpid'] = $_POST["wpid"];

            return $curl_user;
        }

        public static function listen_open($request){

            global $wpdb;
            $tbl_store_v2 = TP_STORES_v2;
            $tbl_store__field_v2 = TP_STORES_FIELDS_v2;

            $files = $request->get_file_params();

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


            $user = self::catch_post();


            $check_store = $wpdb->get_row("SELECT * FROM $tbl_store_v2 WHERE hsid = '{$user["stid"]}' AND `status` = 'active' AND id IN ( SELECT MAX( p.id ) FROM $tbl_store_v2  p WHERE p.hsid = hsid GROUP BY hsid ) ");
            if (empty($check_store)) {
                return array(
                    "status" => "failed",
                    "message" => "This store does not exists.",
                );
            }

            $wpdb->query("START TRANSACTION");

            $import_data = $wpdb->query("INSERT INTO
                $tbl_store_v2
                    ( `avatar`, `banner`, `hsid`, $tbl_store__field_v2, `status`, `commision`)
                VALUES
                    ('$check_store->avatar', '$check_store->banner', '$check_store->hsid', '$check_store->scid', '$check_store->title', '$check_store->info', '$check_store->adid', '{$user["wpid"]}', 'inactive', '$check_store->commision' ) ");
            $import_data_id = $wpdb->insert_id;

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
                    "message" => "Data has been updated successfully.",
                    "data" => $check_store->hsid
                );
            }
        }
    }