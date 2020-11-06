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

    class TP_Product_Category_Listing_Product_v2 {

        //REST API Call
        public static function listen($request){

            return rest_ensure_response(
                self::listen_open($request)
            );
        }

        public static function catch_post(){
            $curl_user = array();

            isset($_POST['stid']) && !empty($_POST['stid'])? $curl_user['stid'] =  $_POST['stid'] :  $curl_user['stid'] = null ;
            isset($_POST['title']) && !empty($_POST['title'])? $curl_user['title'] =  $_POST['title'] :  $curl_user['title'] = null ;
            isset($_POST['ID']) && !empty($_POST['ID'])? $curl_user['ID'] =  $_POST['ID'] :  $curl_user['ID'] = null ;
            isset($_POST['status']) && !empty($_POST['status'])? $curl_user['status'] =  $_POST['status'] :  $curl_user['status'] = null ;

            return $curl_user;
        }

        public static function listen_open($request){

            global $wpdb;
            $tbl_product_category_v2 = TP_PRODUCT_CATEGORY_v2;
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

            $user = self::catch_post();

            $sql = "SELECT
                hsid as ID,
                stid,
                title,
                info,
                `status`,
                date_created
            FROM
                $tbl_product_category_v2
            WHERE
                id IN ( SELECT MAX( id ) FROM $tbl_product_category_v2 GROUP BY hsid ) ";

            if ($user["stid"] != null) {
                $sql .= " AND stid = '{$user["stid"]}' ";
            }

            if ($user["title"] != null) {
                $sql .= " AND title LIKE '%{$user["title"]}%' ";
            }

            if ($user["ID"] != null) {
                $sql .= " AND hsid = '{$user["ID"]}' ";
            }

            if ($user["status"] != null) {
                $sql .= " AND `status` = '{$user["status"]}' ";
            }

            $data = $wpdb->get_results($sql);

            foreach ($data as $key => $value) {
                $value->products = $wpdb->get_results("SELECT
                        hsid as ID,
                        stid,
                        title,
                        info,
                        avatar,
                        banner,
                        price,
                        IF(discount is null OR discount = '', 0, discount) as discount
                    FROM
                        $tbl_product
                    WHERE
                        pcid = '$value->ID'
                    AND
                        ID IN ( SELECT MAX( pdd.ID ) FROM $tbl_product  pdd WHERE pdd.hsid = hsid GROUP BY hsid ) ");
            }
            return array(
                "status" => "success",
                "data" => $data
            );
        }
    }