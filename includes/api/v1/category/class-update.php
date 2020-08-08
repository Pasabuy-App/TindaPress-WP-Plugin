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
                TP_Category_Update:: Update_category()
            );
        }
        
        //Inserting Category function
        public static function Update_category(){
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
                        "message" => "Please contact your administrator. Request Unknown!",
                );
                
            }

            if (!isset($_POST["title"]) || !isset($_POST["info"])  || !isset($_POST["types"]) ) {
				return array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Request unknown!",
                );
            }

            if (empty($_POST["title"]) || empty($_POST["info"])  || empty($_POST["types"]) ) {
				return array(
						"status" => "failed",
						"message" => "Required fields cannot be empty.",
                );
            }


            if ( !($_POST['types'] === 'store') && !($_POST['types'] === 'product')) {
                return array(
                    "status" => "failed",
                    "message" => "Category must be product or store only.",
                );
            }

            $title = $_POST['title'];
            
            $info = $_POST['info'];

            $wpid = $_POST["wpid"]; 

            $ctid = $_POST["ctid"];

            //Store or product
            $types = $_POST["types"]; 

            $table_revs = TP_REVISION_TABLE;
            $table_revs_fields = TP_REVISION_FIELDS;
            $table_categories = TP_CATEGORIES_TABLE;
            $categories_fields = TP_CATEGORIES_FIELDS;

            $revs_type = "categories";

            $date = date('Y-m-d h:i:s');

            

            $wpdb->query("START TRANSACTION");
                // update Status to zero
                $status_id = $wpdb->get_row("SELECT `status` FROM $table_categories WHERE ID = $ctid  ");
                $wpdb->query("UPDATE $table_revs SET `child_val` = 0 WHERE ID = $status_id->status  ");


                $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('$revs_type', '0', 'title', '$title', $wpid, '$date')");
                
                $title_id = $wpdb->insert_id;

                $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('$revs_type', '0', 'info', '$info', $wpid, '$date')");
                
                $info_id = $wpdb->insert_id;

                $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('$revs_type', '0', 'status', 1, $wpid, '$date')");

                $status_id = $wpdb->insert_id;

                $wpdb->query("UPDATE $table_categories SET `title`=$title_id, `info`=$info_id, `status`= $status_id WHERE ID = $ctid ");
                
             
                $result = $wpdb->query("UPDATE $table_revs SET `parent_id` = $ctid WHERE ID IN ($title_id, $info_id, $status_id) ");

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

            return array(
                "status" => "success",
                "message" => "Data has been added successfully!",
            );


        }

    }