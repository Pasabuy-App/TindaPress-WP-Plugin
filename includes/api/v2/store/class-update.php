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

    class TP_Store_Update_v2 {

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


            isset($_POST['title']) && !empty($_POST['title'])? $user['title'] =  $_POST['title'] :  $user['title'] = $check_store->title;
            isset($_POST['info']) && !empty($_POST['info'])? $user['info'] =  $_POST['info'] :  $user['info'] = $check_store->info;
            isset($_POST['adid']) && !empty($_POST['adid'])? $user['adid'] =  $_POST['adid'] :  $user['adid'] = $check_store->adid;
            isset($_POST['commision']) && !empty($_POST['commision'])? $user['commision'] =  $_POST['commision'] :  $user['commision'] = $check_store->commision;
            isset($_POST['scid']) && !empty($_POST['scid'])? $user['scid'] =  $_POST['scid'] :  $user['scid'] = $check_store->scid;
            isset($files['avatar']) && !empty($files['avatar'])? $user['avatar'] =  $files['avatar'] :  $user['avatar'] = $check_store->avatar;
            isset($files['banner']) && !empty($files['banner'])? $user['banner'] =  $files['banner'] :  $user['banner'] = $check_store->banner;

            // Optional Update for avatar and banner
            if (isset($files['avatar']) || isset($files['banner'])) {
                if (empty($files['banner']['name'])) {
                    unset($files['banner']);
                }

                if (empty($files['avatar']['name'])) {
                    unset($files['avatar']);
                }

                if (isset($files['avatar']) || isset($files['banner'])) {
                    $image = TP_Globals_v2::upload_image( $request, $files);
                    if ($image['status'] != 'success') {
                        return array(
                            "status" => $image['status'],
                            "message" => $image['message']
                        );
                    }
                }

                if (!empty($files['avatar']['name'])) {
                    $user["avatar"] = $image["data"][0]["avatar_id"];
                }else{
                    $user["avatar"] = $check_store->avatar;
                }

                if (!empty($files['banner']['name'])) {
                    $user["banner"] = $image["data"][0]["banner_id"];
                }else{
                    $user["banner"] = $check_store->banner;
                }
            }

            $import_data = $wpdb->query("INSERT INTO
                $tbl_store_v2
                    ( `avatar`, `banner`, `hsid`, $tbl_store__field_v2, `status`, `commision`)
                VALUES
                    ('{$user["avatar"]}', '{$user["banner"]}', '$check_store->hsid', '{$user["scid"]}', '{$user["title"]}', '{$user["info"]}', '{$user["adid"]}', '{$user["wpid"]}', '$check_store->status', '{$user["commision"]}' ) ");
            $import_data_id = $wpdb->insert_id;


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
                    "message" => "Data has been updated successfully.",
                    "data" => $check_store->hsid
                );
            }
        }
    }