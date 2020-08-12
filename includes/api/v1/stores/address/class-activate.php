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

    //Qa done 2020-08-12 9:13 pm
    class TP_Store_Activate_Address {
        
        //REST API Call
        public static function listen(){
            return rest_ensure_response( 
                TP_Store_Activate_Address:: listen_open()
            );
        }

        public static function listen_open (){
            global $wpdb;
            
            $date_created = TP_Globals::date_stamp();
            
            $dv_rev_table = DV_REVS_TABLE;
            $table_address = DV_ADDRESS_TABLE;
                 
            // Step 1: Check if prerequisites plugin are missing
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

            // Step 3: Check if required parameters are passed
            if (!isset($_POST['addr'])) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Request unknown!",
                );
            }
            
            // Step 4: Check if parameters passed are empty
            if (empty($_POST['addr'])) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Request unknown!",
                );
            }
            

            $address_id = $_POST["addr"];
            // Step 5: Check if this address if exists in database.
            $get_address_status = $wpdb->get_row("SELECT `child_val`as `status` FROM $dv_rev_table WHERE ID = (SELECT `status` FROM $table_address WHERE ID = $address_id  ) ");
            if (!$get_address_status) {
                return array(
                    "status" => "failed",
                    "message" => "This address does not exists..",
                );
            }

            // Step 6: Check if this address is already activated.
            if ($get_address_status->status != 0) {
                return array(
                    "status" => "failed",
                    "message" => "This address is already activated..",
                );
            }

            // Step 7: Start mysql transaction
            $wpdb->query("START TRANSACTION");
               $get_address_data =  $wpdb->get_row("SELECT * FROM $table_address WHERE ID = $address_id");

                $result = $wpdb->query("UPDATE $dv_rev_table SET `child_val` = 1, `date_created` = '$date_created' WHERE ID = $get_address_data->status   ");

            // Step 8: Check if any queries above failed
            if ($result < 1 || empty($get_address_data) ) {
                //Do a rollback if any of the above queries failed
                $wpdb->query("ROLLBACK");
                return array(
                    "status" => "failed",
                    "message" => "An error occured while submitting data to database.",
                );

            }else{
                //Commit if no errors found
                $wpdb->query("COMMIT");
                return array(
                    "status" => "success",
                    "message" => "Data has been activated successfully.",
                );

            }

        }
    }