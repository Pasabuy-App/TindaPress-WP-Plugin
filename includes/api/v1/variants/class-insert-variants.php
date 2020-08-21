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
			if ( !isset($_POST['name']) || !isset($_POST['pdid']) || !isset($_POST['pid']) ) {
				return array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Request unknown!",
                );
            }
            
            // Step4 : Sanitize if variable is empty
            if ( empty($_POST["name"]) || empty($_POST["pdid"]) || ( empty($_POST['base']) && empty($_POST['price']) ) ) {
				return array(
						"status" => "failed",
						"message" => "Required fields cannot be empty.",
                );
            }

            isset($_POST['price']) ? $price = $_POST['price'] : $price = NULL;
            isset($_POST['base']) ? $base_price = $_POST['base'] : $base_price = NULL;
            isset($_POST['info']) ? $info = $_POST['info'] : $info = NULL;

            //Separate into different variables
            $product_id = $_POST['pdid'];
            $parent_id = $_POST['pid'];
            $name = $_POST['name'];
            $wpid = $_POST['wpid'];

            $date = TP_Globals:: date_stamp();
            
            $get_product = $wpdb->get_row("SELECT
                    tp_prod.ID, tp_prod.ctid, tp_prod.status as status_id
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

            // //Fails if product is currently inactive
            if ($get_product->status == 0) {
                return array(
                    "status" => "failed",
                    "message" => "This product is currently inactive.",
                );
            }
                
            $wpdb->query("START TRANSACTION");
            
            if ($parent_id == 0) {
                $wpdb->query("INSERT INTO `$table_variants` $variants_fields VALUES (0, $product_id, $wpid, '$date')");
                $last_id = $wpdb->insert_id;
                $wpdb->query("INSERT INTO `$table_revs` $rev_fields VALUES ('variants', $last_id, 'baseprice', '$base_price', $wpid, '$date')");
                $rev_bp = $wpdb->insert_id;
                $wpdb->query("INSERT INTO `$table_revs` $rev_fields VALUES ('variants', $last_id, 'status', 1, $wpid, '$date')");
                $rev_status = $wpdb->insert_id;
            } else {
                $wpdb->query("INSERT INTO `$table_variants` $variants_fields VALUES ($parent_id, $product_id, $wpid, '$date')");
                $last_id = $wpdb->insert_id;
                $wpdb->query("INSERT INTO `$table_revs` $rev_fields VALUES ('variants', $last_id, 'price', '$price', $wpid, '$date')");

            }

            if ($info) {
                $wpdb->query("INSERT INTO `$table_revs` $rev_fields VALUES ('variants', $last_id, 'info', '$info', $wpid, '$date')");
            }
            
            $wpdb->query("INSERT INTO `$table_revs` $rev_fields VALUES ('variants', $last_id, 'name', '$name', $wpid, '$date')");
            $rev_name = $wpdb->insert_id;

            if ( $last_id < 1 ) {
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
