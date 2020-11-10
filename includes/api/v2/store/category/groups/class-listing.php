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

    class TP_Store_Category_Group_Listing_v2 {

        //REST API Call
        public static function listen($request){

            return rest_ensure_response(
                self::listen_open($request)
            );
        }

        public static function catch_post(){
            $data = array();

            isset($_POST['title']) && !empty($_POST['title']) ? $data['title'] =  $_POST['title'] :  $data['title'] = null ;
            isset($_POST['ID']) && !empty($_POST['ID']) ? $data['ID'] =  $_POST['ID'] :  $data['ID'] = null ;
            isset($_POST['status']) && !empty($_POST['status']) ? $data['status'] =  $_POST['status'] :  $data['status'] = null ;


            return $data;
        }

        public static function listen_open($request){

            global $wpdb;
            $tbl_store_category_groups = TP_STORES_CATEGORY_GROUPS_v2;

            // Step 1: Check if prerequisites plugin are missing
            $plugin = TP_Globals_v2::verify_prerequisites();
            if ($plugin !== true) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. ".$plugin." plugin missing!",
                );
            }

            // Step 2: Validate user
            // if (DV_Verification::is_verified() == false) {
            //     return array(
            //         "status" => "unknown",
            //         "message" => "Please contact your administrator. Verification Issues!",
            //     );
            // }

            $posts = self::catch_post();

            $sql = "SELECT
                hsid as ID,
                title,
                info,
                `status`
            FROM
                $tbl_store_category_groups as groups
            WHERE
                id IN ( SELECT MAX( id ) FROM $tbl_store_category_groups WHERE groups.hsid = hsid GROUP BY hsid ) ";

            if ($posts['title'] != null) {
                $sql .= " AND title LIKE '%{$posts["title"]}%' ";
            }

            if($posts['ID'] != null){
                $sql .= " AND hsid = '{$posts["ID"]}' ";
            }

            if($posts['status'] != null){
                $sql .= " AND `status` = '{$posts["status"]}' ";
            }

            $data = $wpdb->get_results($sql);

            return array(
                "status" => "success",
                "data" => $data
            );
        }
    }