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

    class TP_Product_Variants_Update_v2 {

        //REST API Call
        public static function listen($request){

            return rest_ensure_response(
                self::listen_open($request)
            );
        }

        public static function catch_post(){
            $curl_user = array();
            $curl_user['vrid'] = $_POST['vrid'];
            $curl_user['wpid'] = $_POST['wpid'];
            return $curl_user;
        }

        public static function listen_open($request){

            global $wpdb;
            $tbl_product = TP_PRODUCT_v2;
            $tbl_variants =  TP_PRODUCT_VARIANTS_v2;
            $tbl_variants_filed =  TP_PRODUCT_VARIANTS_FILEDS_v2;

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

            if (!isset($_POST['vrid'])) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Request unknown.",
                );
            }

            $user = self::catch_post();

            // Fetch all data of variants
                $variant_data = $wpdb->get_row("SELECT * FROM $tbl_variants v WHERE hsid = '{$user["vrid"]}' AND id IN ( SELECT MAX( id ) FROM $tbl_variants WHERE hsid = v.hsid  GROUP BY hsid ) ");
                if (empty($variant_data)) {
                    return array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Verification Issues!",
                    );
                }
            // End

            // Create listener of post param
                isset($_POST['pdid']) && !empty($_POST['pdid'])? $user['pdid'] =  $_POST['pdid'] :  $user['pdid'] = $variant_data->pdid ;
                isset($_POST['title']) && !empty($_POST['title'])? $user['title'] =  $_POST['title'] :  $user['title'] = $variant_data->title ;
                isset($_POST['info']) && !empty($_POST['info'])? $user['info'] =  $_POST['info'] :  $user['info'] = $variant_data->info ;
                isset($_POST['price']) && !empty($_POST['price'])? $user['price'] =  $_POST['price'] :  $user['price'] = $variant_data->price ;
                isset($_POST['required']) && !empty($_POST['required'])? $user['required'] =  $_POST['required'] :  $user['required'] = $variant_data->required ;
                isset($_POST['parents']) && !empty($_POST['parents'])? $user['parents'] =  $_POST['parents'] :  $user['parents'] = $variant_data->parents ;
            // End

            if ($user["required"] != "true" && $user["required"] != "false") {
                return array(
                    "status" => "failed",
                    "message" => "Invalid value of required.",
                );
            }

            if ($user['parents'] != null) {
                $checK_parent = $wpdb->get_row("SELECT ID FROM $tbl_variants WHERE hsid = '{$user["parents"]}' ");
                if (empty($checK_parent)) {
                    return array(
                        "status" => "failed",
                        "message" => "Parent variant does not exists.",
                    );
                }
            }

            $wpdb->query("START TRANSACTION");

            $import_data = $wpdb->query("INSERT INTO
                $tbl_variants
                    (`hsid`,$tbl_variants_filed, `parents`, `status`)
                VALUES
                    ('$variant_data->hsid', '{$user["pdid"]}', '{$user["title"]}', '{$user["info"]}', '{$user["price"]}', '{$user["required"]}', '{$user["wpid"]}', '{$user["parents"]}', '$variant_data->status' ) ");

            if ($import_data < 1 ) {
                $wpdb->query("ROLLBACK");
                return array(
                    "status" => "failed",
                    "message" => "An error occured while submitting data to server."
                );
            }else{
                $wpdb->query("COMMIT");
                return array(
                    "status" => "success",
                    "message" => "Data has been updated successfully."
                );
            }
        }
    }