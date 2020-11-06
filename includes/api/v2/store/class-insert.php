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

    class TP_Store_Insert_v2 {

        //REST API Call
        public static function listen($request){

            return rest_ensure_response(
                self::listen_open($request)
            );
        }

        public static function catch_post(){
            $curl_user = array();

            $curl_user['scid'] = $_POST["scid"];
            $curl_user['title'] = $_POST["title"];
            $curl_user['info'] = $_POST["info"];
            $curl_user['adid'] = $_POST["adid"];
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

            if(!isset($_POST['scid']) || !isset($_POST['title']) || !isset($_POST['info'])  || !isset($_POST['adid'])  ){
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

            $check_store = $wpdb->get_row("SELECT `title`, `status` FROM $tbl_store_v2 WHERE title LIKE '%{$user["title"]}%' AND `status` = 'active' ");
            if (!empty($check_store)) {
                return array(
                    "status" => "failed",
                    "message" => "This Store is already exists. $check_store->title ",
                );
            }

            $import_data = $wpdb->query("INSERT INTO
                $tbl_store_v2
                    ($tbl_store__field_v2)
                VALUES
                    ( '{$user["scid"]}', '{$user["title"]}', '{$user["info"]}', '{$user["adid"]}', '{$user["wpid"]}') ");
            $import_data_id = $wpdb->insert_id;

            $hsid = TP_Globals_v2::generating_pubkey($import_data_id, $tbl_store_v2, 'hsid', true, 15);

            // Optional Upload for avatar and banner
            if (isset($files['avatar']) || isset($files['banner'])) {

                if (empty($files['banner']['name'])) {
                    unset($files['banner']);
                }
                if (empty($files['avatar']['name'])) {
                    unset($files['avatar']);
                }

                $image = TP_Globals_v2::upload_image( $request, $files);
                if ($image['status'] != 'success') {
                    return array(
                        "status" => $image['status'],
                        "message" => $image['message']
                    );
                }

                if (!empty($files['avatar']['name'])) {
                    $avatar = $wpdb->query("UPDATE $tbl_store_v2 SET `avatar` =  '{$image["data"][0]["avatar_id"]}' WHERE ID = '$import_data_id' ");
                    if ($avatar < 1) {
                        return array(
                            "status" => "failed",
                            "message" => "An error occured while submitting data to server."
                        );
                    }
                }

                if (!empty($files['banner']['name'])) {
                    $banner = $wpdb->query("UPDATE $tbl_store_v2 SET `banner` =  '{$image["data"][0]["banner_id"]}' WHERE ID = '$import_data_id' ");
                    if ($banner < 1) {
                        return array(
                            "status" => "failed",
                            "message" => "An error occured while submitting data to server."
                        );
                    }
                }
            }

            if ($import_data < 1) {
                $wpdb->query("ROLLBACK");
                return array(
                    "status" => "failed",
                    "message" => "An error occured while submitting data to server."
                );
            }else{
                $wpdb->query("ROLLBACK");
                return array(
                    "status" => "success",
                    "message" => "Data has been added successfully.",
                    "data" => $hsid
                );
            }
        }
    }