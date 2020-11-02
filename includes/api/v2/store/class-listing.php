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
            isset($_POST['type']) && !empty($_POST['type'])? $curl_user['type'] =  $_POST['type'] :  $curl_user['type'] = null ;

            return $curl_user;
        }

        public static function listen_open($request){

            global $wpdb;
            $tbl_store = TP_STORES_v2;
            $tbl_store_category = TP_STORES_CATEGORIES_v2;
            $tbl_address_view = DV_ADDRESS_VIEW;
            $tbl_operation = MP_OPERATIONS_v2;
            $tbl_store_category_groups = TP_STORES_CATEGORY_GROUPS_v2;
            $tbl_rate = TP_STORES_RATINGS_v2;

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
                ID,
                hsid,
                title,
                scid,
                null as category_name,
                info,
                avatar,
                banner,
                null as rates,
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
                $tbl_store
            WHERE
                id IN ( SELECT MAX( id ) FROM $tbl_store GROUP BY title ) ";

            if($user["stid"] != null){
                $sql .= " AND hsid = '{$user["stid"]}' ";
            }

            if($user["title"] != null){
                $sql .= " AND title LIKE '%{$user["title"]}%' ";
            }

            if ($user['scid'] != null) {
                $sql .= " AND scid = '{$user["scid"]}' ";
            }

            if ($user['type'] != null) {

                if ($user['type'] != "robinson" && $user['type'] != "food/drinks" && $user['type'] != "market" && $user['type'] != "pasamall") {
                    return array(
                        "status" => "failed",
                        "message" => "Invalid type value of type.",
                    );
                }

                switch ($user['type']) {
                    case 'robinson':
                        $sql .= " AND scid IN (SELECT hsid FROM $tbl_store_category WHERE groups =  (SELECT hsid FROM $tbl_store_category_groups WHERE title LIKE  '%robinson%' ) )";
                        break;
                    case 'food/drinks':
                        $sql .= " AND scid IN (SELECT hsid FROM $tbl_store_category WHERE groups =  (SELECT hsid FROM $tbl_store_category_groups WHERE title LIKE  '%food/drinks%' ) )";
                        break;
                    case 'market':
                        $sql .= " AND scid IN (SELECT hsid FROM $tbl_store_category WHERE groups =  (SELECT hsid FROM $tbl_store_category_groups WHERE title LIKE  '%market%' ) )";
                        break;
                    case 'pasamall':
                        $sql .= " AND scid IN (SELECT hsid FROM $tbl_store_category WHERE groups =  (SELECT hsid FROM $tbl_store_category_groups WHERE title LIKE  '%pasamall%' ) )";
                        break;
                }
            }

            $data = $wpdb->get_results($sql);

            // Get other store information
                foreach ($data as $key => $value) {
                    // Store category
                        $get_category = $wpdb->get_row("SELECT title FROM $tbl_store_category WHERE hsid = '$value->scid' ");
                        if (empty($get_category)) {
                            $value->category_name = '';
                        }else{

                            $value->category_name = $get_category->title;
                        }
                    // End

                    // Get Store Data
                        $get_store_address = $wpdb->get_row("SELECT * FROM $tbl_address_view WHERE ID = $value->adid ");

                        if (empty($get_store_address)) {
                            $value->street = "";
                            $value->brgy = "";
                            $value->city = "";
                            $value->province = "";
                            $value->country = "";
                        }else{
                            $value->street = $get_store_address->street;
                            $value->brgy = $get_store_address->brgy;
                            $value->city = $get_store_address->city;
                            $value->province = $get_store_address->province;
                            $value->country = $get_store_address->country;
                        }



                    // End
                    $get_rates = $wpdb->get_row("SELECT hsid as ID, stid, AVG(rates) as rates FROM $tbl_rate WHERE stid = '$value->hsid' ");
                    if (!empty($get_rates)) {
                        $value->rates = $get_rates->rates;
                    }
                }
            // End

            return array(
                "status" => "success",
                "data" => $data
            );

        }
    }