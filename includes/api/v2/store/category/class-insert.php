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

    class TP_Store_Category_Insert_v2 {

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
            $curl_user['wpid'] = $_POST["wpid"];
            return $curl_user;
        }

        public static function listen_open($request){

            global $wpdb;
            $table_store_categories = TP_STORES_CATEGORIES_v2;
            $table_store_categories_field = TP_STORES_CATEGORIES_FIELDS_v2;

            if (!isset($_POST['title']) || !isset($_POST['info']) ) {
                return array(
                    "status" => "unknwon",
                    "message" => "Please contact your administrator. Request unknown!",
                );
            }

            $user = self::catch_post();

            $validate = HP_Globals_v2::check_listener($user);
            if ($validate !== true) {
                return array(
                    "status" => "failed",
                    "message" => "Required fileds cannot be empty "."'".ucfirst($validate)."'"."."
                );
            }

            $files = $request->get_file_params();

            $check_category = $wpdb->get_row("SELECT `status` FROM $table_store_categories WHERE title LIKE '%{$user["title"]}%' AND `status` = 'active' ");
            if (!empty($check_category)) {
                return array(
                    "status" => "failed",
                    "message" => "This Store category is already exists.",
                );
            }

            $result = DV_Globals::upload_image( $request, $files ); // upload image

            $import_data = $wpdb->query("INSERT INTO
                $table_store_categories
                    ($table_store_categories_field)
                VALUES
                ( '{$user["title"]}', '{$user["info"]}', '{$result["data"]}', '{$user["wpid"]}') ");
            $import_data_id = $wpdb->insert_id;

            $hsid = TP_Globals_v2::generating_pubkey($import_data_id, $table_store_categories, 'hsid', false, 64);

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