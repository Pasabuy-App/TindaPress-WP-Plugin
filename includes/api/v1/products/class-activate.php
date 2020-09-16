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

    class TP_Product_Activate {

        //REST API Call
        public static function listen(){
            
            return rest_ensure_response( 
                TP_Product_Activate:: activate_product()
            );
        }

        //QA Done 2020-08-12 4:10 pm
        public static function activate_product(){

            global $wpdb;
            
            $date_stamp        = TP_Globals::date_stamp();
            // $product_table  = TP_PRODUCT_TABLE;
            $table_revs        = TP_REVISIONS_TABLE;
            $table_revs_fields = TP_REVISION_FIELDS;
            $table_product     = TP_PRODUCT_TABLE;
            $table_categories  = TP_CATEGORIES_TABLE;

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

             // Step 3: Check if parameters are passed
			if ( !isset($_POST['pdid']) ) {
				return array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Request unknown!",
                );
                
            }

            // Step 4: Check if params passed are empty
			if ( empty($_POST['pdid']) ) {
				return array(
						"status" => "failed",
						"message" => "Required fields cannot be empty!",
                );
            }

            // variables
            $parentid = $_POST['pdid'];
            $wpid = $_POST['wpid'];
            
            // Step 6: Check if products exists
            $get_product_data = $wpdb->get_row("SELECT
                    prod.ID,
                    child_val AS `status` 
                FROM
                    tp_products prod
                    INNER JOIN tp_revisions rev ON rev.`ID` = prod.`status` 
                WHERE
                    revs_type = 'products' 
                    AND child_key = 'status' AND prod.ID = '$parentid'");

            if ( empty($get_product_data)  ) {
                return array(
                    "status" => "failed",
                    "message" => "This product does not exists.",
                );
            }

            if ($get_product_data->status == '1') {
                return array(
                    "status" => "failed",
                    "message" => "This product is already activated.",
                );
            }

            // Step 7: Start mysql transaction
            $wpdb->query("START TRANSACTION ");
                
                $insert_new_status = $wpdb->query("INSERT INTO tp_revisions $table_revs_fields VALUES ('products', '$get_product_data->ID', 'status', '1', '$wpid', '$date_stamp')");
                $insert_new_status_ID = $wpdb->insert_id;

                $update_product = $wpdb->query("UPDATE tp_products SET `status` = '$insert_new_status_ID' WHERE ID = $get_product_data->ID ");
                // Sanitize product title with apostrophe
               
            // Step 8: Check for query results. Do a rollback if errors found
            if ( $insert_new_status_ID < 1  || $update_product < 1 ) {
                $wpdb->query("ROLLBACK");
                return array(
					"status" => "error",
					"message" => "An error occured while submitting data to the server.",
                );

            }else {
                //Do a commit and return success status
                $wpdb->query("COMMIT");
                return array(
					"status" => "success",
					"message" => "Data has been activated successfully.",
                );
            }
        }
    }