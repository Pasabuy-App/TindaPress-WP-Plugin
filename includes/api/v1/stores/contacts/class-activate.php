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
    class TP_Store_Activate_Contacts {

        public static function listen(){
            return rest_ensure_response( 
                TP_Store_Activate_Contacts:: listen_open()
            );
        }

        public static function listen_open (){

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
            if ( !isset($_POST["stid"]) || !isset($_POST["cid"]) ) {
                return array(
                    "status" => "failed",
                    "message" => "Please contact your administrator. Request Unknown!",
                );
            }

            // Step 4: Check if parameters passed are not null
            if ( empty($_POST["stid"]) || empty($_POST["cid"]) ) {
                return array(
                    "status" => "failed",
                    "message" => "Required fields cannot be empty.",
                );
            }

            // Step 8: Pass post to variable
            $stid = $_POST["stid"];
            $cid = $_POST["cid"];
            $date_created = TP_Globals::date_stamp();

            $table_contact = DV_CONTACTS_TABLE;

            // Step 9: Check if contact is exists using contact id, store id and types
            $val_contact = $wpdb->get_row("SELECT ID, status FROM $table_contact  WHERE ID = '$cid' AND stid = '$stid' ");    
            if ( !$val_contact ) {
                return array(
                        "status" => "failed",
                        "message" => "This contact does not exists.",
                );
            }   
            if ( $val_contact->status === '1' ) {
                return array(
                        "status" => "failed",
                        "message" => "This contact has already been activated.",
                );
            }
            
            // Step 10: Insert query for revision and Update query for contacts
            $update_contact = $wpdb->query("UPDATE $table_contact SET `status` = '1' WHERE ID = $cid AND stid = '$stid' ");

            // Step 11: Check result
            if ($update_contact < 1) {
                return array(
                    "status" => "failed",
                    "message" => "An error occured while submitting data to database.",
                );
                
            }

            return array(
                "status" => "success",
                "message" => "Data has been successfully activated.",
            );

        }
    }
