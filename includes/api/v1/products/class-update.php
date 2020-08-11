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

    class TP_Product_Update {

        public static function listen(){
            return rest_ensure_response( 
                TP_Product_Update:: update_product()
            );
        }

        public static function update_product(){
            
            global $wpdb;

            // Variables for Tables
            $table_revs = TP_REVISIONS_TABLE;
            $table_revs_fields = TP_REVISION_FIELDS;
            $table_product = TP_PRODUCT_TABLE;
            $table_product_fields = TP_PRODUCT_FIELDS;
            $revs_type = "products";
            
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
            if (!isset($_POST['pdid']) 
                || !isset($_POST["stid"]) 
                || !isset($_POST["title"]) 
                || !isset($_POST["short_info"]) 
                || !isset($_POST["long_info"]) 
                || !isset($_POST["sku"]) 
                || !isset($_POST["price"]) 
                || !isset($_POST["weight"]) 
                || !isset($_POST["dimension"]) 
                || !isset($_POST["preview"])) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Request unknown!",
                );
                
            }

            // Step6: Sanitize all Request if empty
            if (empty($_POST['pdid']) 
                || empty($_POST["stid"]) 
                || empty($_POST["title"]) 
                || empty($_POST["short_info"]) 
                || empty($_POST["long_info"]) 
                || empty($_POST["sku"]) 
                || empty($_POST["price"]) 
                || empty($_POST["weight"]) 
                || empty($_POST["dimension"]) 
                || empty($_POST["preview"])) {
               return array(
                       "status" => "unknown",
                       "message" => "Required fields cannot be empty",
               );
               
           }

            // Check user role 
            if (TP_Globals::verify_role( $_POST['wpid'], $_POST['stid'], 'can_update_product' )) {
                return array( 
                    TP_Globals::verify_role($_POST['wpid'], $_POST['stid'], 'can_update_product' ),
                );
            }
            
            // variables for query    
            $later = TP_Globals::date_stamp();
            $created_by = $_POST['wpid'];
            $product_id = $_POST['pdid'];
            $stid = $_POST['stid'];
            $revs_type = "products";

            $get_product = $wpdb->get_row("SELECT
                    tp_prod.ID, tp_prod.ctid, tp_prod.status as status_id,
                    ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE tp_rev.ID = tp_prod.title ) AS product_name,
                    ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.short_info ) AS `short_info`,
                    ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.long_info ) AS `long_info`,
                    ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.sku ) AS `sku`,
                    ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.price ) AS `price`,
                    ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.weight ) AS `weight`,
                    ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.dimension ) AS `dimension`,
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
            
            if (!$get_product) {
                return array(
                    "status" => "failed",
                    "message" => "This product does not exists",
                );
            }

            if ($get_product->status == 0) {
                return array(
                    "status" => "failed",
                    "message" => "This product is currently deactivated.",
                );
            }

            $status_id = $get_product->status_id;

            $user = TP_Product_Update::catch_post();
            // Query
            $wpdb->query("START TRANSACTION");

                $wpdb->query("UPDATE $table_revs SET child_val = '0' WHERE ID = $status_id ");

                $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('$revs_type', '{$user["pdid"]}', 'title', '{$user["title"]}', '{$user["created_by"]}', '$later')");
                $title = $wpdb->insert_id;

                $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('$revs_type', '{$user["pdid"]}', 'preview', '{$user["preview"]}', '{$user["created_by"]}', '$later')");
                $preview = $wpdb->insert_id;

                $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('$revs_type', '{$user["pdid"]}', 'short_info', '{$user["short_info"]}', '{$user["created_by"]}', '$later')");
                $short_info = $wpdb->insert_id;

                $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('$revs_type', '{$user["pdid"]}', 'long_info', '{$user["long_info"]}', '{$user["created_by"]}', '$later')");
                $long_info = $wpdb->insert_id;

                $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('$revs_type', '{$user["pdid"]}', 'status', '1', '{$user["created_by"]}', '$later')");
                $status = $wpdb->insert_id;

                $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('$revs_type', '{$user["pdid"]}', 'sku', '{$user["sku"]}', '{$user["created_by"]}', '$later')");
                $sku = $wpdb->insert_id;

                $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('$revs_type', '{$user["pdid"]}', 'price', '{$user["price"]}', '{$user["created_by"]}', '$later')");
                $price = $wpdb->insert_id;

                $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('$revs_type', '{$user["pdid"]}', 'weight', '{$user["weight"]}', '{$user["created_by"]}', '$later')");
                $weight = $wpdb->insert_id;

                $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('$revs_type', '{$user["pdid"]}', 'dimension', '{$user["dimension"]}', '{$user["created_by"]}', '$later')");
                $dimension = $wpdb->insert_id;

                //  (stid, ctid, title, preview, short_info, long_info, status, sku, price,  weight,  dimension , created_by, date_created)
                 $result = $wpdb->query("UPDATE $table_product SET `title` = $title, `preview` = $preview, `short_info` = $short_info, `long_info` = $long_info, `status` = $status, `sku` = $sku, `price` = $price,  `weight` = $weight,  `dimension` = $dimension  WHERE ID = {$user["pdid"]} ");

            if ($result < 1 || $title < 1 || $short_info < 1 || $long_info < 1 || $sku < 1 || $price < 1 || $weight < 1 || $dimension < 1 || $preview < 1 ) {
               
                // when insert failed rollback all inserted data
                $wpdb->query("ROLLBACK");
                return array(
                    "status" => "failed",
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

          // Catch Post 
        public static function catch_post()
        {
              $cur_user = array();
               
                $cur_user['created_by'] = $_POST["wpid"];
                $cur_user['pdid']       = $_POST["pdid"];
                $cur_user['stid']       = $_POST["stid"];
                $cur_user['title']      = $_POST["title"];
                $cur_user['short_info'] = $_POST["short_info"];
                $cur_user['long_info']  = $_POST["long_info"];
                $cur_user['sku']        = $_POST["sku"];
                $cur_user['price']      = $_POST["price"];
                $cur_user['weight']     = $_POST["weight"];
                $cur_user['dimension']  = $_POST["dimension"];
                $cur_user['preview']    = $_POST["preview"];
  
                return  $cur_user;
        }

    }