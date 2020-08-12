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

    class TP_Store_Delete_Address {
        
        //REST API Call
        public static function listen(){
            return rest_ensure_response( 
                TP_Store_Delete_Address:: listen_open()
            );
        }

        public static function listen_open (){
            global $wpdb;
            
            $date_created = TP_Globals::date_stamp();
            
            $dv_rev_table = DV_REVS_TABLE;
            $table_address = DV_ADDRESS_TABLE;
                 
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

            if (!isset($_POST['addr'])) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Request unknown!",
                );
            }
            
            if (empty($_POST['addr'])) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Request unknown!",
                );
            }
            
            if (!is_numeric($_POST['addr'])) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. ID is not in valid format",
                );
            }

            $address_id = $_POST["addr"];
            
            $get_address_status = $wpdb->get_row("SELECT `child_val`as `status` FROM $dv_rev_table WHERE ID = (SELECT `status` FROM $table_address WHERE ID = $address_id  ) ");
            if (!$get_address_status) {
                return array(
                    "status" => "failed",
                    "message" => "This address does not exists..",
                );
            }
            if ($get_address_status->status != 1) {
                return array(
                    "status" => "failed",
                    "message" => "This address is already deactivated..",
                );
            }

            $wpdb->query("START TRANSACTION");
               $get_address_data =  $wpdb->get_row("SELECT * FROM $table_address WHERE ID = $address_id");

                $result = $wpdb->query("UPDATE $dv_rev_table SET `child_val` = 0, `date_created` = '$date_created' WHERE ID = $get_address_data->status   ");

            if ($result < 1 || empty($get_address_data) ) {
                $wpdb->query("ROLLBACK");

                return array(
                    "status" => "failed",
                    "message" => "An error occured while submitting data to database.",
                );

            }else{
                $wpdb->query("COMMIT");
                
                return array(
                    "status" => "success",
                    "message" => "Data has been deleted successfully.",
                );

            }

        }
    }