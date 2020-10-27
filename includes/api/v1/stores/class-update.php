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
    class TP_Update_Store {

        public static function listen(){
            return rest_ensure_response(
                self:: list_open()
            );
        }

        public static function list_open(){

            // 2nd Initial QA 2020-08-24 11:02 PM - Miguel
            global $wpdb;

            $later = TP_Globals::date_stamp();
            // declaring table names to variable
            $table_store = TP_STORES_TABLE;
            $table_store_fields = TP_STORES_FIELDS;
            $table_revs = TP_REVISIONS_TABLE;
            $table_revs_fields = TP_REVISION_FIELDS;
            $table_dv_revs = DV_REVS_TABLE;
            $table_contact = DV_CONTACTS_TABLE;
            // declaring variable
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
                    "message" => "Please contact your administrator. Verification Issues!",
                );
            }

            // Step3 : Sanitize all request
            if (!isset($_POST["title"])
                || !isset($_POST["short_info"])
                || !isset($_POST["long_info"])
                ) {
				return array(
					"status" => "unknown",
					"message" => "Please contact your administrator. Request unknown!",
                );
            }

            // Step4 : Sanitize all variable is empty
            if (empty($_POST["title"])
                || empty($_POST["short_info"])
                || empty($_POST["long_info"])
                ) {
                return array(
                    "status" => "failed",
                    "message" => "Required fields cannot be empty.",
                );
            }

            //  $get_store = $wpdb->get_row("SELECT ID FROM tp_stores  WHERE ID = '{$user["store_id"]}' ");

            // // Step5 : Check if this store id exists
            // if ( !$get_store ) {
            //     return array(
            //         "status" => "failed",
            //         "message" => "This store does not exists.",
            //     );
            // }

            $user = self::catch_post();

            // Step6 : Query
            $wpdb->query("START TRANSACTION");

                //get country id

                $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('$revs_type', '0', 'title', '{$user["title"]}', '{$user["created_by"]}', '$later')");
                $title = $wpdb->insert_id;

                $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('$revs_type', '0', 'short_info', '{$user["short_info"]}', '{$user["created_by"]}', '$later')");
                $short_info = $wpdb->insert_id;

                $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('$revs_type', '0', 'long_info', '{$user["long_info"]}', '{$user["created_by"]}', '$later')");
                $long_info = $wpdb->insert_id;

            // Step7 : Check if failed
            if ( $title < 1 || $short_info < 1 || $long_info < 1  ) {
            $wpdb->query("ROLLBACK");
                return array(
                    "status" => "failed",
                    "message" => "An error occured while submitting data to database.",
                );
            }

            // Step8 : Query
            $update_store = $wpdb->query("UPDATE $table_store SET `title` = $title, `short_info` = $short_info, `long_info` = $long_info WHERE ID = '{$user["store_id"]}' ");

            $result = $wpdb->query("UPDATE $table_revs SET `parent_id` =  '{$user["store_id"]}' WHERE ID IN ($title, $short_info, $long_info) ");

            // Step9 : Check if failed
            if ($result < 1 ) {
                $wpdb->query("ROLLBACK");
                return array(
                    "status" => "failed",
                    "message" => "An error occured while submitting data to database.",
                );

            }else{
                $wpdb->query("COMMIT");
                return array(
                    "status" => "success",
                    "message" => "Data has been updated successfully.",
                );
            }
        }

        // Catch Post
        public static function catch_post()
        {
            $cur_user = array();

            $cur_user['created_by'] = $_POST["wpid"];
            $cur_user['store_id']   = $_POST["stid"];

            $cur_user['title']      = $_POST["title"];
            $cur_user['short_info'] = $_POST["short_info"];
            $cur_user['long_info']  = $_POST["long_info"];

            return  $cur_user;
        }
    }