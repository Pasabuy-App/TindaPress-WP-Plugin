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

            isset($_POST['ID']) && !empty($_POST['ID'])? $curl_user['ID'] =  $_POST['ID'] :  $curl_user['ID'] = null ;
            isset($_POST['stid']) && !empty($_POST['stid'])? $curl_user['stid'] =  $_POST['stid'] :  $curl_user['stid'] = null ;
            isset($_POST['status']) && !empty($_POST['status'])? $curl_user['status'] =  $_POST['status'] :  $curl_user['status'] = null ;

            return $curl_user;
        }

        public static function listen_open($request){

            global $wpdb;
            $tbl_featured_store = TP_FEATURED_STORES_v2;

            $user = self::catch_post();

            $sql = " SELECT
                hsid as ID,
                stid,
                groups,
                avatar,
                banner,
                `status`,
                date_created
            FROM
                $tbl_featured_store
            WHERE
                id IN ( SELECT MAX( id ) FROM $tbl_featured_store GROUP BY stid ) ";

            if ($user["ID"] != null ) {
                $sql .= " AND hsid = '{$user["ID"]}' ";
            }

            if ($user["status"] != null ) {
                $sql .= " AND `status` = '{$user["status"]}' ";
            }

            if ($user["stid"] != null ) {
                $sql .= " AND `stid` = '{$user["stid"]}' ";
            }

            $data = $wpdb->get_results($sql);

            return array(
                "status" => "success",
                "data" => $data
            );
        }
    }