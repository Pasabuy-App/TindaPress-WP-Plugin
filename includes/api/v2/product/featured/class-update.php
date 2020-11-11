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

    class TP_Featured_Products_Update_v2 {

        //REST API Call
        public static function listen($request){

            return rest_ensure_response(
                self::listen_open($request)
            );
        }

        public static function catch_post(){
            $curl_user = array();

            $curl_user['fpid'] = $_POST["fpid"];
            $curl_user['wpid'] = $_POST["wpid"];

            return $curl_user;
        }

        public static function listen_open($request){

            global $wpdb;
            $tbl_store = TP_STORES_v2;
            $tbl_product = TP_PRODUCT_v2;
            $tbl_featured_product = TP_FEATURED_PRODUCT_v2;
            $tbl_featured_product_filed = TP_FEATURED_PRODUCT_FIELDS_v2;
            $files = $request->get_file_params();

            if (!isset($_POST['fpid'])  ) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your admininstrator. Request unknown!"
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

            // Check if featured product exists
                $get_data = $wpdb->get_row("SELECT * FROM $tbl_featured_product WHERE hsid = '{$user["fpid"]}' AND `status` = 'active'  AND  ID IN ( SELECT MAX( pdd.ID ) FROM $tbl_featured_product  fp WHERE fp.hsid = hsid GROUP BY hsid ) ");
                if (empty($get_data)) {
                    return array(
                        "status" => "failed",
                        "message" => "This featured product  does not exists.",
                    );
                }
            // End

            isset($_POST['stid'])   && !empty($_POST['stid'])   ? $user['stid']   =  $_POST['stid']   :  $user['stid']   = $get_data->stid ;
            isset($_POST['pdid'])   && !empty($_POST['pdid'])   ? $user['pdid']   =  $_POST['pdid']   :  $user['pdid']   = $get_data->pdid ;
            isset($files['avatar']) && !empty($files['avatar']) ? $user['avatar'] =  $files['avatar'] :  $user['avatar'] = $get_data->avatar;
            isset($files['banner']) && !empty($files['banner']) ? $user['banner'] =  $files['banner'] :  $user['banner'] = $get_data->banner;

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

            $wpdb->query("START TRANSACTION");

            $import_data = $wpdb->query("INSERT INTO
                $tbl_featured_product
                    (`hsid`, $tbl_featured_product_filed, `status`, `avatar`, `banner`)
                VALUES
                    ('$get_data->hsid', '{$user["stid"]}', '{$user["pdid"]}', '{$user["wpid"]}', '$get_data->status', '{$user["avatar"]}', '{$user["banner"]}',)");
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
                    "message" => "Data has been added successfully."
                );
            }
        }
    }