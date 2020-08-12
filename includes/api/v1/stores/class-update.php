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
                TP_Update_Store:: list_open()
            );
        }

        public static function list_open(){

            global $wpdb;

            $user = TP_Update_Store::catch_post();
            $later = TP_Globals::date_stamp();

            // declaring table names to variable
            $table_store = TP_STORES_TABLE;
            $table_store_fields = TP_STORES_FIELDS;
            $table_revs = TP_REVISIONS_TABLE;
            $table_revs_fields = TP_REVISION_FIELDS;

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
            if (!isset($_POST["wpid"]) 
                || !isset($_POST["ctid"]) 
                || !isset($_POST["title"]) 
                || !isset($_POST["short_info"]) 
                || !isset($_POST["long_info"]) 
                || !isset($_POST["logo"]) 
                || !isset($_POST["banner"]) 
                // NEW
                || !isset($_POST["phone"]) 
                || !isset($_POST["email"]) 
                || !isset($_POST["st"]) 
                || !isset($_POST["co"]) 
                || !isset($_POST["pv"]) 
                || !isset($_POST["ct"]) 
                || !isset($_POST["bg"]) 
                ) {
				return array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Request unknown!",
                );
            }

            // Step4 : Sanitize all variable is empty
            if (empty($_POST["wpid"]) 
                || empty($_POST["ctid"]) 
                || empty($_POST["title"]) 
                || empty($_POST["short_info"]) 
                || empty($_POST["long_info"]) 
                || empty($_POST["logo"]) 
                || empty($_POST["banner"]) 
                // NEW
                || !isset($_POST["phone"]) 
                || !isset($_POST["email"]) 
                || !isset($_POST["st"]) 
                || !isset($_POST["co"]) 
                || !isset($_POST["pv"]) 
                || !isset($_POST["ct"]) 
                || !isset($_POST["bg"]) 
                ) {
                return array(
                        "status" => "failed",
                        "message" => "Required fields cannot be empty.",
                );
            }   

             $get_store = $wpdb->get_row("SELECT ID FROM tp_stores  WHERE ID = '{$user["store_id"]}' ");
                
            // Step5 : Check if this store id exists
            if ( !$get_store ) {
                return array(
                        "status" => "failed",
                        "message" => "This store does not exists.",
                );
            }

            // Step6 : Query
            $wpdb->query("START TRANSACTION");

                //get country id
                $get_country = $wpdb->get_row("SELECT ID FROM dv_geo_countries WHERE `country_code` = '$country_code'");


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


                // Query of store contact.
                // Phone
                $wpdb->query();
                $wpdb->query("INSERT INTO `$table_dv_revs` (revs_type, parent_id, child_key, child_val, created_by, date_created) 
                                                VALUES ( 'contacts', 0, 'phone', '{$user["phone"]}', '{$user["created_by"]}', '$date_created'  )");
                $phone_last_id = $wpdb->insert_id;

                $wpdb->query("INSERT INTO `$table_contact` (`status`, `types`, `revs`, `stid`, `created_by`, `date_created`) 
                                                    VALUES ('1', 'phone', '$phone_last_id', $store_id, '{$user["created_by"]}', '$date_created');");
                $contact_phone_id = $wpdb->insert_id;
                
                $update_contact_phone = $wpdb->query("UPDATE `$table_dv_revs` SET `parent_id` = $contact_phone_id WHERE ID = $phone_last_id ");

                // Email
                $wpdb->query("INSERT INTO `$table_dv_revs` (revs_type, parent_id, child_key, child_val, created_by, date_created) 
                                                VALUES ( 'contacts', 0, 'email', '{$user["phone"]}', '{$user["created_by"]}', '$date_created'  )");
                $email_last_id = $wpdb->insert_id;

                $wpdb->query("INSERT INTO `$table_contact` (`status`, `types`, `revs`, `stid`, `created_by`, `date_created`) 
                                                    VALUES ('1', 'email', '$email_last_id', $store_id, '{$user["created_by"]}', '$date_created');");
                $contact_email_id = $wpdb->insert_id;
                
                $update_contact_email = $wpdb->query("UPDATE `$table_dv_revs` SET `parent_id` = $contact_email_id WHERE ID = $email_last_id ");

                // End of store contact query

            // Step7 : Check if failed
            if ( $title < 1 || $short_info < 1 || $long_info < 1 || $logo < 1 || $banner < 1 ) {
            $wpdb->query("ROLLBACK");
                return array(
                    "status" => "failed",
                    "message" => "An error occured while submitting data to database.",
                );
            }

            // Step8 : Query
            $update_store = $wpdb->query("UPDATE $table_store SET `title` = $title, `short_info` = $short_info, `long_info` = $long_info, `logo` = $logo, `banner` = $banner WHERE ID = '{$user["store_id"]}' ");

            $result = $wpdb->query("UPDATE $table_revs SET `parent_id` =  '{$user["store_id"]}' WHERE ID IN ($title, $short_info, $long_info, $logo, $banner) ");
            
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
                $cur_user['ctid']       = $_POST["ctid"];
                $cur_user['address']    = $_POST["address"];
                $cur_user['store_id']   = $_POST["stid"];
                
                $cur_user['title']      = $_POST["title"];
                $cur_user['short_info'] = $_POST["short_info"];
                $cur_user['long_info']  = $_POST["long_info"];
                $cur_user['logo']       = $_POST["logo"];
                $cur_user['banner']     = $_POST["banner"];
  
              return  $cur_user;
        }
    }