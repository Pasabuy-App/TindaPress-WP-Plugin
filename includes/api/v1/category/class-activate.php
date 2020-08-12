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
    class TP_Category_Activate {
        
        public static function listen(){
            return rest_ensure_response( 
                TP_Category_Activate:: activate_category()
            );
        }
        
        public static function activate_category(){
            
            //Inital QA done 2020-08-11 10:55 AM
            global $wpdb;
            $category_id = $_POST["catid"];
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

            //Check if user has roles_access of can_activate_store or either contributor or editor
            $permission = TP_Globals::verify_role($_POST['wpid'], '0', 'can_activate_store' );
            
            if ($permission == true) {
                return array(
                    "status" => "failed",
                    "message" => "Current user has no access in activating category.",
                );
            }
            
            // Step 5: Get status of this category
            $category = $wpdb->get_row("SELECT cat.ID, cat.types, cat.status as status_id,
                ( SELECT rev.child_val FROM $table_revs rev WHERE ID = cat.status) as status
                FROM
                    $table_categories cat
                INNER JOIN
                    $table_revs rev ON rev.parent_id = cat.id
                WHERE 
                    cat.id = $category_id
                GROUP BY
                    cat.id
            ");

            // Step 6: Check if this category id exists
            if ( !$category ) {
				return array(
						"status" => "failed",
						"message" => "This category does not exists.",
                );
            }

            if ( $category->status == 1) {
				return array(
						"status" => "failed",
						"message" => "This category is already activated.",
                );
            }
            
            $status_id = $category->status_id;

            // Step 7: Activate this category by setting its status to 1
            $result = $wpdb->query("UPDATE $table_revs SET `child_val` = 1 WHERE `ID` = $status_id");
            
            // Step 8: Check if there's problem in query
            if ($result < 1) {
                return array(
                    "status" => "error",
                    "message" => "An error occured while submitting data to the server.",
                );
            }

            // Step 9: Return a success status and message 
            return array(
                "status" => "success",
                "message" => "Data has been activated successfully.",
            );

        
        }

    }