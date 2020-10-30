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

    class TP_Featured_Store_Listing_v2 {

        //REST API Call
        public static function listen($request){

            return rest_ensure_response(
                self::listen_open($request)
            );
        }

        public static function catch_post(){
            $curl_user = array();

            isset($_POST['ctid']) && !empty($_POST['ctid'])? $curl_user['ctid'] =  $_POST['ctid'] :  $curl_user['ctid'] = null ;
            isset($_POST['title']) && !empty($_POST['title'])? $curl_user['title'] =  $_POST['title'] :  $curl_user['title'] = null ;
            isset($_POST['ID']) && !empty($_POST['ID'])? $curl_user['ID'] =  $_POST['ID'] :  $curl_user['ID'] = null ;
            isset($_POST['inventory']) && !empty($_POST['inventory'])? $curl_user['inventory'] =  $_POST['inventory'] :  $curl_user['inventory'] = null ;
            isset($_POST['status']) && !empty($_POST['status'])? $curl_user['status'] =  $_POST['status'] :  $curl_user['status'] = null ;

            return $curl_user;
        }

        public static function listen_open($request){

            global $wpdb;
            $tbl_featured_store = TP_FEATURED_STORES_v2;

            $sql = " SELECT
                hsid as ID,
                stid,
                groups,
                avatar,
                banner,
                `status`,
                date_created
            FROM
                $tbl_featured_store ";

            $data = $wpdb->query($sql);

            return array(
                "status" => "success",
                "data" => $data
            );
        }
    }