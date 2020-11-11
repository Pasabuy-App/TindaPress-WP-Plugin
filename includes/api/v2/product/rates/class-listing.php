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

    class TP_Product_Rates_Listing_v2 {

        //REST API Call
        public static function listen($request){

            return rest_ensure_response(
                self::listen_open($request)
            );
        }

        public static function catch_post(){
            $curl_user = array();

            isset($_POST['pdid']) && !empty($_POST['pdid'])? $curl_user['pdid'] =  $_POST['pdid'] :  $curl_user['pdid'] = null ;
            isset($_POST['rates']) && !empty($_POST['rates'])? $curl_user['rates'] =  $_POST['rates'] :  $curl_user['rates'] = null ;
            isset($_POST['rated_by']) && !empty($_POST['rated_by'])? $curl_user['rated_by'] =  $_POST['rated_by'] :  $curl_user['rated_by'] = null ;
            isset($_POST['status']) && !empty($_POST['status'])? $curl_user['status'] =  $_POST['status'] :  $curl_user['status'] = null ;

            return $curl_user;
        }

        public static function listen_open($request){

            global $wpdb;
            $tbl_ratings = TP_PRODUCT_RATING_v2;
            $tbl_product = TP_PRODUCT_v2;

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

            $sql = "SELECT
                hsid as ID,
                pdid,
                (SELECT title FROM $tbl_product WHERE hsid = pdid AND  ID IN ( SELECT MAX( ID ) FROM $tbl_product  p WHERE p.hsid = hsid GROUP BY hsid ) ) as product_name,
                rates,
                comments,
                rated_by,
                date_created
            FROM
                $tbl_ratings
            WHERE
                ID IN ( SELECT MAX( ID ) FROM $tbl_ratings r WHERE r.hsid = hsid GROUP BY hsid )  ";

            $user = self::catch_post();

            if ($user['pdid'] != null ) {
                $sql .= " AND pdid = '{$user["pdid"]}' ";
            }

            if ($user['rates'] != null ) {
                $sql .= " AND rates = '{$user["rates"]}' ";
            }

            if ($user['rated_by'] != null ) {
                $sql .= " AND rated_by = '{$user["rated_by"]}' ";
            }


            if ($user['status'] != null ) {
                $sql .= " AND `status` = '{$user["status"]}' ";
            }


            $data = $wpdb->get_results($sql);

            return array(
                "status" => "success",
                "data" => $data
            );
        }
    }