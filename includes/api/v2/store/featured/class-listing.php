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

            isset($_POST['type']) && !empty($_POST['type'])? $curl_user['type'] =  $_POST['type'] :  $curl_user['type'] = null ;
            isset($_POST['ID']) && !empty($_POST['ID'])? $curl_user['ID'] =  $_POST['ID'] :  $curl_user['ID'] = null ;
            isset($_POST['stid']) && !empty($_POST['stid'])? $curl_user['stid'] =  $_POST['stid'] :  $curl_user['stid'] = null ;
            isset($_POST['status']) && !empty($_POST['status'])? $curl_user['status'] =  $_POST['status'] :  $curl_user['status'] = null ;

            return $curl_user;
        }

        public static function listen_open($request){
            global $wpdb;

            $tbl_store = TP_STORES_v2;
            $tbl_featured_store = TP_FEATURED_STORES_v2;
            $tbl_featured_store_groups = TP_FEATURED_STORES_GROUPS_v2;
            $tbl_store_category_groups = TP_STORES_CATEGORY_GROUPS_v2;

            // Step 1: Check if prerequisites plugin are missing
            $plugin = TP_Globals_v2::verify_prerequisites();
            if ($plugin !== true) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. ".$plugin." plugin missing!",
                );
            }

            // // Step 2: Validate user
            if (DV_Verification::is_verified() == false) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Verification issues!",
                );
            }

            $user = self::catch_post();

            $sql = " SELECT
                hsid as ID,
                stid,
                (SELECT title FROM $tbl_store s WHERE hsid = stid AND id IN ( SELECT MAX( id ) FROM $tbl_store  WHERE hsid = s.hsid GROUP BY hsid  ) ) as title,
                groups,
                avatar,
                banner,
                `status`,
                date_created
            FROM
                $tbl_featured_store fs
            WHERE
                id IN ( SELECT MAX( id ) FROM $tbl_featured_store WHERE  hsid = fs.hsid GROUP BY stid ) " ;

            if ($user["ID"] != null ) {
                $sql .= " AND hsid = '{$user["ID"]}' ";
            }

            if ($user["status"] != null ) {
                $sql .= " AND `status` = '{$user["status"]}' ";
            }

            if ($user["stid"] != null ) {
                $sql .= " AND `stid` = '{$user["stid"]}' ";
            }

            if ($user['type'] != null) {

                // Check filter type
                    $get = $wpdb->get_results("SELECT title FROM $tbl_featured_store_groups ");
                    $check = TP_Globals_v2::check_type($user['type'], $get);
                    if ($check == false) {
                        return array(
                            "status" => "failed",
                            "message" => "Invalid value of type."
                        );
                    }
                // End

                $sql .= " AND  groups IN (SELECT hsid FROM $tbl_featured_store_groups WHERE title LIKE  '%{$user["type"]}%' )  ";

            }

            $sql .= " LIMIT 5";
            $data = $wpdb->get_results($sql);

            return array(
                "status" => "success",
                "data" => $data
            );
        }
    }