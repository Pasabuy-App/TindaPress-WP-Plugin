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

    class TP_Store_Listing_v2 {

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
            isset($_POST['scid']) && !empty($_POST['scid'])? $curl_user['scid'] =  $_POST['scid'] :  $curl_user['scid'] = null ;
            return $curl_user;
        }

        public static function listen_open($request){

            global $wpdb;
            $tbl_store = TP_STORES_v2;
            $tbl_store_category = TP_STORES_CATEGORIES_v2;
            $tbl_address_view = DV_ADDRESS_VIEW;
            $tbl_operation = MP_OPERATIONS_v2;

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

            $sql = "SELECT
                ID,
                hsid,
                title,
                scid,
                null as category_name,
                info,
                avatar,
                banner,
                adid,
                null as street,
                null as brgy,
                null as city,
                null as province,
                null as country,
                `status`,
                created_by,
                date_created
            FROM
                $tbl_store ";

            if($user["stid"] != null){
                $sql .= " WHERE hsid = '{$user["stid"]}' ";
            }

            if($user["title"] != null){
                if ($user["stid"] != null) {
                    $sql .= " AND title LIKE '%{$user["title"]}%' ";
                }else{
                    $sql .= " WHERE title LIKE '%{$user["title"]}%' ";
                }
            }

            if ($user['scid'] != null) {
                if ($user["stid"] != null || $user["title"] != null) {
                    $sql .= " AND scid = '{$user["scid"]}' ";
                }else{
                    $sql .= " WHERE scid = '{$user["scid"]}' ";
                }
            }

            $data = $wpdb->get_results($sql);

            // Get other store information
                foreach ($data as $key => $value) {
                    // Store category
                        $get_category = $wpdb->get_row("SELECT title FROM $tbl_store_category WHERE hsid = '$value->scid' ");
                        $value->category_name = $get_category->title;
                    // End

                    // Get Store Data
                        $get_store_address = $wpdb->get_row("SELECT * FROM $tbl_address_view WHERE stid = $value->ID ");
                        $value->street = $get_store_address->street;
                        $value->brgy = $get_store_address->brgy;
                        $value->city = $get_store_address->city;
                        $value->province = $get_store_address->province;
                        $value->country = $get_store_address->country;


                    // End
                }
            // End

            return array(
                "status" => "success",
                "message" => $data
            );

        }
    }