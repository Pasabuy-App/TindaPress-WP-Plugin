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

    class TP_Select_Product {
        
        public static function listen(){
            global $wpdb;

            // Step1 : validate if datavice plugin is activated
            if (TP_Globals::verify_datavice_plugin() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Plugin Missing!",
                    )
                );
            }
            
            //  Step2 : Validate if user is exist
			if (DV_Verification::is_verified() == false) {
                return array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Request Unknown!",
                );
                
            }
            
            // Step3 : Sanitize all Request
			if (!isset($_POST["wpid"]) || !isset($_POST["snky"]) ) {
				return rest_ensure_response( 
					array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Request unknown!",
					)
                );
                
            }

            // Step 4: Check if ID is in valid format (integer)
			if (!is_numeric($_POST["wpid"])  ) {
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

            // Step6 : Sanitize all Request if empty
			if (empty($_POST["wpid"]) || empty($_POST["snky"]) ) {
				return rest_ensure_response( 
					array(
						"status" => "unknown",
						"message" => "Required fields cannot be empty.",
					)
                );
                
            }
            // table names variable for query
            $table_product = TP_PRODUCT_TABLE;
            $table_stores = TP_STORES_TABLE;
            $table_categories = TP_CATEGORIES_TABLE;
            $table_revs = TP_REVISION_TABLE;

            // Step7 : if last insert id is not in Request
            if(!isset($_POST['lid'])){

                // product list query
                $result =  $wpdb->get_results("SELECT
                    tp_prod.ID,
                    ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE tp_rev.ID = ( SELECT title FROM $table_stores WHERE ID = tp_prod.stid ) ) AS store_title,
                    ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE tp_rev.ID = ( SELECT title FROM $table_categories WHERE ID = tp_prod.ctid ) ) AS category_title,
                    ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE tp_rev.ID = tp_prod.title ) AS product_title,
                    ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE tp_rev.ID = tp_prod.preview ) AS product_preview,
                    ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE tp_rev.ID = tp_prod.short_info ) AS product_short_info,
                    ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE tp_rev.ID = tp_prod.long_info ) AS product_long_info,
                    ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE tp_rev.ID = tp_prod.sku ) AS product_sku,
                    ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE tp_rev.ID = tp_prod.price ) AS product_price,
                    ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE tp_rev.ID = tp_prod.weight ) AS product_weight,
                    ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE tp_rev.ID = tp_prod.dimension ) AS product_dimension,
                    tp_prod.date_created 
                FROM
                    $table_product tp_prod 
                GROUP BY
                    tp_prod.ID DESC
                    LIMIT 12
                ");
                $last_id = min($result);

                // Return result
                return array(
                        "status" => "success",
                        "data" => array(
                            'list' => $result, 
                            'last_id' => $last_id
                        )
                );

            }else{
                // Sanitize requirest if numeric
                if(!is_numeric($_POST["lid"])){
					return array(
							"status" => "failed",
							"message" => "Parameters not in valid format!",
					);

                }
                // Sanitize requirest if not empty
                if(empty($_POST["lid"])){
					return array(
							"status" => "unknown",
							"message" => "Required fields cannot be empty.",
					);

                }
                
                // variable of query
                $get_last_id = $_POST['lid'];
                $add_feeds = $get_last_id - 5;


                // query
                $result =  $wpdb->get_results("SELECT
                       tp_prod.ID,
                        ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE tp_rev.ID = ( SELECT title FROM $table_stores WHERE ID = tp_prod.stid ) ) AS store_title,
                        ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE tp_rev.ID = ( SELECT title FROM $table_categories WHERE ID = tp_prod.ctid ) ) AS category_title,
                        ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE tp_rev.ID = tp_prod.title ) AS product_title,
                        ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE tp_rev.ID = tp_prod.preview ) AS product_preview,
                        ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE tp_rev.ID = tp_prod.short_info ) AS product_short_info,
                        ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE tp_rev.ID = tp_prod.long_info ) AS product_long_info,
                        ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE tp_rev.ID = tp_prod.sku ) AS product_sku,
                        ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE tp_rev.ID = tp_prod.price ) AS product_price,
                        ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE tp_rev.ID = tp_prod.weight ) AS product_weight,
                        ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE tp_rev.ID = tp_prod.dimension ) AS product_dimension,
                        tp_prod.date_created 
                    FROM
                        $table_product tp_prod 
                    WHERE tp_prod.id BETWEEN $add_feeds AND ($get_last_id - 1) 
                        GROUP BY
                        tp_prod.ID DESC LIMIT 12
                        ");

                //Step 8: Check if array count is 0 , return error message if true
				if (!$result) {
					return array(
							"status" => "failed",
							"message" => "No more posts to see",
                    );
                    
				} else {

					//Pass the last id
                    $last_id = min($result);
                    //Step 9: Return a success message and a complete object
                    return array(
                            "status" => "success",
                            "data" => array(
                                'list' => $result, 
                                'last_id' => $last_id
                            )
                    );
                    
                }
                
            }
        }

    }