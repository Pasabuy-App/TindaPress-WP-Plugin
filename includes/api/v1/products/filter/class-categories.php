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

    class TP_Product {
        
        public static function initialize(){
            global $wpdb;

            if (TP_Globals::validate_user() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Request Unknown!",
                    )
                );
            }




            // Step1 : Sanitize all Request
			if (!isset($_POST["wpid"]) || !isset($_POST["snky"]) || !isset($_POST['stid']) || !isset($_POST['catid'])) {
				return rest_ensure_response( 
					array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Request unknown!",
					)
                );
                
            }


            // Step 2: Check if ID is in valid format (integer)
			if (!is_numeric($_POST["wpid"]) || !is_numeric($_POST["stid"]) || !is_numeric($_POST["catid"]) ) {
				return rest_ensure_response( 
					array(
						"status" => "failed",
						"message" => "Please contact your administrator. ID not in valid format!",
					)
                );
                
			}

			// Step 3: Check if ID exists
			if (!get_user_by("ID", $_POST['wpid'])) {
				return rest_ensure_response( 
					array(
						"status" => "failed",
						"message" => "User not found!",
					)
                );
                
            }
            

            
            //Step 4: Create table name for posts (tp_categories, tp_categories_revs)

            
            $store_table           = STORES_TABLE;
            $store_revs_table      = STORES_REVS_TABLE;
            $categories_table      = CATEGORIES_TABLE;
            $categories_revs_table = CATEGORIES_REVS_TABLE;
            $product_table         = PRODUCT_TABLE;
            $product_revs_table    = PRODUCT_REVS_TABLE;
            
            $result = $wpdb->get_results("SELECT
                prd.id AS id,
                st_r.child_val AS store_name,
                max( IF ( cat_r.child_key = 'title', cat_r.child_val,  '' ) ) AS cat_title,
                max( IF ( cat_r.child_key = 'info', cat_r.child_val, ''  ) ) AS cat_info,
                max( IF ( prd_r.child_key = 'title', prd_r.child_val, ''  ) ) AS title,
                max( IF ( prd_r.child_key = 'preview', prd_r.child_val,  '' ) ) AS preview,
                max( IF ( prd_r.child_key = 'short_info', prd_r.child_val, ''  ) ) AS short_info,
                max( IF ( prd_r.child_key = 'long_info', prd_r.child_val, ''  ) ) AS long_info,
                max( IF ( prd_r.child_key = 'status', prd_r.child_val, ''  ) ) AS STATUS,
                max( IF ( prd_r.child_key = 'sku', prd_r.child_val, ''  ) ) AS sku,
                max( IF ( prd_r.child_key = 'price', prd_r.child_val, ''  ) ) AS price,
                max( IF ( prd_r.child_key = 'weight', prd_r.child_val,  '' ) ) AS weight,
                max( IF ( prd_r.child_key = 'dimension', prd_r.child_val, ''  ) ) AS dimension,
                prd_r.created_by,
                prd.date_created 
            FROM
                $product_table prd
                INNER JOIN $product_revs_table prd_r ON prd.title = prd_r.ID 
                OR prd.preview = prd_r.ID 
                OR prd.short_info = prd_r.ID 
                OR prd.long_info = prd_r.ID 
                OR prd.`status` = prd_r.ID 
                OR prd.sku = prd_r.ID 
                OR prd.price = prd_r.ID 
                OR prd.weight = prd_r.ID 
                OR prd.dimension = prd_r.ID
                INNER JOIN $store_table st ON prd.stid = st.ID
                INNER JOIN $store_revs_table st_r ON st.title = st_r.ID
                INNER JOIN $categories_table cat ON prd.ctid = cat.ID
                INNER JOIN $categories_revs_table cat_r ON cat.title = cat_r.ID 
                OR cat.info = cat_r.ID 
            WHERE
                prd.ctid = 1 
                AND prd.stid = 1 
            GROUP BY
                prd_r.parent_id DESC
            ", OBJECT);
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