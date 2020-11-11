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

    class TP_Store_Category_Update_v2 {

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
            $files = $request->get_file_params();

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
                    "message" => "Please contact your administrator. Required fields cannot be empty!",
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
                    "message" => "This store category is currently inactive.",
                );
            }

            isset($_POST['title']) && !empty($_POST['title']) ? $posts['title'] =  $_POST['title'] :  $posts['title'] = $check_category->title;
            isset($_POST['info']) && !empty($_POST['info']) ? $posts['info'] =  $_POST['info'] :  $posts['info'] = $check_category->info;
            isset($files['avatar']) && !empty($files['avatar']) ? $posts['avatar'] =  $files['avatar'] :  $posts['avatar'] = $check_category->avatar;
            isset($_POST['parent']) && !empty($_POST['parent']) ? $posts['parent'] =  $_POST['parent'] :  $posts['parent'] = $check_category->parent;
            isset($_POST['groups']) && !empty($_POST['groups']) ? $posts['groups'] =  $_POST['groups'] :  $posts['groups'] = $check_category->groups;

            // Optional Update for avatar and banner
            if (isset($files['avatar']) || isset($files['banner'])) {
                if (empty($files['avatar']['name'])) {
                    unset($files['avatar']);
                }

                if (isset($files['avatar']) || isset($files['banner'])) {
                    $image = TP_Globals_v2::upload_image( $request, $files);
                    if ($image['status'] != 'success') {
                        return array(
                            "status" => $image['status'],
                            "message" => $image['message']
                        );
                    }
                }

                if (!empty($files['avatar']['name'])) {
                    $posts["avatar"] = $image["data"][0]["avatar_id"];
                }else{
                    $posts["avatar"] = $check_category->avatar;
                }
            }


            $wpdb->query("START TRANSACTION");

            $category = $wpdb->query("INSERT
                INTO
                    $table_store_categories
                        (`hsid`, $table_store_categories_field, `status`)
                VALUES
                    ('$check_category->hsid', '{$posts["title"]}', '{$posts["info"]}', '{$posts["groups"]}', '{$posts["avatar"]}', '$check_category->created_by', '$check_category->status') ");
            
            $category = $wpdb->insert_id;

            if ($category < 1) {
                $wpdb->query("ROLLBACK");
                return array(
                    "status" => "failed",
                    "message" => "An error occured while submitting data to server"
                );

            }else{
                $wpdb->query("COMMIT");
                return array(
                    "status" => "success",
                    "message" => "Data has been updated successfully"
                );

            }
        }
    }