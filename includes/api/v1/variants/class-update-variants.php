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
            
            // 2nd Initial QA 2020-08-24 11:20 PM - Miguel
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
			if ( !isset($_POST['pdid']) || !isset($_POST['vid']) || !isset($_POST['name']) ) {
				return array(
					"status" => "unknown",
					"message" => "Please contact your administrator. Request unknown!",
                );
            }
            
            // Step 4: Sanitize if variable is empty
            if ( empty($_POST['pdid']) || empty($_POST['vid']) || empty($_POST['name']) || ( empty($_POST['base']) && empty($_POST['price']) ) ) {
				return array(
					"status" => "failed",
					"message" => "Required fields cannot be empty.",
                );
            }

            $product_id = $_POST['pdid'];
            $variants_id = $_POST['vid'];
            $variant_name = $_POST['name'];
            $wpid = $_POST['wpid'];
            $date = TP_Globals:: date_stamp();

            // Validate if exists and if status is 0 or 1 using variant id and product id
            $get_parent = $wpdb->get_row("SELECT var.ID,
                (SELECT child_val FROM $table_revs WHERE ID = MAX(rev.ID)) as status
            FROM
                $table_variants var
            INNER JOIN $table_revs rev ON rev.parent_id = var.ID 
            WHERE var.ID = '$variants_id'  
            AND rev.revs_type = 'variants' 
            AND child_key = 'status' 
            ");

            if ($get_parent->ID === null){
                return array(
                    "status" => "failed",
                    "message" => "This variant does not exists" 
                );
            }
            
            if ($get_parent->status == '0') {
                return array(
                    "status" => "failed",
                    "message" => "This variant is already inactive." 
                );
            }

            $wpdb->query("START TRANSACTION");

            $get_key = $wpdb->get_row("SELECT `ID` FROM $table_revs WHERE `parent_id` = $get_parent->name_id AND `child_key` = '$get_parent->name' AND `child_val` LIKE '%$variant_key%'");

            $rev_insert = $wpdb->query("INSERT INTO `$table_revs` $rev_fields VALUES ('variants', '$get_key->ID', '$variant_childkey', '$new_value', $wpid, '$date')");

            if ($rev_insert < 1) {
                $wpdb->query("ROLLBACK");
                return array(
                    "status" => "error",
                    "message" => "An error occured while submitting data to the server.",
                );

            }else{
                $wpdb->query("COMMIT");
            
                return array(
                    "status" => "success",
                    "message" => "Data has been updated successfully.",
                );
            }

            


        }   

        

        
    }
