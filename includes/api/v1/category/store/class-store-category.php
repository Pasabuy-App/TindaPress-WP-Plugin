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

	class TP_Store_Category {
        
        public static function listen(){
            // Initialize WP global variable
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
			
			//  Step2 : Validate if user is exist
			if (TP_Globals::validate_user() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Request Unknown!",
                    )
                );
            }

            // Step3 : Sanitize all Request
			if (!isset($_POST["wpid"]) || !isset($_POST["snky"]) || !isset($_POST["stid"]) ) {
				return rest_ensure_response( 
					array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Request unknown!",
					)
                );
                
            }


            // Step 4: Check if ID is in valid format (integer)
			if ( !is_numeric($_POST['stid'])) {
				return rest_ensure_response( 
					array(
						"status" => "failed",
						"message" => "Please contact your administrator. ID not in valid format!",
					)
                );
                
			}

            // Step6 : Sanitize all Request
			if ( empty($_POST["stid"])) {
				return rest_ensure_response( 
					array(
						"status" => "unknown",
						"message" => "Required fields cannot be empty.",
					)
                );
                
            }

            //Step 7: Create table name for posts (tp_categories, tp_categories_revs)
			// table names and POST Variables
            $store_table           = TP_STORES_TABLE;
            $store_revs_table      = TP_STORES_REVS_TABLE;
            $categories_table      = TP_CATEGORIES_TABLE;
            $product_table         = TP_PRODUCT_TABLE;
            $product_revs_table    = TP_PRODUCT_REVS_TABLE;
            $table_revs            = TP_REVISION_TABLE;
            // datavice table variables declarations
            $dv_geo_brgy    = DV_BRGY_TABLE;
            $dv_revs        =  DV_REVS_TABLE;
            $dv_address     = DV_ADDRESS_TABLE;
            $dv_geo_city    = DV_CITY_TABLE;
            $dv_geo_prov    = DV_PROVINCE_TABLE;
            $dv_geo_count   = DV_COUNTRY_TABLE;    
			
		

			//Step 8: Get results from database 
			$result= $wpdb->get_results("SELECT
            tp_categories.ID,
            tp_revisions.child_val AS cat_status,
            ( SELECT tp_revisions.child_val FROM tp_revisions WHERE tp_revisions.ID = tp_categories.title ) AS cat_title,
            ( SELECT tp_revisions.child_val FROM tp_revisions WHERE tp_revisions.ID = tp_categories.info ) AS cat_info,
            tp_categories.types,
            tp_categories.`name`,
            tp_categories.created_by,
            tp_categories.date_created 
        FROM
            tp_categories
            INNER JOIN tp_revisions ON tp_revisions.ID = tp_categories.`status`
            
             WHERE tp_revisions.child_val  =1 AND tp_categories.ID = (SELECT ctid FROM tp_stores WHERE ID = 2)
                        ", OBJECT);


			if (empty($result)) {

				return rest_ensure_response( 
					array(
						"status" => "faields",
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
?>