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
            // Step1 : check if datavice plugin is activated
            if (TP_Globals::verify_datavice_plugin() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Plugin Missing!",
                    )
                );
            }
            // Step2 : Check if wpid and snky is valid
            if (TP_Globals::validate_user() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Request Unknown!",
                    )
                );
            }

             // Step3 : Sanitize all Request
			if (!isset($_POST["wpid"]) || !isset($_POST['pid']) ) {
				return rest_ensure_response( 
					array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Request unknown!",
					)
                );
                
            }

            // Step 1: Check if ID is in valid format (integer)
            if (!is_numeric($_POST["wpid"]) || !is_numeric($_POST["pid"]) ) {
                return rest_ensure_response( 
                    array(
                        "status" => "failed",
                        "message" => "Please contact your administrator. ID not in valid format!",
                    )
                );
                
            }

            // Step 5: Check if ID exists
			if (!get_user_by("ID", $_POST['wpid'])) {
				return rest_ensure_response( 
					array(
						"status" => "failed",
						"message" => "User not found!",
					)
                );
                
            }

            // Step6 : Sanitize all Request
			if (empty($_POST["wpid"]) || empty($_POST['pid']) ) {
				return rest_ensure_response( 
					array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Request unknown!",
					)
                );
                
            }
            // variables
            $parentid = $_POST['pid'];

            $wpid = $_POST['wpid'];

            $product_type = "products";

            $date_stamp = TP_Globals::date_stamp();

            $product_table         = TP_PRODUCT_TABLE;
            $table_revs            = TP_REVISION_TABLE;
            $table_revs_fields     = TP_REVISION_FIELDS;




            // Query
            $wpdb->query("START TRANSACTION ");
                $inactive = $wpdb->get_row("SELECT `status` FROM $product_table WHERE ID = $parentid  ");

                $wpdb->query("UPDATE $table_revs  SET child_val = '0' WHERE ID =  $inactive->status  AND parent_id = $parentid");
                
                $wpdb->query("INSERT INTO $table_revs (revs_type, parent_id, child_key, child_val, created_by, date_created) 
                                            VALUES ('$product_type', $parentid , 'status', '1', $wpid, '$date_stamp'  )");
                $last_id = $wpdb->insert_id;

                $result2 = $wpdb->query("UPDATE $product_table tp_prod SET tp_prod.`status` =  $last_id  WHERE tp_prod.ID = $parentid ");


            // check of retsult is true or not
            if ($last_id < 1 || $result2 < 1 ) {
                $wpdb->query("ROLLBACK");

                return rest_ensure_response( 
					array(
						"status" => "failed",
						"message" => "Please contact your administrator. Deletion Failed",
					)
                );

            }else{
                $wpdb->query("COMMIT");

            // return Success
                return rest_ensure_response( 
					array(
						"status" => "success",
						"message" => "Product has been updated successfully",
					)
                );

            }

        }
        
    }