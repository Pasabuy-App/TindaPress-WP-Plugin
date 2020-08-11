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

    class TP_Product_Activate {

        //REST API Call
        public static function listen(){
            
            return rest_ensure_response( 
                TP_Product_Activate:: activate_product()
            );
        }


        public static function activate_product(){

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

            // Step2 : Validate if user is exist
			if (DV_Verification::is_verified() == false) {
                return array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Verification issues!",
                );
            }

             // Step3 : Sanitize all Request
			if ( !isset($_POST['pid']) ) {
				return array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Request unknown!",
                );
                
            }

            // Step4 : Sanitize all Request
			if ( empty($_POST['pid']) ) {
				return array(
						"status" => "unknown",
						"message" => "Required fields cannot be empty!",
                );
            }

            // Step5 : Sanitize all Request
			if ( !is_numeric($_POST['pid']) ) {
				return array(
						"status" => "unknown",
						"message" => "Please contact your administrator. ID not in valid format!",
                );
            }

            // variables
            $parentid = $_POST['pid'];
            $wpid = $_POST['wpid'];

            // Query
            $wpdb->query("START TRANSACTION ");
                
                $get_status_id = $wpdb->get_row("SELECT `status` FROM $product_table WHERE ID = $parentid  ");

                $get_status = $wpdb->get_row("SELECT `child_val`as `status` FROM $table_revs WHERE ID = $get_status_id->status  ");

                $result =  $wpdb->query("UPDATE $table_revs  SET child_val = '1', `date_created` = '$date_stamp' WHERE ID =  $get_status_id->status  AND parent_id = $parentid");

                if ($get_status->status != 0  ) {
                    
                    $wpdb->query("ROLLBACK");
                    return array(
                        "status" => "failed",
                        "message" => "This product is already activated.",
                    );
                }
            // check of retsult is true or not
            if (empty($get_status_id) || $result < 1 ) {
            
                $wpdb->query("ROLLBACK");
            
                return array(
					"status" => "failed",
					"message" => "An error occured while submitting data to the server.",
                );

            }else{
            
                $wpdb->query("COMMIT");
                
                // return Success result
                return array(
					"status" => "success",
					"message" => "Data has been activate successfully.",
                );

            }
        }
        
    }