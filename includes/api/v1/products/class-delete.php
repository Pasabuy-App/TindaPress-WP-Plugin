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
?>
<?php

    class TP_Delete_Product {

        public static function listen(){
            global $wpdb;

            //Check if prerequisites plugin are missing
            $plugin = TP_Globals::verify_prerequisites();
            if ($plugin !== true) {
                return array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. ".$plugin." plugin missing!",
                );
            }

            //  Step2 : Validate if user is exist
			if (DV_Verification::is_verified() == false) {
                return array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Verification issues!",
                );
            }

             // Step3 : Sanitize all Request
			if (!isset($_POST["wpid"]) || !isset($_POST['pid']) ) {
				return array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Request unknown!",
                );
                
            }

            // Step6 : Sanitize all Request
			if ( empty($_POST['pid']) ) {
				return array(
						"status" => "unknown",
						"message" => "Required fields cannot be empty!",
                );
            }


            // variables
            $parentid = $_POST['pid'];
            $wpid = $_POST['wpid'];

            $product_type = "products";
            $date_stamp = TP_Globals::date_stamp();
            $product_table         = TP_PRODUCT_TABLE;
            $table_revs = TP_REVISIONS_TABLE;
            $table_revs_fields     = TP_REVISION_FIELDS;

            // Query
            $wpdb->query("START TRANSACTION ");
                $inactive = $wpdb->get_row("SELECT `status` FROM $product_table WHERE ID = $parentid  ");
                $wpdb->query("UPDATE $table_revs  SET child_val = '0' WHERE ID =  $inactive->status  AND parent_id = $parentid");

            // check of retsult is true or not
            if ($last_id < 1 || $result2 < 1 ) {
                $wpdb->query("ROLLBACK");
                return rest_ensure_response( 
					array(
						"status" => "failed",
						"message" => "An error occured while submitting data to the server",
					)
                );

            }else{
                $wpdb->query("COMMIT");
            // return Success
                return rest_ensure_response( 
					array(
						"status" => "success",
						"message" => "Data has been deleted successfully",
					)
                );

            }
        }
        
    }