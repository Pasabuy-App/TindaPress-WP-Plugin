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
            $tbl_store_category_groups_fields = TP_STORES_CATEGORY_GROUPS_FIELDS_v2;

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

            if($check_group->status == "inactive"){
                return array(
                    "status" => "unknown",
                    "message" => "This category group is already inactive"
                );
            }

            // Start MYSQL Transaction
            $wpdb->query("START TRANSACTION");

            // Insert a new category group with a status of 'inactive'
            $group = $wpdb->query("INSERT
                INTO
                    $tbl_store_category_groups
                        (`hsid`, $tbl_store_category_groups_fields, `status`)
                    VALUES
                        ('$check_group->hsid', '$check_group->title', '$check_group->info', '$check_group->created_by', 'inactive' ) ");
            $group_id = $wpdb->insert_id;

            if ($group_id < 1 ) {
                $wpdb->query("ROLLBACK");
                return array(
                    "status" => "failed",
                    "message" => "An error occured while submitting data to server."
                );
            }else{
                $wpdb->query("COMMIT");
                return array(
                    "status" => "success",
                    "message" => "Data has been deleted sucessfully."
                );
            }
            
        }
    }