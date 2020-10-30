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

    class TP_Store_Category_Group_Insert_v2 {

        //REST API Call
        public static function listen($request){

            return rest_ensure_response(
                self::listen_open($request)
            );
        }

        public static function catch_post(){
            $curl_user = array();
            $curl_user['title'] = $_POST['title'];
            $curl_user['info'] = $_POST['info'];
            $curl_user['wpid'] = $_POST['wpid'];
            return $curl_user;
        }

        public static function listen_open($request){

            global $wpdb;
            $tbl_store_category_groups =  TP_STORES_CATEGORY_GROUPS_v2;
            $tbl_store_category_groups_field =  TP_STORES_CATEGORY_GROUPS_FIELDS_v2;


              // Step 1: Check if prerequisites plugin are missing
            $plugin = TP_Globals::verify_prerequisites();
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

            if (!isset($_POST['title']) || !isset($_POST['info']) ) {
                return array(
                    "status" => "unknwon",
                    "message" => "Please contact your administrator. Request unknown!",
                );
            }

            $user = self::catch_post();

            $validate = TP_Globals_v2::check_listener($user);
            if ($validate !== true) {
                return array(
                    "status" => "failed",
                    "message" => "Required fileds cannot be empty "."'".ucfirst($validate)."'"."."
                );
            }

            // Check if title of category groups exists
                $check_group = $wpdb->get_row("SELECT `title` FROM $tbl_store_category_groups WHERE title LIKE '%{$user["title"]}%' ");
                if (!empty($check_group )) {
                    return array(
                        "status" => "failed",
                        "message" => "This category groups is already exists. $check_group->title "
                    );
                }
            // End

            $wpdb->query("START TRANSACTION");

            $import_data = $wpdb->query("INSERT INTO
                $tbl_store_category_groups
                    ($tbl_store_category_groups_field)
                VALUES
                    ('{$user["title"]}', '{$user["info"]}', '{$user["wpid"]}' )");

            $import_data_id = $wpdb->insert_id;

            $hsid = TP_Globals_v2::generating_pubkey($import_data_id, $tbl_store_category_groups, 'hsid', false, 64);

            if($import_data < 1 || $hsid == false){
                $wpdb->query("ROLLBACK");
                return array(
                    "status" => "failed",
                    "message" => "An error occured while submitting data to server."
                );
            }else{
                $wpdb->query("COMMIT");
                return array(
                    "status" => "success",
                    "message" => "Data has been added successfully."
                );
            }
        }
    }