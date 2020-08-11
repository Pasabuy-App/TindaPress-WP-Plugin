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

    class TP_Category_Insert {

        //REST API Call
        public static function listen(){
            return rest_ensure_response( 
                TP_Category_Insert:: insert_category()
            );
        }
        
        //Inserting Category function
        public static function insert_category(){
            
            //Inital QA done 2020-08-10 08:14 PM

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
            if (!isset($_POST["title"]) || !isset($_POST["info"])  || !isset($_POST["types"]) ) {
				return array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Request unknown!",
                );
            }

            // Step 4: Check if parameters passed are not null
            if (empty($_POST["title"]) || empty($_POST["info"])  || empty($_POST["types"]) ) {
				return array(
						"status" => "failed",
						"message" => "Required fields cannot be empty.",
                );
            }

            // Step 5: Check if types value is valid
            if ( !($_POST['types'] === 'store') && !($_POST['types'] === 'product') && !($_POST['types'] === 'tags') ) {
                return array(
                    "status" => "failed",
                    "message" => "Category must be product or store only.",
                );
            }

            $title = $_POST['title'];
            
            $info = $_POST['info'];

            $wpid = $_POST["wpid"]; 

            //Store or product
            $types = $_POST["types"]; 

            // Step 6: Do mysql transaction
            $wpdb->query("START TRANSACTION");

                $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('$revs_type', '0', 'title', '$title', $wpid, '$date')");
                $title_id = $wpdb->insert_id;

                $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('$revs_type', '0', 'info', '$info', $wpid, '$date')");
                $info_id = $wpdb->insert_id;

                $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('$revs_type', '0', 'status', 1, $wpid, '$date')");
                $status_id = $wpdb->insert_id;

                $wpdb->query("INSERT INTO $table_categories $categories_fields VALUES ('$title_id', '$info_id', '$status_id','$types', $wpid, '$date')");
                $parent_id = $wpdb->insert_id;

                $result = $wpdb->query("UPDATE $table_revs SET `parent_id` = $parent_id WHERE ID IN ($title_id, $info_id, $status_id) ");

            // Step 7: Check if any of the queries above failed
            if ($title_id < 1 || $info_id < 1 || $status_id < 1 || $parent_id < 1 || $result < 1) {
                // when insert failed rollback all inserted data
                $wpdb->query("ROLLBACK");
                return array(
                        "status" => "error",
                        "message" => "An error occured while submitting data to database.",
                );
            
            }

            // commits all insert if true
            $wpdb->query("COMMIT");

            // Step 8: Return a success status and message 
            return array(
                "status" => "success",
                "message" => "Data has been added successfully!",
            );


        }

    }