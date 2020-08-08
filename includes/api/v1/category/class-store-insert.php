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

    class TP_Category_Store_Insert {

        //REST API Call
        public static function listen(){
            
            return rest_ensure_response( 
                TP_Category_Store_Insert:: store_insert_category()
            );
        }
        
        //Inserting Category function
        public static function store_insert_category(){
            global $wpdb;
            
            //  Step1 : Verify if Datavice Plugin is Active
			if (TP_Globals::verify_datavice_plugin() == false) {
                return  array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Plugin Missing!",
                );
                
			}
			
			//  Step2 : Validate if user is exist
			if (DV_Verification::is_verified() == false) {
                return array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Verification Issues!",
                );
                
            }

            if (!isset($_POST["title"]) || !isset($_POST["info"])  || !isset($_POST["types"]) || !isset($_POST["stid"]) ) {
				return array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Request unknown!",
                );
            }

            if (empty($_POST["title"]) || empty($_POST["info"])  || empty($_POST["types"]) || !isset($_POST["stid"]) ) {
				return array(
						"status" => "failed",
						"message" => "Required fields cannot be empty.",
                );
            }

            if ( !($_POST['types'] === 'store') && !($_POST['types'] === 'product') && !($_POST['types'] === 'tags') ) {
                return array(
                    "status" => "failed",
                    "message" => "Category must be product or store only.",
                );
            }

            $title = $_POST['title'];
            
            $info = $_POST['info'];

            $wpid = $_POST["wpid"]; 

            $store_id = $_POST["stid"]; 

            //Store or product
            $types = $_POST["types"]; 

            $table_revs = TP_REVISION_TABLE;
            $table_revs_fields = TP_REVISION_FIELDS;
            $table_categories = TP_CATEGORIES_TABLE;
            $categories_fields = TP_CATEGORIES_FIELDS;

            $revs_type = "categories";

            $date = date('Y-m-d h:i:s');

            $get_store = $wpdb->get_row("SELECT `ID` FROM `tp_stores` WHERE `ID` = $store_id");

            if (!$get_store) {
                return array(
                    "status" => "failed",
                    "message" => "This store does not exists.",
                );
            }

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

                $store_result = $wpdb->query("UPDATE `tp_stores` SET `ctid` = $parent_id WHERE `ID` = $store_id ");

            if ($title_id < 1 || $info_id < 1 || $status_id < 1 || $parent_id < 1 || $result < 1 || $store_result < 1) {
                // when insert failed rollback all inserted data
                $wpdb->query("ROLLBACK");
                return array(
                        "status" => "error",
                        "message" => "An error occured while submitting data to database.",
                );
            
            }

            // commits all insert if true
            $wpdb->query("COMMIT");

            return array(
                "status" => "success",
                "message" => "Data has been added successfully!",
            );


        }

    }