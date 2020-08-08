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
    // NOTE : this function is for select product with store id and category id 
    class TP_Products {

        // GET product by store  product category ID
        public static function listen(){
            global $wpdb;

			//  Step1 : Verify if Datavice Plugin is Active
			if (TP_Globals::verify_datavice_plugin() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Plugin Missing!",
                    )
                );
			}
            
            // Step2 : Check if wpid and snky is valid
            if (DV_Verification::is_verified() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Request Unknown!",
                    )
                );
            }

            // Step3 : Sanitize all Request
			if (!isset($_POST["wpid"]) || !isset($_POST["snky"]) || !isset($_POST["ctid"]) || !isset($_POST["types"]) || !isset($_POST["stid"])) {
				return rest_ensure_response( 
					array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Request unknown!",
					)
                );
                
            }

            // Step 4: Check if ID is in valid format (integer)
			if (!is_numeric($_POST["wpid"]) || !is_numeric($_POST['ctid'])) {
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
			if (empty($_POST["wpid"]) || empty($_POST["snky"]) || empty($_POST["ctid"]) || empty($_POST["types"])  || empty($_POST["stid"])) {
				return rest_ensure_response( 
					array(
						"status" => "unknown",
						"message" => "Required fields cannot be empty.",
					)
                );
                
            }

            // Create table name for posts (tp_categories, tp_categories_revs)
            $store_table           = TP_STORES_TABLE;
            $tp_categories_table   = TP_CATEGORIES_TABLE;
            $product_table         = TP_PRODUCT_TABLE;
            $tp_revs               = TP_REVISION_TABLE;
     
            $types = $_POST['types'];
            $stid = $_POST['stid'];
            $ctid = $_POST['ctid'];

            $result = $wpdb->get_results("SELECT
                    tp_prod.ID,
                    ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE tp_rev.ID = ( SELECT title FROM $store_table WHERE ID = tp_prod.stid ) ) AS store_title,
                    ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE tp_rev.ID = ( SELECT title FROM $tp_categories_table WHERE ID = tp_prod.ctid ) ) AS category_title,
                    ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE tp_rev.ID = tp_prod.title ) AS product_title,
                    ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE tp_rev.ID = tp_prod.preview ) AS product_preview,
                    ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE tp_rev.ID = tp_prod.short_info ) AS product_short_info,
                    ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE tp_rev.ID = tp_prod.long_info ) AS product_long_info,
                    ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE tp_rev.ID = tp_prod.sku ) AS product_sku,
                    ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE tp_rev.ID = tp_prod.price ) AS product_price,
                    ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE tp_rev.ID = tp_prod.weight ) AS product_weight,
                    ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE tp_rev.ID = tp_prod.dimension ) AS product_dimension,
                    tp_prod.date_created 
                FROM
                    $product_table tp_prod 
                WHERE tp_prod.stid = $stid AND tp_prod.ctid = $ctid");

       
            if (empty($result)) {

                return rest_ensure_response( 
                    array(
                        "status" => "failed",
                        "message" => "Please contact your Administrator. Empty result"
                        
                    )
                );

            }else {
                
                // reutrn success result
                return rest_ensure_response( 
                    array(
                        "status" => "success",
                        "data" => array(
                            'list' => $result, 
                        
                        )
                    )
                );
            }


        }


    }
