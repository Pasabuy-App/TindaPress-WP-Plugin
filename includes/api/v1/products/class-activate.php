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

        //QA Done 2020-08-12 4:10 pm
        public static function activate_product(){

            global $wpdb;
            
            $date_stamp        = TP_Globals::date_stamp();
            // $product_table     = TP_PRODUCT_TABLE;
            $table_revs        = TP_REVISIONS_TABLE;
            $table_revs_fields = TP_REVISION_FIELDS;
            // Variables for Table
            $table_product = TP_PRODUCT_TABLE;
            $table_categories = TP_CATEGORIES_TABLE;

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



            // Step 5: Check if user has roles_access of can_activate_store or either contributor or editor
            $permission = TP_Globals::verify_role($_POST['wpid'], '0', 'can_activate_products' );
            
            if ($permission == true) {
                return array(
                    "status" => "failed",
                    "message" => "Current user has no access in activating products.",
                );
            }

            // variables
            $parentid = $_POST['pdid'];
            $wpid = $_POST['wpid'];
            
            // Step 6: Check if products exists
            $get_status_id = $wpdb->get_row("SELECT `status` FROM $table_product WHERE ID = $parentid  ");

            if ( empty($get_status_id)  ) {
                return array(
                    "status" => "failed",
                    "message" => "This product does not exists.",
                );
            }



            // Step 7: Start mysql transaction
            $wpdb->query("START TRANSACTION ");

                  $get_product_last_value = $wpdb->get_row("SELECT
                    tp_rev.revs_type as `type`,
                    ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE tp_rev.ID = tp_prod.title ) AS title,
                    ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.short_info ) AS `short_info`,
                    ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.long_info ) AS `long_info`,
                    ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.sku ) AS `sku`,
                    ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.price ) AS `price`,
                    ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.weight ) AS `weight`,
                    ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.dimension ) AS `dimension`,
                    ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.preview ) AS `preview`,
                    ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.`status` ) AS `status`
                FROM
                    $table_product tp_prod
                INNER JOIN 
                    $table_revs tp_rev ON tp_rev.ID = tp_prod.title
                INNER JOIN
                    $table_categories c ON c.ID = tp_prod.ctid
                WHERE tp_prod.ID = $parentid ");

                //Check if product is already activated
                if ($get_status->status != 0   ) {
                        
                    $wpdb->query("ROLLBACK");
                    return array(
                        "status" => "failed",
                        "message" => "This product is already activated.",
                    );
                }
                
                // Sanitize product title with apostrophe
                foreach ($get_product_last_value as $key => $value) {
                    $get_product_last_value->$key = str_replace("'","''", $value);
                }

                return$wpdb->query("INSERT INTO $table_revs $table_revs_fields VALUES ('$get_product_last_value->type', '$parentid', 'title', '$get_product_last_value->title', '$wpid', '$date_stamp')");
                $title = $wpdb->insert_id;

                $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('$get_product_last_value->type', '$parentid', 'preview', '$get_product_last_value->preview', '$wpid', '$date_stamp')");
                $preview = $wpdb->insert_id;

                $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('$get_product_last_value->type', '$parentid', 'short_info', '$get_product_last_value->short_info', '$wpid', '$date_stamp')");
                $short_info = $wpdb->insert_id;

                $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('$get_product_last_value->type', '$parentid', 'long_info', '$get_product_last_value->long_info', '$wpid', '$date_stamp')");
                $long_info = $wpdb->insert_id;

                $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('$get_product_last_value->type', '$parentid', 'status', '1', '$wpid', '$date_stamp')");
                $status = $wpdb->insert_id;

                $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('$get_product_last_value->type', '$parentid', 'sku', '$get_product_last_value->sku', '$wpid', '$date_stamp')");
                $sku = $wpdb->insert_id;

                $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('$get_product_last_value->type', '$parentid', 'price', '$', '$wpid', '$date_stamp')");
                $price = $wpdb->insert_id;

                $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('$get_product_last_value->type', '$parentid', 'weight', '$get_product_last_value->weight', '$wpid', '$date_stamp')");
                $weight = $wpdb->insert_id;

                $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('$get_product_last_value->type', '$parentid', 'dimension', '$get_product_last_value->dimension', '$wpid', '$date_stamp')");
                $dimension = $wpdb->insert_id;

                $update_product = $wpdb->query("UPDATE tp_products SET `title` = $title, `preview` = $preview, `short_info` = $short_info, `long_info` = $long_info, `status` = $status, `sku` = $sku, `price` = $price, `weight` = $weight, `dimension` = $dimension WHERE ID = $parentid ");

            

            // Step 8: Check for query results. Do a rollback if errors found
            if ( $result < 1 ) {
            
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