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

    class TP_Store_Category_Group_Delete_v2 {

        //REST API Call
        public static function listen($request){

            return rest_ensure_response(
                self::listen_open($request)
            );
        }

        public static function catch_post(){
            $data = array();
            isset($_POST['ID']) && !empty($_POST['ID']) ? $data['ID'] =  $_POST['ID'] :  $data['ID'] = null ;
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

            $check_group = $wpdb->get_row("SELECT * 
                FROM 
                    $tbl_store_category_groups as groups
                WHERE 
                    hsid = '{$posts["ID"]}'
                AND 
                    id IN ( SELECT MAX( id ) FROM $tbl_store_category_groups WHERE groups.hsid = hsid GROUP BY hsid ) ");

            if(!$check_group){
                return array(
                    "status" => "unknown",
                    "message" => "This category group does not exists"
                );
            }

            return $check_group;

            

            return array(
                "status" => "success",
                "data" => $data
            );
        }
    }