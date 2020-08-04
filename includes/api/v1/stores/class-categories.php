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

	class TP_Store {
        
        public static function category(){
            // Initialize WP global variable
			global $wpdb;

			//  Step1 : Verify if Datavice Plugin is Active
			if (TP_Globals::verifiy_datavice_plugin() == false) {
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
			if (!isset($_POST["wpid"]) || !isset($_POST["snky"]) || !isset($_POST["ctid"]) || !isset($_POST["types"])) {
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
			if (empty($_POST["wpid"]) || empty($_POST["snky"]) || empty($_POST["ctid"]) || empty($_POST["types"])) {
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
			
			$type = $_POST['types'];
			
			$categories = $_POST['ctid'];
			//Step 8: Get results from database 
			$result= $wpdb->get_results("SELECT
					tp_str.ID,
					tp_cat.types,
					tp_str.ctid,
					tp_rev.child_val AS title,
					( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_str.short_info ) AS `short_info`,
					( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_str.long_info ) AS `long_info`,
					( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_str.logo ) AS `logo`,
					( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_str.banner ) AS `banner`,
					( SELECT dv_rev.child_val FROM $dv_revs dv_rev WHERE ID = dv_add.street ) AS `street`,
					( SELECT brgy_name FROM $dv_geo_brgy WHERE ID = ( SELECT child_val FROM $dv_revs WHERE ID = dv_add.brgy ) ) AS brgy,
					( SELECT city_name FROM $dv_geo_city WHERE ID = ( SELECT child_val FROM $dv_revs WHERE ID = dv_add.city ) ) AS city,
					( SELECT prov_name FROM $dv_geo_prov WHERE ID = ( SELECT child_val FROM $dv_revs WHERE ID = dv_add.province ) ) AS province,
					( SELECT country_name FROM $dv_geo_count WHERE ID = ( SELECT child_val FROM $dv_revs WHERE ID = dv_add.country ) ) AS country 
				FROM
					$store_table tp_str
					INNER JOIN $table_revs tp_rev ON tp_rev.ID = tp_str.title 
					INNER JOIN $dv_address dv_add ON tp_str.address = dv_add.ID	
					INNER JOIN $categories_table tp_cat ON tp_str.ctid = tp_cat.ID 
				WHERE 
					tp_cat.types = '$type' AND  tp_str.ctid = $categories
				", OBJECT);


			if (empty($result)) {

				return rest_ensure_response( 
					array(
						"status" => "success",
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