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
    class TP_Store_Update_Contacts {
        
        public static function listen(){
            return rest_ensure_response( 
                TP_Store_Update_Contacts:: listen_open()
            );
        }

        public static function listen_open (){

            // 2nd Initial QA 2020-08-24 8:08 PM - Miguel
            global $wpdb;

            $plugin = TP_Globals::verify_prerequisites();
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
                    "message" => "Please contact your administrator. Verification issues!",
                );
            }

            // Step 3: Check if parameters are passed
            if (!isset($_POST["stid"]) || !isset($_POST["cid"]) || !isset($_POST["type"]) || !isset($_POST["val"]) ) {
                return array(
                    "status" => "failed",
                    "message" => "Please contact your administrator. Request Unknown!",
                );
            }

            // Step 4: Check if parameters passed are not null
            if (empty($_POST["stid"]) || empty($_POST["cid"]) || empty($_POST["type"]) || empty($_POST["val"]) ) {
                return array(
                    "status" => "failed",
                    "message" => "Required fields cannot be empty.",
                );
            }

            // Step 5: Check if parameters are valid type
            if (!($_POST["type"] === 'phone') && !($_POST["type"] === 'email') ) {
                return array(
                    "status" => "failed",
                    "message" => "Invalid value for type.",
                );
            }

            // Step 6: Check if email type is valid email format
            if ($_POST["type"] === 'email')  {
                if (!is_email($_POST['val'])) {
                    return array(
                        "status" => "failed",
                        "message" => "Email not in valid format."
                    );
                }
            }

            // Step 7: Check if phone type is numeric 
            if ($_POST["type"] === 'phone')  {
                if (!is_numeric($_POST['val'])) {
                    return array(
                        "status" => "failed",
                        "message" => "Phone not in valid format."
                    );
                }
            }

            // Step 8: Pass post to variable
            $stid = $_POST["stid"];
            $cid = $_POST["cid"];
            $type = $_POST["type"];
            $val = $_POST["val"];
            $date_created = TP_Globals::date_stamp();
            $table_contact = DV_CONTACTS_TABLE;
            $table_dv_revsions = DV_REVS_TABLE;

            // Step 9: Check if contact is exists using contact id, store id and types
            $val_contact = $wpdb->get_row("SELECT ID, status, (SELECT child_val FROM $table_dv_revsions WHERE ID = revs) AS val FROM $table_contact  WHERE ID = '$cid' AND stid = '$stid' AND types = '$type' ");    
            if ( !$val_contact ) {
                return array(
                        "status" => "failed",
                        "message" => "This contact does not exists.",
                );
            }  

            if ( $val_contact->status === '0' ) {
                return array(
                        "status" => "failed",
                        "message" => "This contact does not exist.",
                );
            } 
            
            if ( $val_contact->val === $val ) {
                return array(
                        "status" => "failed",
                        "message" => "You already input this value.",
                );
            }
            
            // Step 10: Insert query for revision and Update query for contacts
            $wpdb->query("INSERT INTO $table_dv_revsions (revs_type, parent_id, child_key, child_val, created_by, date_created) 
            VALUES ( 'contacts', $cid, '$type', '$val', '$wpid', '$date_created'  )");
            $last_id = $wpdb->insert_id;
            
            $update_contact = $wpdb->query("UPDATE $table_contact SET `revs` = $last_id WHERE ID = $cid AND stid = '$stid' ");

            // Step 11: Check result
            if ($last_id < 1 || $update_contact < 1) {
                return array(
                    "status" => "failed",
                    "message" => "An error occured while submitting data to database.",
                );
            }else{
                return array(
                    "status" => "success",
                    "message" => "Data has been successfully updated.",
                );
            }
        }
    }
