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

    class TP_Select_Store_Category_Product {
        
        public static function listen(){
            global $wpdb;

            // Step1 : validate if datavice is activated
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
			if (!isset($_POST["wpid"]) || !isset($_POST["snky"]) || !isset($_POST['stid']) || !isset($_POST['catid'])) {
				return rest_ensure_response( 
					array(
						"status" => "unknown",
						"message" => "Please contact your administrator.  Request unknown missing parameters!",
					)
                );
                
            }


            // Step 4: Check if ID is in valid format (integer)
			if (!is_numeric($_POST["wpid"]) || !is_numeric($_POST["stid"]) || !is_numeric($_POST["catid"]) ) {
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
			if (empty($_POST["wpid"]) || empty($_POST["snky"]) || empty($_POST['stid']) || empty($_POST['catid'])) {
				return rest_ensure_response( 
					array(
						"status" => "unknown",
						"message" => "Required fields cannot be empty",
					)
                );
                
            }
            

            
            //Step 7: Create table name for all tables needed
            $store_table           = TP_STORES_TABLE;
            $store_revs_table      = TP_STORES_REVS_TABLE;
            $categories_table      = TP_CATEGORIES_TABLE;
            $categories_revs_table = TP_CATEGORIES_REVS_TABLE;
            $product_table         = TP_PRODUCT_TABLE;
            $product_revs_table    = TP_PRODUCT_REVS_TABLE;
            
            $table_revs = TP_REVISION;
            $catid = $_POST['catid'];
            $stid = $_POST['stid'];

            //Step 8: Fetch data from database
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
                INNER JOIN $table_revs prd_r ON prd.title = prd_r.ID 
                OR prd.preview = prd_r.ID 
                OR prd.short_info = prd_r.ID 
                OR prd.long_info = prd_r.ID 
                OR prd.`status` = prd_r.ID 
                OR prd.sku = prd_r.ID 
                OR prd.price = prd_r.ID 
                OR prd.weight = prd_r.ID 
                OR prd.dimension = prd_r.ID
                INNER JOIN $store_table st ON prd.stid = st.ID
                INNER JOIN $table_revs st_r ON st.title = st_r.ID
                INNER JOIN $categories_table cat ON prd.ctid = cat.ID
                INNER JOIN $table_revs cat_r ON cat.title = cat_r.ID 
                OR cat.info = cat_r.ID 
            WHERE
                prd.ctid = $catid 
                AND prd.stid = $stid
            GROUP BY
                prd_r.parent_id DESC
            ", OBJECT);

            //Step 9: Return result
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