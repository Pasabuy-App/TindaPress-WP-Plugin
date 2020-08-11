<?php
	// Exit if accessed directly
	if ( ! defined( 'ABSPATH' ) ) 
	{
		exit;
	}

	/** 
        * @package tindapress-wp-plugin
        * @version 0.1.0
	*/
    class TP_Insert_Store {

        public static function listen(){
            return rest_ensure_response( 
                TP_Insert_Store::add_store()
            );
        }
        
        public static function add_store(){

            global $wpdb;

            $later = TP_Globals::date_stamp();
            $user = TP_Insert_Store::catch_post();

            // declaring table names to variable
            $table_store = TP_STORES_TABLE;
            $table_store_fields = TP_STORES_FIELDS;
            $table_revs = TP_REVISIONS_TABLE;
            $table_revs_fields = TP_REVISION_FIELDS;
            $revs_type = "stores";

            // Step1 : Check if prerequisites plugin are missing
            $plugin = TP_Globals::verify_prerequisites();
            if ($plugin !== true) {
                return array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. ".$plugin." plugin missing!",
                );
            }

            // Step2 : Check if wpid and snky is valid
            if (DV_Verification::is_verified() == false) {
                return array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Verification issues!",
                );
            }

            // Step3 : Sanitize all request
            if (!isset($_POST["wpid"]) 
                || !isset($_POST["title"]) 
                || !isset($_POST["short_info"]) 
                || !isset($_POST["long_info"]) 
                || !isset($_POST["logo"]) 
                || !isset($_POST["banner"]) 
                ) {
				return array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Request unknown!",
                ); 
            }

            // Step4 : Sanitize all variable is empty
            if (empty($_POST["wpid"]) 
                || empty($_POST["title"]) 
                || empty($_POST["short_info"]) 
                || empty($_POST["long_info"]) 
                || empty($_POST["logo"]) 
                || empty($_POST["banner"]) 
            ) {
                return array(
                        "status" => "unknown",
                        "message" => "Required fields cannot be empty.",
                );
            }

            // Step5 : Validate permission
            $permission = TP_Globals::verify_role($_POST['wpid'], '0', 'can_add_store' );
            if ($permission == true) {
                return array(
                    "status" => "failed",
                    "message" => "Current user has no access in manipulation of data.",
                );
            }

            // Step6 : Check user role 
            if (TP_Globals::verify_role( $_POST['wpid'], '0', 'can_add_store' )) {
                return array( 
                    TP_Globals::verify_role($_POST['wpid'], '0', 'can_add_store' ),
                );
            }

            // Step7 : Query
            $wpdb->query("START TRANSACTION");

                $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('$revs_type', '0', 'title', '{$user["title"]}', '{$user["created_by"]}', '$later')");
                $title = $wpdb->insert_id;

                $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('$revs_type', '0', 'short_info', '{$user["short_info"]}', '{$user["created_by"]}', '$later')");
                $short_info = $wpdb->insert_id;

                $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('$revs_type', '0', 'long_info', '{$user["long_info"]}', '{$user["created_by"]}', '$later')");
                $long_info = $wpdb->insert_id;

                $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('$revs_type', '0', 'logo', '{$user["logo"]}', '{$user["created_by"]}', '$later')");
                $logo = $wpdb->insert_id;

                $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('$revs_type', '0', 'banner', '{$user["banner"]}', '{$user["created_by"]}', '$later')");
                $banner = $wpdb->insert_id;

                $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('$revs_type', '0', 'status', '1', '{$user["created_by"]}', '$later')");
                $status = $wpdb->insert_id;

            // Step8 : Check if failed
            if ( $title < 1 || $short_info < 1 || $long_info < 1 || $logo < 1 || $banner < 1 || $status < 0 ) {
                $wpdb->query("ROLLBACK");
                return array(
                    "status" => "failed",
                    "message" => "An error occured while submitting data to database.",
                );
            }

            // Step9 : Query
            $wpdb->query("INSERT INTO $table_store $table_store_fields VALUES ('{$user["ctid"]}', $title, $short_info, $long_info, $logo, $banner, $status, '{$user["address"]}', '{$user["created_by"]}', '$later' )");
            $store_id = $wpdb->insert_id;

            $result = $wpdb->query("UPDATE $table_revs SET `parent_id` = $store_id WHERE ID IN ($title, $short_info, $long_info, $logo, $banner, $status) ");
            
            // Step10 : Check if failed
            if ($store_id < 1 || $result < 1 ) {
                $wpdb->query("ROLLBACK");
                return array(
                    "status" => "failed",
                    "message" => "An error occured while submitting data to database.",
                );
            }else{
                $wpdb->query("COMMIT");
                return array(
                    "status" => "success",
                    "message" => "Data has been added successfully.",
                );
            }
        }

        // Catch Post 
        public static function catch_post()
        {
              $cur_user = array();
               
                $cur_user['created_by'] = $_POST["wpid"];
                $cur_user['ctid']       = $_POST["ctid"];
                $cur_user['address']    = $_POST["address"];
                $cur_user['title']      = $_POST["title"];
                $cur_user['short_info'] = $_POST["short_info"];
                $cur_user['long_info']  = $_POST["long_info"];
                $cur_user['logo']        = $_POST["logo"];
                $cur_user['banner']      = $_POST["banner"];
  
              return  $cur_user;
        }
    }