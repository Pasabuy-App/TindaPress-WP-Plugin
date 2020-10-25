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


    class TP_Product_Delete {

        //REST API Call
        public static function listen(){
            
            return rest_ensure_response( 
                TP_Product_Delete:: delete_product()
            );
        }

        //QA done 2020-8-12 10:53 am
        public static function delete_product(){

            global $wpdb;
            $product_type      = "products";
            $date_stamp        = TP_Globals::date_stamp();
            $product_table     = TP_PRODUCT_TABLE;
            $table_revs        = TP_REVISIONS_TABLE;
            $table_revs_fields = TP_REVISION_FIELDS;

            // Step 1: Check if prerequisites plugin are missing
            $plugin = TP_Globals::verify_prerequisites();
            if ($plugin !== true) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. ".$plugin." plugin missing!",
                );
            }

            // Step 2: Validate if user is exist
			if (DV_Verification::is_verified() == false) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Verification issues!",
                );
            }

             // Step 3: Check if params are passed
			if (!isset($_POST['pdid']) ) {
				return array(
					"status" => "unknown",
					"message" => "Please contact your administrator. Request unknown!",
                );
            }

            // Step 4: Check if params passed are not null
			if ( empty($_POST['pdid']) ) {
				return array(
					"status" => "unknown",
					"message" => "Required fields cannot be empty!",
                );
            }

            // Step 5: Check if user has roles_access of can_activate_store or either contributor or editor
            // $permission = TP_Globals::verify_role($_POST['wpid'], '0', 'can_delete_products' );
            
            // if ($permission == true) {
            //     return array(
            //         "status" => "failed",
            //         "message" => "Current user has no access in deleting products.",
            //     );
            // }

            // variables
            $parentid = $_POST['pdid'];
            $wpid = $_POST['wpid'];

            // Step 6: Check if product exists
            $get_status_id = $wpdb->get_row("SELECT `status` FROM $product_table WHERE ID = $parentid  ");

            if ( empty($get_status_id)  ) {
                return array(
                    "status" => "failed",
                    "message" => "This product does not exists.",
                );
            }

            // Step 7: Start mysql transaction
            $wpdb->query("START TRANSACTION ");
                
                //Get current status of the product
                $get_status = $wpdb->get_row("SELECT `child_val`as `status` FROM $table_revs WHERE ID = $get_status_id->status  ");

                $result =  $wpdb->query("UPDATE $table_revs  SET child_val = '0', `date_created` = '$date_stamp' WHERE ID =  $get_status_id->status  AND parent_id = $parentid");

            //Returns failed if product is already deactivated
            if ($get_status->status != 1  ) {
                $wpdb->query("ROLLBACK");
                return array(
                    "status" => "failed",
                    "message" => "This product is already inactive.",
                );
            }

            // Step 8: Check for results. Do a rollback if error occurs. Else do a commit
            if ($result < 1 ) {
            
                $wpdb->query("ROLLBACK");
            
                return array(
					"status" => "error",
					"message" => "An error occured while submitting data to the server.",
                );

            }else{
            
                $wpdb->query("COMMIT");
                
                // return Success result
                return array(
					"status" => "success",
					"message" => "Data has been deleted successfully.",
                );
            }
        }
    }