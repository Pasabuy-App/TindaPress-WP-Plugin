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

    class TP_Store_Insert_Contacts {
        public static function listen(){
            return rest_ensure_response( 
                TP_Store_Insert_Contacts:: listen_open()
            );
        }

        public static function listen_open (){

            // 2nd Initial QA 2020-08-24 8:00 PM - Miguel
            global $wpdb;

            $table_contact = DV_CONTACTS_TABLE;
            $table_dv_revsions = DV_REVS_TABLE;

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

            if (!isset($_POST["email"]) || !isset($_POST["phone"]) ) {
                return array(
                    "status" => "failed",
                    "message" => "Email not in valid format."
                );
            }

            if (empty($_POST["email"]) || empty($_POST["phone"]) ) {
                return array(
                    "status" => "failed",
                    "message" => "Email not in valid format."
                );
            }

            if (!is_email($_POST['email'])) {
                return array(
                    "status" => "failed",
                    "message" => "Email not in valid format."
                );
            }

            $user = TP_Store_Insert_Contacts::catch_post();
            $date_created = TP_Globals::date_stamp();

            $get_store = $wpdb->get_row("SELECT
                tp_stores.ID
                FROM
                tp_stores
                INNER JOIN tp_revisions rev ON tp_stores.`status` = rev.ID
                WHERE rev.`revs_type` = 'stores' AND rev.child_key ='status' AND rev.child_val = 1 AND rev.ID =  ( SELECT MAX(ID) FROM tp_revisions WHERE ID = rev.ID   ) AND tp_stores.ID = '{$user["store_id"]}'");
            
            // Step5 : Check if this store id exists
            if ( empty($get_store) ) {
                return array(
                    "status" => "failed",
                    "message" => "This store does not exists.",
                );
            }

            $wpdb->query("START TRANSACTION");
                // Query of store contact.
                // Phone
                $wpdb->query("INSERT INTO `$table_dv_revsions` (revs_type, parent_id, child_key, child_val, created_by, date_created) 
                                                VALUES ( 'contacts', 0, 'phone', '{$user["phone"]}', '{$user["created_by"]}', '$date_created'  )");
                $phone_last_id = $wpdb->insert_id;

                $wpdb->query("INSERT INTO `$table_contact` (`status`, `types`, `revs`, `stid`, `created_by`, `date_created`) 
                                                    VALUES ('1', 'phone', '$phone_last_id', '{$user["store_id"]}', '{$user["created_by"]}', '$date_created');");
                $contact_phone_id = $wpdb->insert_id;
                
                $update_contact_phone = $wpdb->query("UPDATE `$table_dv_revsions` SET `parent_id` = $contact_phone_id WHERE ID = $phone_last_id ");

                // Email
                $wpdb->query("INSERT INTO `$table_dv_revsions` (revs_type, parent_id, child_key, child_val, created_by, date_created) 
                                                VALUES ( 'contacts', 0, 'email', '{$user["email"]}', '{$user["created_by"]}', '$date_created'  )");
                $email_last_id = $wpdb->insert_id;

                $wpdb->query("INSERT INTO `$table_contact` (`status`, `types`, `revs`, `stid`, `created_by`, `date_created`) 
                                                    VALUES ('1', 'email', '$email_last_id', '{$user["store_id"]}', '{$user["created_by"]}', '$date_created');");
                $contact_email_id = $wpdb->insert_id;
                
                $update_contact_email = $wpdb->query("UPDATE `$table_dv_revsions` SET `parent_id` = $contact_email_id WHERE ID = $email_last_id ");

                // End of store contact query
            if ($phone_last_id < 1 || $contact_phone_id < 1 || $update_contact_phone < 1 || $email_last_id < 1 || $contact_email_id < 1 || $update_contact_email < 1) {
                $wpdb->query("ROLLBACK");
                return array(
                    "status" => "failed",
                    "message" => "An error occured while submitting data to database.",
                );
                
            }else{
                $wpdb->query("COMMIT");
                return array(
                    "status" => "success",
                    "message" => "Data has been successfully added.",
                );
            }
        }

        public static function catch_post(){
            $cur_user = array();
            $cur_user["created_by"] = $_POST["wpid"];
            $cur_user["store_id"] = $_POST["stid"];

            $cur_user["phone"] = $_POST["phone"];
            $cur_user["email"] = $_POST["email"];

            return  $cur_user;
        }
    }
