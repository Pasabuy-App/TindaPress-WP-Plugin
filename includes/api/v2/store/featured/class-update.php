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

    class TP_Featured_Store_Update_v2 {

        //REST API Call
        public static function listen($request){

            return rest_ensure_response(
                self::listen_open($request)
            );
        }

        public static function catch_post(){
            $curl_user = array();

            $curl_user['fstid'] = $_POST["fstid"];
            $curl_user['wpid'] = $_POST["wpid"];

            return $curl_user;
        }

        public static function listen_open($request){

            global $wpdb;
            $tbl_featured_store = TP_FEATURED_STORES_v2;
            $tbl_featured_store_field = TP_FEATURED_STORES_FIELDS_v2;

            $files = $request->get_file_params();

            // Step 1: Check if prerequisites plugin are missing
            $plugin = TP_Globals_v2::verify_prerequisites();
            if ($plugin !== true) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. ".$plugin." plugin missing!",
                );
            }

            // // Step 2: Validate user
            if (DV_Verification::is_verified() == false) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Verification issues!",
                );
            }

            if(!isset($_POST['fstid'])){
                return  array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Request Unknown!",
                );
            }

            $user = self::catch_post();



            // Check if Featured Groups exists
            $get_data = $wpdb->get_row("SELECT * FROM $tbl_featured_store fs WHERE hsid = '{$user["fstid"]}' AND id IN ( SELECT MAX( id ) FROM $tbl_featured_store WHERE  hsid = fs.hsid GROUP BY stid ) ");

            if (empty($check_featured_groups)) {
                return array(
                    "status" => "failed",
                    "message" => "This featured store does not exists. ",
                );
            }
            // End

            isset($_POST['groups']) && !empty($_POST['groups']) ? $user['groups'] = $_POST['groups'] : $user['groups'] = $get_data->groups;
            isset($_POST['stid']) &&   !empty($_POST['stid'])   ? $user['stid']   = $_POST['stid']   : $user['stid']   = $get_data->stid;
            isset($files['avatar']) && !empty($files['avatar']) ? $user['avatar'] = $files['avatar'] : $user['avatar'] = $get_data->avatar;
            isset($files['banner']) && !empty($files['banner']) ? $user['banner'] = $files['banner'] : $user['banner'] = $get_data->banner;


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
                    $user["avatar"] = $get_data->avatar;
                }

                if (!empty($files['banner']['name'])) {
                    $user["banner"] = $image["data"][0]["banner_id"];
                }else{
                    $user["banner"] = $get_data->banner;
                }
            }

            // Start mysql transaction
            $wpdb->query("START TRANSACTION");

            $import_data = $wpdb->query("INSERT INTO
                $tbl_featured_store
                    (`hsid`, $tbl_featured_store_field, `status`, `avatar`, `banner`)
                VALUES
                    ('$get_data->hsid', '{$user["stid"]}', '{$user["groups"]}', '{$user["wpid"]}', '$get_data->status', '{$user["avatar"]}', '{$user["banner"]}' )");
            $import_data_id = $wpdb->insert_id;

            if($import_data < 1){
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