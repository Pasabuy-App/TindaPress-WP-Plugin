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
			if ( !isset($_POST['pdid']) || !isset($_POST['vid']) || !isset($_POST['key']) || !isset($_POST['ckey']) || !isset($_POST['val']) ) {
				return array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Request unknown!",
                );
            }
            
            // Step 4: Sanitize if variable is empty
            if ( empty($_POST['pdid']) || empty($_POST['vid']) || empty($_POST['key']) || empty($_POST['ckey']) || empty($_POST['val'])  ) {
				return array(
						"status" => "failed",
						"message" => "Required fields cannot be empty.",
                );
            }

            $product_id = $_POST['pdid'];
            $variants_id = $_POST['vid'];
            $variant_key = $_POST['key'];
            $variant_childkey = $_POST['ckey'];
            $new_value = $_POST['val'];
            $wpid = $_POST['wpid'];
            $date = TP_Globals:: date_stamp();

            //Check if this exists
            $get_parent = $wpdb->get_row("SELECT `ID`,
            (SELECT `child_val` FROM $table_revs WHERE `ID` = $table_variants.status) as status,
            (SELECT `child_val` FROM $table_revs WHERE `parent_id` = $variants_id AND `child_key` = 'name') as name,
            (SELECT `ID` FROM $table_revs WHERE `parent_id` = $variants_id AND `child_key` = 'name') as name_id
            FROM $table_variants 
            WHERE `ID` = $variants_id
            AND `pdid` = $product_id");

            if (!$get_parent) {
                return array(
                    "status" => "failed",
                    "message" => "This variant does not exists." 
                );
            }

            if ($get_parent->status == 0) {
                return array(
                    "status" => "failed",
                    "message" => "This variant is currently inactive." 
                );
            }
      
            $get_key = $wpdb->get_row("SELECT `ID` FROM $table_revs WHERE `parent_id` = $get_parent->name_id AND `child_key` = '$get_parent->name' AND `child_val` LIKE '%$variant_key%'");
            
            $wpdb->query("START TRANSACTION");

            $rev_insert = $wpdb->query("INSERT INTO `$table_revs` $rev_fields VALUES ('variants', '$get_key->ID', '$variant_childkey', '$new_value', $wpid, '$date')");
            // $rev_parent = $wpdb->insert_id;

            if ($rev_insert < 1) {
                $wpdb->query("ROLLBACK");
                return array(
                        "status" => "error",
                        "message" => "An error occured while submitting data to the server.",
                );
            }

            $wpdb->query("COMMIT");
            
            return array(
                        "status" => "success",
                        "message" => "Data has been updated successfully.",
            );


        }   

        

        
    }
