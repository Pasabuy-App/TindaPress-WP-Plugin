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

    class TP_Delete_Store {

        public static function listen(){
            return rest_ensure_response( 
                TP_Delete_Store::list_open()
            );
        }
        
        public static function list_open(){

            global $wpdb;
            
            $user = TP_Delete_Store::catch_post();

            // declaring table names to variable
                  // declaring table names to variable
            $table_store = TP_STORES_TABLE;
            $table_revisions = TP_REVISIONS_TABLE;

            $table_revision_field = TP_REVISION_FIELDS;

            $date_created = TP_Globals::date_stamp();


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
                        "message" => "Please contact your administrator. Verification Issues!",
                );
            }

            // Step3 : Sanitize request
            if (!isset($_POST["stid"])) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Missing paramiters.",
                );
            }
            
            // Step4 : Sanitize variable is empty
            if (empty($_POST["stid"])) {
                return array(
                    "status" => "failed",
                    "message" => "Required fields cannot be empty.",
                );
            }
            
            // Step5 :  Query
            $store_data = $wpdb->get_row("SELECT child_val as stats FROM tp_revisions WHERE ID = (SELECT `status` FROM tp_stores WHERE ID = '{$user["store_id"]}')");
               
            // Step6 :  Check if failed
            if (!$store_data) {
                return array(
                    "status" => "failed",
                    "message" => "This store does not exists..",
                );
            }

            if ($store_data->stats == 0) {
                return array(
                    "status" => "failed",
                    "message" => "This store is already deactivated.",
                );
            }

            $wpdb->query("START TRANSACTION");

                $get_last_value = $wpdb->get_row("SELECT
                    tp_rev.child_val AS title,
                    (select child_val from $table_revisions where id = tp_str.short_info) AS short_info,
                    (select child_val from $table_revisions where id = tp_str.long_info) AS long_info,
                    (select child_val from $table_revisions where id = tp_str.logo) AS logo,
                    (select child_val from $table_revisions where id = tp_str.banner) AS banner,
                    (select child_val from $table_revisions where id = tp_str.status) AS `status`
                FROM
                    $table_store tp_str
                INNER JOIN 
                    $table_revisions tp_rev ON tp_rev.ID = tp_str.title 
                WHERE 
                    tp_str.ID = '{$user["store_id"]}'
                ");

                $wpdb->query("INSERT INTO $table_revisions $table_revision_field VALUES ( 'stores', '{$user["store_id"]}', 'title', '$get_last_value->title', '{$user["created_by"]}', '$date_created'  ) ");
                $title = $wpdb->insert_id;

                $wpdb->query("INSERT INTO $table_revisions $table_revision_field VALUES ( 'stores', '{$user["store_id"]}', 'short_info', '$get_last_value->short_info', '{$user["created_by"]}', '$date_created'  ) ");
                $short_info = $wpdb->insert_id;

                $wpdb->query("INSERT INTO $table_revisions $table_revision_field VALUES ( 'stores', '{$user["store_id"]}', 'long_info', '$get_last_value->long_info', '{$user["created_by"]}', '$date_created'  ) ");
                $long_info = $wpdb->insert_id;

                $wpdb->query("INSERT INTO $table_revisions $table_revision_field VALUES ( 'stores', '{$user["store_id"]}', 'logo', '$get_last_value->logo', '{$user["created_by"]}', '$date_created'  ) ");
                $logo = $wpdb->insert_id;

                $wpdb->query("INSERT INTO $table_revisions $table_revision_field VALUES ( 'stores', '{$user["store_id"]}', 'banner', '$get_last_value->banner', '{$user["created_by"]}', '$date_created'  ) ");
                $banner = $wpdb->insert_id;

                $wpdb->query("INSERT INTO $table_revisions $table_revision_field VALUES ( 'stores', '{$user["store_id"]}', 'status', '0', '{$user["created_by"]}', '$date_created'  ) ");
                $status = $wpdb->insert_id;

                $update_store = $wpdb->query("UPDATE tp_stores SET `title` = '$title', `short_info` = '$short_info', `long_info` = '$long_info', `logo` = '$logo', `banner` = '$banner', `status` = '$status' WHERE ID = '{$user["store_id"]}' ");

            // Step8 :  Check if failed
            if ($title < 1 || $short_info < 1 || $long_info < 1 || $logo < 1 || $banner < 1 || $status < 1 || $update_store < 1 ) {
                $wpdb->query("ROLLBACK");

                return array(
                    "status" => "failed",
                    "message" => "An error occured while submmiting data to database.",
                );
            } else{
                $wpdb->query("COMMIT");

                return array(
                    "status" => "success",
                    "message" => "Data has been deleted successfully.",
                );
            }
        }  
        
        // Catch Post 
        public static function catch_post()
        {
              $cur_user = array();
               
                $cur_user['created_by'] = $_POST["wpid"];
                $cur_user['store_id']      = $_POST["stid"];
  
              return  $cur_user;
        }
    }