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
    class TP_Category_Select {
        
        public static function listen(){
            return rest_ensure_response( 
                TP_Category_Select:: select()
            );
        }
        
        public static function select(){
            
            //Inital QA done 2020-08-11 10:30 AM
            global $wpdb;
            $table_revs = TP_REVISIONS_TABLE;
            $table_categories = TP_CATEGORIES_TABLE;

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
            
            // Step 3: Check if parameters are passed
            if (!isset($_POST["catid"])  ) {
				return array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Request unknown!",
                );
            }

            // Step 4: Check if parameters passed are not null
            if (empty($_POST["catid"])  ) {
				return array(
						"status" => "failed",
						"message" => "Required fields cannot be empty.",
                );
            }

            $category_id = $_POST['catid'];

            // Step 5: Start mysql query
            $category = $wpdb->get_row("SELECT
                cat.ID, cat.types,
                ( SELECT rev.child_val FROM $table_revs rev WHERE ID = cat.title ) AS title,
                ( SELECT rev.child_val FROM $table_revs rev WHERE ID = cat.info ) AS info,
                ( SELECT rev.child_val FROM $table_revs rev WHERE ID = cat.status) as status
            FROM
                $table_categories cat
            INNER JOIN 
                $table_revs rev ON rev.parent_id = cat.id 
            WHERE
                cat.id = $category_id");
            
            // Step 6: Check results if empty
            if (!$category) {
                return array(
                    "status" => "failed",
                    "message" => "This category does not exists.",
                );
            }

            if ($category->status == 0) {
                return array(
                    "status" => "failed",
                    "message" => "This category is currently inactive.",
                );
            }

            // Step 7: Return a success status and message 
            return array(
                "status" => "success",
                "data" => $category
            );
        
        }

    }