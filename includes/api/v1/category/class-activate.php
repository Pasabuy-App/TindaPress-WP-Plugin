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
            // 2nd Initial QA 2020-08-24 4:55 PM - Miguel
            global $wpdb;

            $table_revs = TP_REVISIONS_TABLE;
            $table_categories = TP_CATEGORIES_TABLE;
            $table_revs_fields = TP_REVISION_FIELDS;
            $date_created = TP_Globals::date_stamp();
            
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

            //Check if user has roles_access of can_delete_category or either contributor or editor
            $permission = TP_Globals::verify_role($_POST['wpid'], '0', 'can_delete_category' );
            
            if ($permission == true) {
                return array(
                    "status" => "failed",
                    "message" => "Current user has no access in deleting category.",
                );
            }
            
            // Step 3: Check if parameters are passed
            if ( !isset($_POST["catid"])  ) {
				return array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Request Unknown!",
                );
            }

            // Step 4: Check if parameters passed are not null
            if ( empty($_POST["catid"])  ) {
				return array(
						"status" => "failed",
						"message" => "Required fields cannot be empty.",
                );
            }

            $category_id = $_POST["catid"];
            $wpid = $_POST["wpid"];
            
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
            // Check if status is active
            if ( $category->status == 1 ) {
				return array(
						"status" => "failed",
						"message" => "This category is already activated.",
                );
            }

            $wpdb->query("START TRANSACTION");

                 $get_category_last_value = $wpdb->get_row("SELECT
                    cat.ID as catid,
                    cat.ID as stid,
                    cat.types,
                    ( SELECT child_val FROM $table_revs WHERE ID = cat.title ) AS `title`,
                    ( SELECT child_val FROM $table_revs WHERE ID = cat.info ) AS `info`,
                    ( SELECT child_val FROM $table_revs WHERE ID = cat.`status` ) AS `status`
                FROM
                    $table_categories cat 
                WHERE
                   cat.ID = $category_id");
                
                $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('categories', '$category_id', 'title', '$get_category_last_value->title', $wpid, '$date_created')");
                $title_id = $wpdb->insert_id;

                $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('categories', '$category_id', 'info', '$get_category_last_value->info', $wpid, '$date_created')");
                $info_id = $wpdb->insert_id;

                $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('categories', '$category_id', 'status', 1, $wpid, '$date_created')");
                $status_id = $wpdb->insert_id;

                $update_category = $wpdb->query("UPDATE $table_categories SET `title` = $title_id, `info` = $info_id, `status` = $status_id WHERE ID = $category_id ");

            // Step 8: Check if there's problem in query
            if (empty($get_category_last_value)  || $title_id < 1 || $info_id < 1 || $status_id < 1 ||  $update_category < 1) {
                $wpdb->query("ROLLBACK");
                return array(
                    "status" => "error",
                    "message" => "An error occured while submitting data to the server.",
                );

            }else{
                $wpdb->query("COMMIT");
                // Step 9: Return a success status and message 
                return array(
                    "status" => "success",
                    "message" => "Data has been activated successfully.",
                );
            }
        }
    }