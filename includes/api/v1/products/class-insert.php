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

    class TP_Insert_Product {
        
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
                        "message" => "Please contact your administrator. Verification Issues!",
                );
            }

            // Step3 : Sanitize all Request
            if (  !isset($_POST["ctid"])      || !isset($_POST["stid"]) 
               || !isset($_POST["title"])     || !isset($_POST["short_info"]) 
               || !isset($_POST["long_info"]) || !isset($_POST["sku"]) 
               || !isset($_POST["price"])     || !isset($_POST["weight"]) 
               || !isset($_POST["dimension"]) || !isset($_POST["preview"])) {
				return rest_ensure_response( 
					array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Request unknown!",
					)
                );
                
            }

            // Step 4: Check if ID is in valid format (integer)
			if (!is_numeric($_POST["wpid"]) || !is_numeric($_POST["ctid"]) || !is_numeric($_POST["stid"]) ) {
				return rest_ensure_response( 
					array(
						"status" => "failed",
						"message" => "Please contact your administrator. ID not in valid format!",
					)
                );
                
			}


            // Step6 : Check if all variable is not empty
            if (empty($_POST["ctid"]) 
                || empty($_POST["stid"]) 
                || empty($_POST["title"]) 
                || empty($_POST["short_info"]) 
                || empty($_POST["long_info"]) 
                || empty($_POST["sku"]) 
                || empty($_POST["price"]) 
                || empty($_POST["weight"]) 
                || empty($_POST["dimension"]) 
                || empty($_POST["preview"])) {
				return rest_ensure_response( 
					array(
						"status" => "unknown",
						"message" => "Required fields cannot be empty.",
					)
                );
                
            }
            // catch all post request
            $user = TP_Insert_Product::catch_post();


            //Check if this store id exists
            $store_id = $user['stid'];
            $get_store = $wpdb->get_row("SELECT ID FROM tp_stores  WHERE ID = $store_id  ");
                
             if ( !$get_store ) {
                return rest_ensure_response( 
                    array(
                        "status" => "error",
                        "message" => "An error occurred while fetching data to the server.",
                    )
                );
            }

       
             // Check user role 
            if (TP_Globals::verify_role( $_POST['wpid'], $_POST['stid'], 'can_add_product' )) {
                return rest_ensure_response( 
                    TP_Globals::verify_role($_POST['wpid'], $_POST['stid'], 'can_add_product' ),
                );
            }

            
            // variable for time stamp
            $later = TP_Globals::date_stamp();

            // variables for query
            $table_product = TP_PRODUCT_TABLE;
            $table_product_fields = TP_PRODUCT_FIELDS;

            $table_revs = TP_REVISIONS_TABLE;
            $table_revs_fields = TP_REVISION_FIELDS;

            $revs_type = "products";

            $wpdb->query("START TRANSACTION");

                $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('$revs_type', '0', 'title', '{$user["title"]}', '{$user["created_by"]}', '$later')");
                $title = $wpdb->insert_id;

                $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('$revs_type', '0', 'preview', '{$user["preview"]}', '{$user["created_by"]}', '$later')");
                $preview = $wpdb->insert_id;

                $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('$revs_type', '0', 'short_info', '{$user["short_info"]}', '{$user["created_by"]}', '$later')");
                $short_info = $wpdb->insert_id;

                $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('$revs_type', '0', 'long_info', '{$user["long_info"]}', '{$user["created_by"]}', '$later')");
                $long_info = $wpdb->insert_id;

                $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('$revs_type', '0', 'status', '1', '{$user["created_by"]}', '$later')");
                $status = $wpdb->insert_id;

                $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('$revs_type', '0', 'sku', '{$user["sku"]}', '{$user["created_by"]}', '$later')");
                $sku = $wpdb->insert_id;

                $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('$revs_type', '0', 'price', '{$user["price"]}', '{$user["created_by"]}', '$later')");
                $price = $wpdb->insert_id;

                $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('$revs_type', '0', 'weight', '{$user["weight"]}', '{$user["created_by"]}', '$later')");
                $weight = $wpdb->insert_id;

                $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('$revs_type', '0', 'dimension', '{$user["dimension"]}', '{$user["created_by"]}', '$later')");
                $dimension = $wpdb->insert_id;

            if ($title < 1 || $short_info < 1 || $long_info < 1 || $sku < 1 || $price < 1 || $weight < 1 || $dimension < 1 || $preview < 1 ) {
                // when insert failed rollback all inserted data
                $wpdb->query("ROLLBACK");
                return array(
                        "status" => "failed",
                        "message" => "An error occured while submitting data to database.",
                );
            }
            
            // Insert Product
            $wpdb->query("INSERT INTO $table_product $table_product_fields VALUES ('{$user["stid"]}', '{$user["ctid"]}', '$title', '$preview', '$short_info', '$long_info', '$status', '$sku', '$price', '$weight', '$dimension', '{$user["created_by"]}', '$later')");
            $product_id = $wpdb->insert_id;

            $result = $wpdb->query("UPDATE $table_revs SET `parent_id` = $product_id WHERE ID IN ($title, $preview, $short_info, $long_info, $status, $sku, $price, $weight, $dimension) ");

            if ($product_id < 1 || $result < 1 ) {
                $wpdb->query("ROLLBACK");
                return array(
                        "status" => "failed",
                        "message" => "An error occured while submitting data to database.",
                );

            }else {
            $wpdb->query("COMMIT");
                return array(
                        "status" => "success",
                        "message" => "Data has been added successfully!",
                );
            }

        }

        // Catch Post 
        public static function catch_post()
        {
                $cur_user = array();
               
                $cur_user['created_by'] = $_POST["wpid"];
                $cur_user['ctid']       = $_POST["ctid"];
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
