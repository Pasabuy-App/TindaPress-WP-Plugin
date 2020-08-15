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
    class TP_Activate_Variants {

        public static function listen(){
            return rest_ensure_response( 
                TP_Activate_Variants:: activate_variants()
            );
        }

        public static function activate_variants(){
            
            global $wpdb;
            $table_variants = TP_VARIANTS_TABLE;
            $table_revs = TP_REVISIONS_TABLE;
            $rev_fields = TP_REVISION_FIELDS;
            $variants_fields = TP_VARIANTS_FIELDS;
            $date = TP_Globals:: date_stamp();
            

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

            // Step 3: Check if params are passed
			if (!isset($_POST['vid']) ) {
				return array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Request unknown!",
                );
            }
            
            // Step 4: Check if params are not empty
            if (empty($_POST["vid"])) {
				return array(
						"status" => "failed",
						"message" => "Required fields cannot be empty.",
                );
            }

            $variants_id = $_POST['vid'];
            $wpid = $_POST['wpid'];

           
            $get_parent = array();
            
            //Check if this exists
            $get_parent = $wpdb->get_row("SELECT `ID`,
            (SELECT `child_val` FROM $table_revs WHERE `ID` = $table_variants.status) as status
            FROM $table_variants 
            WHERE `ID` = $variants_id");
            

            if (!$get_parent) {
                return array(
                    "status" => "failed",
                    "message" => "This variant does not exists" 
                );
            }
            
            if ($get_parent->status == 1) {
                return array(
                    "status" => "failed",
                    "message" => "This variant is already active." 
                );
            }

            $parent_id = $get_parent->ID;

            $wpdb->query("START TRANSACTION");

            $wpdb->query("INSERT INTO `$table_revs` $rev_fields VALUES ('variants', $parent_id, 'status', '1', $wpid, '$date')");
            $status_id = $wpdb->insert_id;
            
            $update_parent = $wpdb->query("UPDATE $table_variants SET `status` = $status_id WHERE ID = $parent_id");

            if ($status_id < 1 || $update_parent < 1) {
                $wpdb->query("ROLLBACK");
                return array(
                    "status" => "error",
                    "message" => "An error occured while submitting data to the server." 
                );
            }

            $wpdb->query("COMMIT");
            
            return array(
                        "status" => "success",
                        "message" => "Data has been activated successfully.",
            );

        }   

        

        
    }
