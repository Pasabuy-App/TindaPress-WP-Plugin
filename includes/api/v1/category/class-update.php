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

    class TP_Category_Update {

        //REST API Call
        public static function listen(){
            
            return rest_ensure_response( 
                TP_Category_Update:: update_category()
            );
        }
        
        //Inserting Category function
        public static function update_category(){
            
            //Inital QA done 2020-08-11 09:56AM
            global $wpdb;
            $table_revs = TP_REVISIONS_TABLE;
            $table_revs_fields = TP_REVISION_FIELDS;
            $table_categories = TP_CATEGORIES_TABLE;
            $categories_fields = TP_CATEGORIES_FIELDS;
            $revs_type = "categories";
            $date = date('Y-m-d h:i:s');

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
            if (!isset($_POST["title"]) || !isset($_POST["info"]) || !isset($_POST["catid"])) {
				return array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Request unknown!",
                );
            }

            // Step 4: Check if parameters passed are not null
            if (empty($_POST["title"]) || empty($_POST["info"]) || empty($_POST["catid"]) ) {
				return array(
						"status" => "failed",
						"message" => "Required fields cannot be empty.",
                );
            }

            // Step 5: Catching post values

            $title = $_POST['title'];
            
            $info = $_POST['info'];

            $wpid = $_POST["wpid"]; 

            $category_id = $_POST["catid"];

            // Step 6: Check if this category exists
            $get_status = $wpdb->get_row("SELECT cat.ID, cat.types, cat.status as status_id,
                ( SELECT rev.child_val FROM $table_revs rev WHERE ID = cat.title ) AS title,
                ( SELECT rev.child_val FROM $table_revs rev WHERE ID = cat.info ) AS info,
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

            //Return a failed status if no rows found
            if (!$get_status) {
                return array(
                    "status" => "failed",
                    "message" => "This category does not exists.",
                );
            }

            //Check if category is active or inactive
            if ($get_status->status == 0) {
                return array(
                    "status" => "failed",
                    "message" => "This category is currently inactive.",
                );
            }

            $status_id = $get_status->status_id;

            // Step 7: Start mysql query
            $wpdb->query("START TRANSACTION");
                
                // Archiving the revision field
                $wpdb->query("UPDATE $table_revs SET `child_val` = 0 WHERE ID = $status_id ");

                $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('$revs_type', '0', 'title', '$title', $wpid, '$date')");
                $title_id = $wpdb->insert_id;

                $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('$revs_type', '0', 'info', '$info', $wpid, '$date')");
                $info_id = $wpdb->insert_id;

                $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('$revs_type', '0', 'status', 1, $wpid, '$date')");
                $status_id = $wpdb->insert_id;

                $wpdb->query("UPDATE $table_categories SET `title` = $title_id, `info` = $info_id, `status` = $status_id WHERE ID = $category_id ");
                
                $result = $wpdb->query("UPDATE $table_revs SET `parent_id` = $category_id WHERE ID IN ($title_id, $info_id, $status_id) ");

            // Step 8: Check if any of the queries above failed
            if ($title_id < 1 || $info_id < 1 || $status_id < 1 ||  $result < 1) {
                // when insert failed rollback all inserted data
                $wpdb->query("ROLLBACK");
                return array(
                        "status" => "error",
                        "message" => "An error occured while submitting data to database.",
                );
            
            }

            // commits all insert if true
            $wpdb->query("COMMIT");

            // Step 9: Return a success status and message 
            return array(
                "status" => "success",
                "message" => "Data has been updated successfully!",
            );

        }

    }