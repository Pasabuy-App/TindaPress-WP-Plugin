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

    class TP_Store_Doc_type_Insert_v2 {

        //REST API Call
        public static function listen($request){

            return rest_ensure_response(
                self::listen_open($request)
            );
        }

        public static function catch_post(){
            $curl_user = array();
            $curl_user['title'] = $_POST["title"];
            $curl_user['info'] = isset($_POST["info"]) ? $_POST["info"] : "";
            $curl_user['wpid'] = $_POST["wpid"];
            return $curl_user;
        }

        public static function listen_open($request){

            global $wpdb;
            $tbl_store_doc_types = TP_STORES_DOCS_TYPES_v2;
            $tbl_store_doc_types_field = TP_STORES_DOCS_TYPES_FIELDS_v2;

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

            if (!isset($_POST['title']) || !isset($_POST['info']) ) {
                return array(
                    "status" => "unknwon",
                    "message" => "Please contact your administrator. Request unknown!",
                );
            }

            $user = self::catch_post();

            $check_category = $wpdb->get_row("SELECT `status` FROM $tbl_store_doc_types WHERE title LIKE '%{$user["title"]}%' AND `status` = 'active' ");
            if (!empty($check_category)) {
                return array(
                    "status" => "failed",
                    "message" => "This Store category is already exists.",
                );
            }

            $import_data = $wpdb->query("INSERT INTO
                $tbl_store_doc_types
                    ($tbl_store_doc_types_field)
                VALUES
                    ( '{$user["title"]}', '{$user["info"]}',  '{$user["wpid"]}') ");
            $import_data_id = $wpdb->insert_id;

            $hsid = TP_Globals_v2::generating_pubkey($import_data_id, $tbl_store_doc_types, 'hsid', false, 64);

            if ($import_data < 1) {
                $wpdb->query("ROLLBACK");
                return array(
                    "status" => "failed",
                    "message" => "An error occured while submitting data to server"
                );

            }else{
                $wpdb->query("ROLLBACK");
                return array(
                    "status" => "success",
                    "message" => "Data has been added successfully"
                );
            }
        }
    }