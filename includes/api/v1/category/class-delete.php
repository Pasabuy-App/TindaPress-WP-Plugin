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
    class TP_Category_Delete {
        
        public static function listen(){
            return rest_ensure_response( 
                TP_Category_Delete:: delete_category()
            );
        }
        
        
        public static function delete_category(){
           
            global $wpdb;

            //Check if prerequisites plugin are missing
            $plugin = TP_Globals::verify_prerequisites();
            if ($plugin !== true) {
                return array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. ".$plugin." plugin missing!",
                );
            }
			
			//  Step2 : Validate if user is exist
			if (DV_Verification::is_verified() == false) {
                return array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Verification issues!",
                );
                
            }
            
            if (!isset($_POST["catid"])  ) {
				return array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Request unknown!",
                );
            }

            if (empty($_POST["catid"])  ) {
				return array(
						"status" => "failed",
						"message" => "Required fields cannot be empty.",
                );
            }

            $category_id = $_POST["catid"];
            $table_revs = TP_REVISION_TABLE;
            $table_categories = TP_CATEGORIES_TABLE;

            $category = $wpdb->get_row("SELECT `status` FROM $table_categories WHERE `id` = $category_id");

            if ( !$category ) {
				return array(
						"status" => "failed",
						"message" => "This category does not exists",
                );
            }

            $status_id = $category->status;

            $result = $wpdb->query("UPDATE $table_revs SET `child_val` = 0 WHERE `ID` = $status_id");

            if ($result < 1) {
                return array(
                    "status" => "error",
                    "message" => "An error occured while submitting data to the server.",
                );
            }

            return array(
                "status" => "success",
                "message" => "Data has been deleted successfully.",
            );

        
        }

    }