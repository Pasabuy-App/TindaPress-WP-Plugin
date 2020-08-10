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
?>
<?php

    class TP_Insert_Store {
        public static function listen(){
            global $wpdb;

            // Step1 : check if datavice plugin is activated
            if (TP_Globals::verify_datavice_plugin() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Plugin Missing!",
                    )
                );
            }
            
            // Step2 : Check if wpid and snky is valid
            if (TP_Globals::validate_user() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Request Unknown!",
                    )
                );
            }

            if (!isset($_POST["wpid"]) 
                || !isset($_POST["ctid"]) 
                || !isset($_POST["title"]) 
                || !isset($_POST["short_info"]) 
                || !isset($_POST["long_info"]) 
                || !isset($_POST["logo"]) 
                || !isset($_POST["banner"]) 
                || !isset($_POST["address"]) ) {
				return rest_ensure_response( 
					array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Request unknown!",
					)
                );
                
            }

            if (empty($_POST["wpid"]) 
            || empty($_POST["ctid"]) 
            || empty($_POST["title"]) 
            || empty($_POST["short_info"]) 
            || empty($_POST["long_info"]) 
            || empty($_POST["logo"]) 
            || empty($_POST["banner"]) 
            || empty($_POST["address"]) ) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Required fields cannot be empty",
                    )
                );
            
            }

            if (  !is_numeric($_POST["ctid"]) || !is_numeric($_POST["address"]) ) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Id is not in Valid format!",
                );
            }

            $later = TP_Globals::date_stamp();
            
            $user = TP_Insert_Store::catch_post();

            // variables for query
            $table_store = TP_STORES_TABLE;
            $table_store_fields = TP_STORES_FIELDS;

            $table_revs = TP_REVISION_TABLE;
            $table_revs_fields = TP_REVISION_FIELDS;

            $revs_type = "stores";

            $wpdb->query("START TRANSACTION");

                $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('$revs_type', '0', 'title', '{$user["title"]}', '{$user["created_by"]}', '$later')");
                $title = $wpdb->insert_id;

                $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('$revs_type', '0', 'short_info', '{$user["title"]}', '{$user["created_by"]}', '$later')");
                $short_info = $wpdb->insert_id;

                $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('$revs_type', '0', 'long_info', '{$user["long_info"]}', '{$user["created_by"]}', '$later')");
                $long_info = $wpdb->insert_id;

                $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('$revs_type', '0', 'logo', '{$user["logo"]}', '{$user["created_by"]}', '$later')");
                $logo = $wpdb->insert_id;

                $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('$revs_type', '0', 'banner', '{$user["banner"]}', '{$user["created_by"]}', '$later')");
                $banner = $wpdb->insert_id;

                $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('$revs_type', '0', 'status', '1', '{$user["created_by"]}', '$later')");
                $status = $wpdb->insert_id;
                
                

            if ( $title < 1 || $short_info < 1 || $long_info < 1 || $logo < 1 || $banner < 1 || $status < 0 ) {
            $wpdb->query("ROLLBACK");
                return array(
                    "status" => "failed",
                    "message" => "An error occured while submitting data to database.",
                );
            }

            $wpdb->query("INSERT INTO $table_store $table_store_fields VALUES ('{$user["ctid"]}', $title, $short_info, $long_info, $logo, $banner, $status, '{$user["address"]}', '{$user["created_by"]}', '$later' )");
            $store_id = $wpdb->insert_id;

            $result = $wpdb->query("UPDATE $table_revs SET `parent_id` = $store_id WHERE ID IN ($title, $short_info, $long_info, $logo, $banner, $status) ");
            
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
                    "message" => "Store has successfully added.",
                );
            }

        }

        // Catch Post 
        public static function catch_post()
        {
              $cur_user = array();
               
                $cur_user['created_by'] = $_POST["wpid"];
                $cur_user['ctid']       = $_POST["ctid"];
                $cur_user['address']       = $_POST["address"];

                $cur_user['title']      = $_POST["title"];
                $cur_user['short_info'] = $_POST["short_info"];
                $cur_user['long_info']  = $_POST["long_info"];
                $cur_user['logo']        = $_POST["logo"];
                $cur_user['banner']      = $_POST["banner"];
            
  
              return  $cur_user;
        }
    }