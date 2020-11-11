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

    class TP_Featured_Products_Delete_v2 {

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

            $wpdb->query("START TRANSACTION");

            $import_data = $wpdb->query("INSERT INTO
                $tbl_featured_product
                    (`hsid`, $tbl_featured_product_filed, `status`, `avatar`, `banner`)
                VALUES
                    ('$get_data->hsid', '$get_data->stid', '$get_data->pdid', '{$user["wpid"]}', 'inactive', '$get_data->avatar', '$get_data->banner',)");
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