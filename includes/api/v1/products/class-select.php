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
			if (!isset($_POST["wpid"]) || !isset($_POST["snky"]) ) {
				return rest_ensure_response( 
					array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Request unknown!",
					)
                );
                
            }

            // Step 4: Check if ID is in valid format (integer)
			if (!is_numeric($_POST["wpid"]) || !is_numeric($_POST["ctid"])  ) {
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
            $table_product_revs = TP_PRODUCT_REVS_TABLE;
            $table_stores = TP_STORES_TABLE;
            $table_stores_revs = TP_STORES_REVS_TABLE;
            $table_categories = TP_CATEGORIES_TABLE;
            $table_categories_revs = TP_CATEGORIES_REVS_TABLE;
            $table_revs = TP_REVISION;

            // Step7 : if last insert id is not in Request
            if(!isset($_POST['lid'])){

                // product list query
                $result =  $wpdb->get_results("SELECT
                        prod.id AS id,
                        revs_2.child_val AS store_name,
                        max( IF ( revs_2.child_key = 'title', revs_2.child_val, '' ) ) AS cat_title,
                        max( IF ( revs_2.child_key = 'info', revs_2.child_val, '' ) ) AS cat_info,
                        max( IF ( revs.child_key = 'title', revs.child_val, '' ) ) AS title,
                        max( IF ( revs.child_key = 'preview', revs.child_val, '' ) ) AS preview,
                        max( IF ( revs.child_key = 'short_info', revs.child_val, '' ) ) AS short_info,
                        max( IF ( revs.child_key = 'long_info', revs.child_val, '' ) ) AS long_info,
                        max( IF ( revs.child_key = 'status', revs.child_val, '' ) ) AS STATUS,
                        max( IF ( revs.child_key = 'sku', revs.child_val, '' ) ) AS sku,
                        max( IF ( revs.child_key = 'price', revs.child_val, '' ) ) AS price,
                        max( IF ( revs.child_key = 'weight', revs.child_val, '' ) ) AS weight,
                        max( IF ( revs.child_key = 'dimension', revs.child_val, '' ) ) AS dimension,
                        revs.created_by,
                        prod.date_created 
                    FROM
                        $table_product prod
                        INNER JOIN $table_revs revs ON prod.title = revs.ID 
                        OR prod.preview = revs.ID 
                        OR prod.short_info = revs.ID 
                        OR prod.long_info = revs.ID 
                        OR prod.`status` = revs.ID 
                        OR prod.sku = revs.ID 
                        OR prod.price = revs.ID 
                        OR prod.weight = revs.ID 
                        OR prod.dimension = revs.ID
                        INNER JOIN $table_stores str ON prod.stid = str.ID
                        INNER JOIN $table_revs revs_1 ON str.title = revs_1.ID
                        INNER JOIN $table_categories cat ON prod.ctid = cat.ID
                        INNER JOIN $table_revs revs_2 ON cat.title = revs_2.ID 
                        OR cat.info = revs_2.ID 
                    GROUP BY
                        revs.parent_id DESC LIMIT 12
                ");
                $last_id = min($result);

                // Return result
                return rest_ensure_response( 
                    array(
                        "status" => "success",
                        "data" => array(
                            'list' => $result, 
                            'last_id' => $last_id
                        )
                    )
                );

            }else{
                // Sanitize requirest if numeric
                if(!is_numeric($_POST["lid"])){
					return rest_ensure_response( 
						array(
							"status" => "failed",
							"message" => "Parameters not in valid format!",
						)
					);

                }
                // Sanitize requirest if not empty
                if(empty($_POST["lid"])){
					return rest_ensure_response( 
						array(
							"status" => "unknown",
							"message" => "Required fields cannot be empty.",
						)
					);

                }
                
                // variable of query
                $get_last_id = $_POST['lid'];
                $add_feeds = $get_last_id - 5;


                // query
                $result =  $wpdb->get_results("SELECT
                    prod.id AS id,
                    revs_2.child_val AS store_name,
                    max( IF ( revs_2.child_key = 'title', revs_2.child_val, '' ) ) AS cat_title,
                    max( IF ( revs_2.child_key = 'info', revs_2.child_val, '' ) ) AS cat_info,
                    max( IF ( revs.child_key = 'title', revs.child_val, '' ) ) AS title,
                    max( IF ( revs.child_key = 'preview', revs.child_val, '' ) ) AS preview,
                    max( IF ( revs.child_key = 'short_info', revs.child_val, '' ) ) AS short_info,
                    max( IF ( revs.child_key = 'long_info', revs.child_val, '' ) ) AS long_info,
                    max( IF ( revs.child_key = 'status', revs.child_val, '' ) ) AS STATUS,
                    max( IF ( revs.child_key = 'sku', revs.child_val, '' ) ) AS sku,
                    max( IF ( revs.child_key = 'price', revs.child_val, '' ) ) AS price,
                    max( IF ( revs.child_key = 'weight', revs.child_val, '' ) ) AS weight,
                    max( IF ( revs.child_key = 'dimension', revs.child_val, '' ) ) AS dimension,
                    revs.created_by,
                    prod.date_created 
                FROM
                    $table_product prod
                    INNER JOIN $table_revs revs ON prod.title = revs.ID 
                    OR prod.preview = revs.ID 
                    OR prod.short_info = revs.ID 
                    OR prod.long_info = revs.ID 
                    OR prod.`status` = revs.ID 
                    OR prod.sku = revs.ID 
                    OR prod.price = revs.ID 
                    OR prod.weight = revs.ID 
                    OR prod.dimension = revs.ID
                    INNER JOIN $table_stores str ON prod.stid = str.ID
                    INNER JOIN $table_revs revs_1 ON str.title = revs_1.ID
                    INNER JOIN $table_categories cat ON prod.ctid = cat.ID
                    INNER JOIN $table_revs revs_2 ON cat.title = revs_2.ID 
                    OR cat.info = revs_2.ID 
                    WHERE prod.id BETWEEN $add_feeds AND ($get_last_id - 1) 
                GROUP BY
                    revs.parent_id DESC LIMIT 12
                
              
                ");

                //Step 8: Check if array count is 0 , return error message if true
				if (count($result) < 1) {

					return rest_ensure_response( 
						array(
							"status" => "failed",
							"message" => "No more posts to see",
						)
                    );
                    
				} else {

					//Pass the last id
                    $last_id = min($result);
                    //Step 9: Return a success message and a complete object
                    return rest_ensure_response( 
                        array(
                            "status" => "success",
                            "data" => array(
                                'list' => $result, 
                                'last_id' => $last_id
                            )
                        )
                    );
                    
                }
                
            }
        }

    }