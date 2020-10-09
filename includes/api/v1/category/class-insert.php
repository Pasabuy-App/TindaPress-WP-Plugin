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
            // 2nd Initial QA 2020-08-24 5:09 PM - Miguel

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

            //Check if user has roles_access of can_add_category or either contributor or editor
           $permission = TP_Globals::verify_role($_POST['wpid'], '0', 'can_add_category' );

            if ($permission != true) {
                return array(
                    "status" => "failed",
                    "message" => "Current user has no access in adding category.",
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
            if (empty($_POST["title"]) || empty($_POST["types"]) ) {
				return array(
					"status" => "failed",
					"message" => "Required fields cannot be empty.",
                );
            }

            // Step 5: Check if types value is valid
            if ( !($_POST['types'] === 'store') && !($_POST['types'] === 'product') && !($_POST['types'] === 'tags') && !($_POST['types'] === 'branch') ) {
                return array(
                    "status" => "failed",
                    "message" => "Category must be product or store only.",
                );
            }

            if ( ($_POST['types'] == 'store')  || ($_POST['types'] == 'tags') ) {
                if ( !isset($_POST['stid'])) {
                    return array(
                        "status" => "failed",
                        "message" => "This Category type must have a store ID.",
                    );
                }

                if ( empty($_POST['stid'])) {
                    return array(
                        "status" => "failed",
                        "message" => "Required fields cannot be empty store ID.",
                    );
                }
            }

            if($_POST['types'] == "branch"){
                $groups = "robinson";
            }else{
                $groups = "inhouse";
            }


            $title = $_POST['title'];

            $info = $_POST['info'];

            $wpid = $_POST["wpid"];

            //Store or product
            $types = $_POST["types"];

            // Step 6: Do mysql transaction
            $wpdb->query("START TRANSACTION");

                // Condition for Robinson Category Child
                if (isset($_POST['pid']) && $_POST['pid'] != "0" && $_POST['pid'] != null && $_POST['types'] == 'branch') {

                    if ($_POST['pid'] != null) {
                        $pid = $_POST['pid'];

                        $check_parent = $wpdb->get_row("SELECT * FROM $table_categories WHERE ID = '$pid' AND types = '$types'");
                        if (empty($check_parent)) {
                            return array(
                                "status" => "failed",
                                "message" => "This parent category does not exists.",
                            );
                        }

                        $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('$revs_type', '0', 'title', '$title', $wpid, '$date')");
                        $title_id = $wpdb->insert_id;

                        $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('$revs_type', '0', 'info', '$info', $wpid, '$date')");
                        $info_id = $wpdb->insert_id;

                        $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('$revs_type', '0', 'status', 1, $wpid, '$date')");
                        $status_id = $wpdb->insert_id;

                        $store_id = 0;
                        if( isset($_POST["stid"]) ) {
                            $store_id = (int)$_POST["stid"];
                        }

                        $wpdb->query("INSERT INTO $table_categories (stid, title, info, `status`, types, created_by, date_created, parent, `groups` ) VALUES ('$store_id', '$title_id', '$info_id', '$status_id','$types', $wpid, '$date', '$pid', 'robinson')");
                        $parent_id = $wpdb->insert_id;

                        $result = $wpdb->query("UPDATE $table_revs SET `parent_id` = $parent_id WHERE ID IN ($title_id, $info_id, $status_id) ");
                        $wpdb->query("UPDATE $table_categories SET `hash_id` = sha2($parent_id, 256) WHERE ID = $parent_id ");

                    }

                }else{

                    $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('$revs_type', '0', 'title', '$title', $wpid, '$date')");
                    $title_id = $wpdb->insert_id;

                    $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('$revs_type', '0', 'info', '$info', $wpid, '$date')");
                    $info_id = $wpdb->insert_id;

                    $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('$revs_type', '0', 'status', 1, $wpid, '$date')");
                    $status_id = $wpdb->insert_id;

                    $store_id = 0;
                    if( isset($_POST["stid"]) ) {
                        $store_id = (int)$_POST["stid"];
                    }

                    $wpdb->query("INSERT INTO $table_categories (stid, title, info, `status`, types, created_by, date_created, `groups` )  VALUES ('$store_id', '$title_id', '$info_id', '$status_id','$types', $wpid, '$date', '$groups')");
                    $parent_id = $wpdb->insert_id;

                    $result = $wpdb->query("UPDATE $table_revs SET `parent_id` = $parent_id WHERE ID IN ($title_id, $info_id, $status_id) ");
                    $wpdb->query("UPDATE $table_categories SET `hash_id` = sha2($parent_id, 256) WHERE ID = $parent_id ");
                }



            // Step 7: Check if any of the queries above failed
            if ($title_id < 1 || $info_id < 1 || $status_id < 1 || $parent_id < 1 || $result < 1) {

                // when insert failed rollback all inserted data
                $wpdb->query("ROLLBACK");
                return array(
                    "status" => "error",
                    "message" => "An error occured while submitting data to database.",
                );

            }else{

                // commits all insert if true
                $wpdb->query("COMMIT");

                // Step 8: Return a success status and message
                return array(
                    "status" => "success",
                    "message" => "Data has been added successfully!",
                );
            }
        }
    }