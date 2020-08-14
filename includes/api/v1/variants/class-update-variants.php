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
    class TP_Update_Variants {

        public static function listen(){
            return rest_ensure_response( 
                TP_Update_Variants:: update_variants()
            );
        }

        public static function update_variants(){
            
            global $wpdb;
            $table_variants = TP_VARIANTS_TABLE;
            $table_revs = TP_REVISIONS_TABLE;
            $rev_fields = TP_REVISION_FIELDS;
            $variants_fields = TP_VARIANTS_FIELDS;
            

            //Step 1: Check if prerequisites plugin are missing
            $plugin = TP_Globals::verify_prerequisites();
            if ($plugin !== true) {
                return array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. ".$plugin." plugin missing!",
                );
            }

            // Step 2: Check if wpid and snky is valid
            if (DV_Verification::is_verified() == false) {
                return array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Verification Issues!",
                );
            }

            // Step 3: Sanitize request
			if (!isset($_POST['data']) || !isset($_POST['vid'])) {
				return array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Request unknown!",
                );
            }
            
            // Step 4: Sanitize if variable is empty
            if (empty($_POST["data"]) || empty($_POST['vid'])) {
				return array(
						"status" => "failed",
						"message" => "Required fields cannot be empty.",
                );
            }

            $data = $_POST['data'];

            if (! is_array($data)) {
                return array(
                    "status" => "failed",
                    "message" => "Data is insufficient.",
                );
            }
            
            //Separate array into different variables
            $name = $data['name'];
            $variants = $data['variants'];
            
            $variants_id = $_POST['vid'];
            $wpid = $_POST['wpid'];
            $date = TP_Globals:: date_stamp();

            //Check if this exists
            $prev_variant = TP_Select_Variants_Id:: select_variants_id();

            return $old_variant = $prev_variant['data'][$variants_id];
            //PENDING
            //PENDING
            // $wpdb->query("START TRANSACTION");

            // $wpdb->query("INSERT INTO `$table_variants` $variants_fields VALUES ($product_id, $wpid, '$date')");
            // $parent_id = $wpdb->insert_id;

            // $wpdb->query("INSERT INTO `$table_revs` $rev_fields VALUES ('variants', $parent_id, 'name', '$name', $wpid, '$date')");
            // $rev_parent = $wpdb->insert_id;

            // if ($rev_parent < 1) {
            //     $wpdb->query("ROLLBACK");
            //     return array(
            //             "status" => "error",
            //             "message" => "An error occured while submitting data to the server.",
            //     );
            // }
            // foreach ($data['values'] as $key => $value) {
            //     $wpdb->query("INSERT INTO `$table_revs` $rev_fields VALUES ('variants', $rev_parent, '$name', '$key', $wpid, '$date')");
            //     $child = $wpdb->insert_id;
            //     if ($child < 1) {
            //         $wpdb->query("ROLLBACK");
            //         return array(
            //                 "status" => "error",
            //                 "message" => "An error occured while submitting data to the server.",
            //         );
            //     }
            //     foreach ($value as $child_key => $child_value) {
            //         $grand_child = $wpdb->query("INSERT INTO `$table_revs` $rev_fields VALUES ('variants', $child, '$child_key', '$child_value', $wpid, '$date')");
            //         if ($grand_child < 1) {
            //             $wpdb->query("ROLLBACK");
            //             return array(
            //                     "status" => "error",
            //                     "message" => "An error occured while submitting data to the server.",
            //             );
            //         }
            //     }
            // }

            // $wpdb->query("INSERT INTO `$table_revs` $rev_fields VALUES ('variants', $parent_id, 'status', 1, $wpid, '$date')");
            // $status_id = $wpdb->insert_id;

            // $update_parent = $wpdb->query("UPDATE $table_variants SET `status` = $status_id WHERE ID = $parent_id");

            // if ($status_id < 1 || $update_parent < 1) {
            //     $wpdb->query("ROLLBACK");
            //     return array(
            //             "status" => "error",
            //             "message" => "An error occured while submitting data to the server.",
            //     );
            // }
            // $wpdb->query("COMMIT");
            
            // return array(
            //             "status" => "success",
            //             "message" => "Data has been added successfully.",
            // );


            

        }   

        

        
    }
