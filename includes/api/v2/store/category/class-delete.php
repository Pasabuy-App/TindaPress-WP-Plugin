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

    class TP_Store_Category_Delete_v2 {

        //REST API Call
        public static function listen($request){

            return rest_ensure_response(
                self::listen_open($request)
            );
        }

        public static function catch_post(){
            $data = array();
            $data['ID'] = $_POST["ID"];
            return $data;
        }

        public static function listen_open($request){
            global $wpdb;
            $table_store_categories = TP_STORES_CATEGORIES_v2;
            $table_store_categories_field = TP_STORES_CATEGORIES_FIELDS_v2;

            // Step 1: Check if prerequisites plugin are missing
            $plugin = TP_Globals_v2::verify_prerequisites();
            if ($plugin !== true) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. ".$plugin." plugin missing!",
                );
            }

            if(empty($_POST['ID'])){
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Request Unknown!",
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

            $check_category = $wpdb->get_row("SELECT * FROM $table_store_categories as cat WHERE hsid LIKE '%{$posts["ID"]}%'
            AND 
               id IN ( SELECT MAX( id ) FROM $table_store_categories WHERE cat.hsid = hsid GROUP BY hsid )");

            if (!$check_category) {
                return array(
                    "status" => "failed",
                    "message" => "This store category does exists.",
                );
            }

            if ($check_category->status == 'inactive') {
                return array(
                    "status" => "failed",
                    "message" => "This store category is already inactive.",
                );
            }

            $category = $wpdb->query("INSERT
                INTO
                    $table_store_categories
                        (`hsid`, $table_store_categories_field, `status`)
                VALUES
                    ('$check_category->hsid', '$check_category->title', '$check_category->info', '$check_category->groups', '$check_category->avatar', '$check_category->created_by', 'inactive' ) ");
            
            $category = $wpdb->insert_id;


            if ($category < 1) {
                $wpdb->query("ROLLBACK");
                return array(
                    "status" => "failed",
                    "message" => "An error occured while submitting data to server"
                );

            }else{
                $wpdb->query("ROLLBACK");
                return array(
                    "status" => "success",
                    "message" => "Data has been deleted successfully"
                );

            }
        }
    }