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
            global $wpdb;

            
            //Check if prerequisites plugin are missing
            $plugin = TP_Globals::verify_prerequisites();
            if ($plugin !== true) {
                return array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. ".$plugin." plugin missing!",
                );
            }
            
            // Step2 : Check if wpid and snky is valid
            if (DV_Verification::is_verified() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Verification Issues!",
                    )
                );
            }

            if (!isset($_POST["stid"])) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Missing paramiters.",
                );
            }

            
            if (empty($_POST["stid"])) {
                return array(
                    "status" => "failed",
                    "message" => "Required fields cannot be empty",
                );
            }

            
            
            $user = TP_Delete_Store::catch_post();
            

            $store_data = $wpdb->get_row("SELECT tp_stores.* FROM tp_stores INNER JOIN tp_revisions ON tp_revisions.ID = tp_stores.`status` WHERE tp_stores.ID = '17' AND tp_revisions.child_val = 1 ");
            
            if (!$store_data) {
                return array(
                    "status" => "failed",
                    "message" => "An error occured while fetching data to database.",
                );
            }

            $result = $wpdb->query("UPDATE tp_revisions SET `child_val` = '0' WHERE ID = $store_data->status ");

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