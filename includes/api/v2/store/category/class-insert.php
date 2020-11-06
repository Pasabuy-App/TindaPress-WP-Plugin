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
            $curl_user['info'] = isset($_POST['info']) ? $_POST['info'] : "";
            $curl_user['wpid'] = $_POST["wpid"];
            return $curl_user;
        }

        public static function listen_open($request){

            global $wpdb;
            $table_store_categories = TP_STORES_CATEGORIES_v2;
            $table_store_categories_field = TP_STORES_CATEGORIES_FIELDS_v2;
            $table_store_categories_groups = TP_STORES_CATEGORY_GROUPS_v2;
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
                    "message" => "Please contact your administrator. Verification Issues!",
                );
            }

            if (!isset($_POST['title']) ) {
                return array(
                    "status" => "unknwon",
                    "message" => "Please contact your administrator. Request unknown!",
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

            if (empty($files['img']['name'])) {
                return array(
                    "status" => "failed",
                    "message" => "Category avatar is required"
                );
            }

            isset($_POST['groups']) && !empty($_POST['groups'])? $user['groups'] =  $_POST['groups'] :  $user['groups'] = null ;

            // Check of category group exits
               if ($user['groups'] != null) {
                    $check_category_groups = $wpdb->get_row("SELECT `status` FROM $table_store_categories_groups WHERE hsid = '{$user["groups"]}' AND `status` = 'active' ");
                    if (empty($check_category_groups)) {
                        return array(
                            "status" => "failed",
                            "message" => "This Store category group does not exists.",
                        );
                    }
               }
            // End

            $check_category = $wpdb->get_row("SELECT `status` FROM $table_store_categories WHERE title LIKE '%{$user["title"]}%' AND `status` = 'active' AND groups = '{$user["groups"]}' ");
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
                #       `title`, `info`, `groups` `avatar`, `created_by`
                ( '{$user["title"]}', '{$user["info"]}', '{$user["groups"]}',  '{$result["data"]}', '{$user["wpid"]}' ) ");
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