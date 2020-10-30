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

    class TP_Product_Variant_Listing_v2 {

        //REST API Call
        public static function listen($request){

            return rest_ensure_response(
                self::listen_open($request)
            );
        }

        public static function catch_post(){
            $curl_user = array();

            isset($_POST['pdid']) && !empty($_POST['pdid'])? $curl_user['pdid'] =  $_POST['pdid'] :  $curl_user['pdid'] = null ;
            isset($_POST['title']) && !empty($_POST['title'])? $curl_user['title'] =  $_POST['title'] :  $curl_user['title'] = null ;
            isset($_POST['ID']) && !empty($_POST['ID'])? $curl_user['ID'] =  $_POST['ID'] :  $curl_user['ID'] = null ;
            isset($_POST['required']) && !empty($_POST['required'])? $curl_user['required'] =  $_POST['required'] :  $curl_user['required'] = null ;
            isset($_POST['status']) && !empty($_POST['status'])? $curl_user['status'] =  $_POST['status'] :  $curl_user['status'] = null ;

            return $curl_user;
        }

        public static function listen_open($request){

            global $wpdb;
            $tbl_variants = TP_PRODUCT_VARIANTS_v2;


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

            $user = self::catch_post();

            $sql = " SELECT
                hsid as ID,
                pdid,
                title,
                info,
                price,
                `required`,
                `status`,
                created_by,
                date_created
            FROM
                $tbl_variants
            WHERE
                id IN ( SELECT MAX( id ) FROM $tbl_variants GROUP BY hsid )
            AND parents = '' OR parents is null";

            if ($user["pdid"]) {
                $sql .= " AND pdid = '{$user["pdid"]}' ";
            }

            if ($user["title"]) {
                $sql .= " AND title LIKE '%{$user["title"]}%' ";
            }

            if ($user["ID"] != null) {
                $sql .= " AND hsid = '{$user["title"]}' ";
            }

            if ($user["required"] != null) {
                if ($user["required"] != "true" && $user["required"] != "false") {
                    return array(
                        "status" => "failed",
                        "message" => "Invalid value of required"
                    );
                }
                $sql .= " AND `required` = '{$user["required"]}' ";
            }

            if ($user["status"] != null) {
                if ($user["status"] != "active" && $user["status"] != "inactive") {
                    return array(
                        "status" => "failed",
                        "message" => "Invalid value of status"
                    );
                }
                $sql .= " AND `status` = '{$user["status"]}' ";
            }

            $parent_variants = $wpdb->get_results($sql);

            foreach ($parent_variants as $key => $value) {
                $get_child = $wpdb->get_results("SELECT
                    hsid as ID,
                    pdid,
                    title,
                    info,
                    price,
                    `required`,
                    `status`,
                    created_by,
                    date_created
                FROM
                    $tbl_variants
                WHERE
                    parents = '$value->ID' ");
                $value->options = $get_child;
            }

            return array(
                "status" => "success",
                "message" => $parent_variants
            );
        }
    }