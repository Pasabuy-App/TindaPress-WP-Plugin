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
            $table_store = TP_STORES_TABLE;
            $table_revs = TP_REVISIONS_TABLE;

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
            $store_data = $wpdb->get_row("SELECT tp_str.* FROM $table_store tp_str INNER JOIN $table_revs tp_revs ON tp_revs.ID = tp_str.`status` WHERE tp_str.ID = '{$user["store_id"]}' AND tp_revs.child_val = 1 ");
               
            // Step6 :  Check if failed
            if (!$store_data) {
                return array(
                    "status" => "failed",
                    "message" => "An error occured while fetching data to database.",
                );
            }

            // Step7 :  Query
            $result = $wpdb->query("UPDATE $table_revs SET `child_val` = '0' WHERE ID = $store_data->status ");

            // Step8 :  Check if failed
            if ($result < 1 ) {
                return array(
                    "status" => "failed",
                    "message" => "An error occured while submmiting data to database.",
                );
            } else{
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