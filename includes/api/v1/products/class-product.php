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

            //Step 7: Create table name for posts (tp_categories, tp_categories_revs)
			// table names and POST Variables
            $store_table           = TP_STORES_TABLE;
            $tp_categories_table   = TP_CATEGORIES_TABLE;
            $product_table         = TP_PRODUCT_TABLE;
            $product_revs_table    = TP_PRODUCT_REVS_TABLE;
            $tp_revs               = TP_REVISION_TABLE;
            // datavice table variables declarations
            $dv_geo_brgy    = DV_BRGY_TABLE;
            $dv_revs        =  DV_REVS_TABLE;
            $dv_address     = DV_ADDRESS_TABLE;
            $dv_geo_city    = DV_CITY_TABLE;
            $dv_geo_prov    = DV_PROVINCE_TABLE;
            $dv_geo_count   = DV_COUNTRY_TABLE;    

            $types = $_POST['types'];
            $stid = $_POST['stid'];
            $ctid = $_POST['ctid'];

            $result = $wpdb->get_results("SELECT
                    tp_prod.ID,
                    tp_cat.types,
                    tp_rev.child_val AS title,
                    tp_reevs.child_val AS category_name,
                    ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_cat.title ) AS `type`, 
                    ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.preview ) AS `preview`,
                    ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.short_info ) AS `short_info`,
                    ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.long_info ) AS `long_info`,
                    ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.`status` ) AS `status`,
                    ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.sku ) AS `sku`,
                    ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.price ) AS `price`,
                    ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.weight ) AS `weight`,
                    ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.dimension ) AS `dimension` 
                FROM
                    tp_products tp_prod
                    INNER JOIN $tp_revs tp_rev ON tp_rev.ID = tp_prod.title
                    INNER JOIN $store_table  tp_strs ON tp_prod.stid = tp_strs.ID
                    INNER JOIN $tp_categories_table tp_cat ON tp_cat.ID = tp_strs.ctid
                    INNER JOIN $tp_revs tp_reevs ON tp_reevs.ID = tp_cat.title 
                WHERE
                    tp_prod.stid = $stid AND tp_cat.types = '$types' AND tp_strs.ctid = $ctid");

       
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
