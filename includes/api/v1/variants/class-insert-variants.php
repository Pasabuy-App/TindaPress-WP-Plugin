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
    class TP_Insert_Variants {

        public static function listen(){
            return rest_ensure_response( 
                TP_Insert_Variants:: insert_variants()
            );
        }

        public static function insert_variants(){
            
            global $wpdb;
            $table_variants = TP_VARIANTS_TABLE;
            $table_revs = TP_REVISIONS_TABLE;
            $rev_fields = TP_REVISION_FIELDS;
            $variants_fields = TP_VARIANTS_FIELDS;
            $table_product = TP_PRODUCT_TABLE;

            //Step1 : Check if prerequisites plugin are missing
            $plugin = TP_Globals::verify_prerequisites();
            if ($plugin !== true) {
                return array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. ".$plugin." plugin missing!",
                );
            }

            // Step2 : Check if wpid and snky is valid
            if (DV_Verification::is_verified() == false) {
                return array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Verification Issues!",
                );
            }

            // Step3 : Sanitize request
			if (!isset($_POST['data']) ) {
				return array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Request unknown!",
                );
            }
            
            // Step4 : Sanitize if variable is empty
            if (empty($_POST["data"])) {
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
            $base_price = $data['bp'];
            $product_id = $data['pdid'];
            $name = $data['name'];
            $values = $data['values'];
            
            $wpid = $_POST['wpid'];
            $date = TP_Globals:: date_stamp();

            $get_product = $wpdb->get_row("SELECT
                    tp_prod.ID, tp_prod.ctid, tp_prod.status as status_id,
                    ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE tp_rev.ID = tp_prod.title ) AS product_name,
                    ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.status ) AS `status`
                FROM
                    $table_product tp_prod
                INNER JOIN 
                    $table_revs tp_rev ON tp_rev.ID = tp_prod.title
                WHERE
                    tp_prod.ID = $product_id
                GROUP BY
                    tp_prod.ID
            ");
            
            //Check if no rows found
            if (!$get_product) {
                return array(
                    "status" => "failed",
                    "message" => "This product does not exists",
                );
            }

            //Fails if product is currently inactive
            if ($get_product->status == 0) {
                return array(
                    "status" => "failed",
                    "message" => "This product is currently inactive.",
                );
            }
                
            $wpdb->query("START TRANSACTION");

            $wpdb->query("INSERT INTO `$table_variants` $variants_fields VALUES ($product_id, $wpid, '$date')");
            $parent_id = $wpdb->insert_id;

            $wpdb->query("INSERT INTO `$table_revs` $rev_fields VALUES ('variants', $parent_id, 'name', '$name', $wpid, '$date')");
            $rev_parent = $wpdb->insert_id;

            $wpdb->query("INSERT INTO `$table_revs` $rev_fields VALUES ('variants', $parent_id, 'baseprice', '$base_price', $wpid, '$date')");

            if ($rev_parent < 1) {
                $wpdb->query("ROLLBACK");
                return array(
                        "status" => "error",
                        "message" => "An error occured while submitting data to the server.",
                );
            }
            foreach ($data['values'] as $key => $value) {
                $wpdb->query("INSERT INTO `$table_revs` $rev_fields VALUES ('variants', $rev_parent, '$name', '$key', $wpid, '$date')");
                $child = $wpdb->insert_id;
                if ($child < 1) {
                    $wpdb->query("ROLLBACK");
                    return array(
                            "status" => "error",
                            "message" => "An error occured while submitting data to the server.",
                    );
                }
                foreach ($value as $child_key => $child_value) {
                    $grand_child = $wpdb->query("INSERT INTO `$table_revs` $rev_fields VALUES ('variants', $child, '$child_key', '$child_value', $wpid, '$date')");
                    if ($grand_child < 1) {
                        $wpdb->query("ROLLBACK");
                        return array(
                                "status" => "error",
                                "message" => "An error occured while submitting data to the server.",
                        );
                    }
                }
            }

            $wpdb->query("INSERT INTO `$table_revs` $rev_fields VALUES ('variants', $parent_id, 'status', 1, $wpid, '$date')");
            $status_id = $wpdb->insert_id;

            $update_parent = $wpdb->query("UPDATE $table_variants SET `status` = $status_id WHERE ID = $parent_id");

            if ($status_id < 1 || $update_parent < 1) {
                $wpdb->query("ROLLBACK");
                return array(
                        "status" => "error",
                        "message" => "An error occured while submitting data to the server.",
                );
            }
            $wpdb->query("COMMIT");
            
            return array(
                        "status" => "success",
                        "message" => "Data has been added successfully.",
            );


            

        }   

        

        
    }
