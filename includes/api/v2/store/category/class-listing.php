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

    class TP_Store_Category_Listing_v2 {

        //REST API Call
        public static function listen($request){

            return rest_ensure_response(
                self::listen_open($request)
            );
        }

        public static function catch_post(){
            $curl_user = array();

            isset($_POST['groups']) && !empty($_POST['groups'])? $curl_user['groups'] =  $_POST['groups'] :  $curl_user['groups'] = null ;
            isset($_POST['title']) && !empty($_POST['title'])? $curl_user['title'] =  $_POST['title'] :  $curl_user['title'] = null ;
            isset($_POST['ID']) && !empty($_POST['ID'])? $curl_user['ID'] =  $_POST['ID'] :  $curl_user['ID'] = null ;

            return $curl_user;
        }

        public static function listen_open($request){

            global $wpdb;
            $tbl_store_category = TP_STORES_CATEGORIES_v2;
            $tbl_store_category_groups = TP_STORES_CATEGORY_GROUPS_v2;

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

            $sql = "SELECT
                hsid as ID,
                title,
                (SELECT title FROM $tbl_store_category_groups WHERE hsid = groups ) as category_groups,
                info,
                avatar,
                `status`,
                date_created
            FROM
                $tbl_store_category
            WHERE
                id IN ( SELECT MAX( id ) FROM $tbl_store_category GROUP BY title ) ";


            if ($user['title'] != null) {
                $sql .= " AND title LIKE '%{$user["title"]}%' ";
            }

            if($user['ID'] != null){
                $sql .= " AND hsid = '{$user["ID"]}' ";
            }

            if ($user['groups'] != null) {
                $sql .= "  HAVING category_groups = '{$user["groups"]}' ";
            }

            $data = $wpdb->get_results($sql);

            return array(
                "status" => "success",
                "message" => $data
            );
        }
    }