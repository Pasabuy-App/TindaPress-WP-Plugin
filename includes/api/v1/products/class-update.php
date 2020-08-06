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

    class TP_Update_Products {

        public static function listen(){
            global $wpdb;

            // Step1 : check if datavice plugin is activated
            if (TP_Globals::verifiy_datavice_plugin() == false) {
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
            if (!isset($_POST["wpid"]) 
                || !isset($_POST["snky"]) 
                || !isset($_POST["ctid"]) 
                || !isset($_POST['pdid']) 
                || !isset($_POST["stid"]) 
                || !isset($_POST["title"]) 
                || !isset($_POST["short_info"]) 
                || !isset($_POST["long_info"]) 
                || !isset($_POST["sku"]) 
                || !isset($_POST["price"]) 
                || !isset($_POST["weight"]) 
                || !isset($_POST["dimension"]) 
                || !isset($_POST["preview"])) {
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

            // Step 5: Check if ID exists
            if (!get_user_by("ID", $_POST['wpid'])) {
                return rest_ensure_response( 
                    array(
                        "status" => "failed",
                        "message" => "User not found!",
                    )
                );
                
            }


               // Step6: Sanitize all Request if empty
               if (empty($_POST["wpid"]) 
                || empty($_POST["snky"]) 
                || empty($_POST["ctid"]) 
                || empty($_POST['pdid']) 
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
                       "message" => "Required fields cannot be empty",
                   )
               );
               
           }
            // variables for query    
            $later = TP_Globals::date_stamp();
            $created_by = $_POST['wpid'];
            $parent_id = $_POST['pdid'];
            $ctid = $_POST['ctid'];
            $stid = $_POST['stid'];
            $revs_type = "products";

            $child_vals = array(
                $_POST['title'],
                $_POST['short_info'],
                $_POST['long_info'],
                $_POST['sku'],
                $_POST['price'],
                $_POST['weight'],
                $_POST['dimension'],
                $_POST['preview'],
            );

            
            $child_keys = array('title', 'short_info', 'long_info', 'sku', 'price',  'weight',  'dimension', 'preview' );
            $last_id_product = array();

            $table_revs = TP_REVISION;
            $table_product = TP_PRODUCT_TABLE;

            // query
            for ($count=0; $count < count($child_keys) ; $count++) {
                $sql = "INSERT INTO $table_revs (revs_type, parent_id, child_key , child_val, created_by, date_created  ) VALUES ('$revs_type', $parent_id, '$child_keys[$count]', '$child_vals[$count]', '$created_by', '$later')";
                // $result = $wpdb->insert('$table_product_revs', array( 'parent_id'=> $parent_id, 'child_key'=> $child_keys[$count], 'child_val' => $child_vals[$count] , 'created_by' => $created_by, 'date_created' => $later ) );
                $result_insert = $wpdb->query($sql);
                $last_id=$wpdb->insert_id;
                $last_ids[] = $last_id;

                $sql2 = "UPDATE $table_product SET  $child_keys[$count] = $last_ids[$count] WHERE ID= $parent_id ";
                $update_result = $wpdb->query($sql2);

            }
            // return if result is empty or null
            if($result_insert < 0 || $update_result <0 ){
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator!",
                    )
                );

            }else {
                // return Success
				return rest_ensure_response( 
                    array(
                        "status" => "success",
                        "message" => "Product has been updated successfully!",
                    )
                );
                
			}

        }

    }