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
            
            $stid = $_POST["stid"];
		

			//Step 8: Get results from database 
			$result= $wpdb->get_results("SELECT
            tp_cat.ID,
            tp_rev.child_val AS cat_status,
            ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE tp_rev.ID = tp_cat.title ) AS cat_title,
            ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE tp_rev.ID = tp_cat.info ) AS cat_info,
            tp_cat.types,
            tp_cat.`name`,
            tp_cat.created_by,
            tp_cat.date_created 
        FROM
            $categories_table tp_cat
            INNER JOIN $table_revs tp_rev ON tp_rev.ID = tp_cat.`status`
             WHERE tp_rev.child_val  =1 AND tp_cat.ID = (SELECT tp_str.ctid FROM $store_table tp_str WHERE tp_str.ID = $stid)
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