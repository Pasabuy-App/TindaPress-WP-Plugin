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

    class TP_StorebyCategory {

        public static function initialize(){
            global $wpdb;
            
            // Step1: verify of datavice plugin is activated
            if (TP_Globals::verify_datavice_plugin() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Plugin Missing!",
                    )
                );
            }

            // Step2 : validate user if activated
            if (TP_Globals::validate_user() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Request Unknown!",
                    )
                );
            }

            // Step3 : Sanitize all Request

            
            // Step 4: sanitize if all variables is empty
            if (empty($_POST["catid"]) ) {
				return rest_ensure_response( 
					array(
						"status" => "unknown",
						"message" => "Required fileds cannot be empty!",
					)
                );
                
            }

            // Step 5: Check if ID is in valid format (integer)
			if (!is_numeric($_POST['catid'])) {
				return rest_ensure_response( 
					array(
						"status" => "failed",
						"message" => "Please contact your administrator. ID not in valid format!",
					)
                );
                
			}

            // declater table names to vatiable
            $table_store = TP_STORES_TABLE;
            $table_store_revs = TP_STORES_REVS_TABLE;
            $table_category = TP_CATEGORIES_TABLE;
            $catid = $_POST['catid'];

            // Step7 : Query
            $result = $wpdb->get_results("SELECT
            tp_stores.ID,
            (select child_val from tp_revisions where id = (select title from tp_categories where id = tp_stores.ctid)) as cat,
            (select child_val from tp_revisions where id = tp_stores.title) as stname,
            (select child_val from tp_revisions where id = tp_stores.short_info) as bio,
            (select child_val from tp_revisions where id = tp_stores.long_info) as details,
            (select child_val from tp_revisions where id = tp_stores.logo) as icon,
            (select child_val from tp_revisions where id = tp_stores.banner) as bg,
            (select child_val from tp_revisions where id = tp_stores.`status`) as stats,
            (select child_val from dv_revisions where id = (select street from dv_address where id = tp_stores.address)) as street,
            (select brgy_name from dv_geo_brgys where id = (select child_val from dv_revisions where id = (select brgy from dv_address where id = tp_stores.address))) as brgy,
            (select citymun_name from dv_geo_cities where city_code = (select child_val from dv_revisions where id = (select city from dv_address where id = tp_stores.address))) as city,
            (select prov_name from dv_geo_provinces where prov_code = (select child_val from dv_revisions where id = (select province from dv_address where id = tp_stores.address))) as province,
            (select country_name from dv_geo_countries where id = (select child_val from dv_revisions where id = (select country from dv_address where id = tp_stores.address))) as country,
            (select meta_value from wp_usermeta where user_id = tp_stores.created_by and meta_key = 'nickname') as users,
            tp_stores.date_created
            FROM
            tp_stores
            where tp_stores.ctid = '$catid'
            ");
            
            
            // Step8 : Return Result 
            return rest_ensure_response( 
                array(
                    "status" => "success",
                    "data" => array(
                        'list' => $result, 
                    
                    )
                )
            );

        }   

        public static function search_store(){
            global $wpdb;

              // Step 1 : Verfy if Datavice Plugin is Activated
			if (TP_Globals::verifiy_datavice_plugin() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Plugin Missing!",
                    )
                );
			}
			//step 2: validate User
			if (TP_Globals::validate_user() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Request Unknown!",
                    )
                );
            }

            // Step3 : Sanitize all Request
			if (!isset($_POST["wpid"]) || !isset($_POST["snky"]) || !isset($_POST['search']) ) {
				return rest_ensure_response( 
					array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Request unknown!",
					)
                );
                
            }

             // Step6 : Sanitize all Request if emply
			if (empty($_POST["wpid"]) || empty($_POST["snky"]) || empty($_POST['search']) ) {
				return rest_ensure_response( 
					array(
						"status" => "unknown",
						"message" => "Required fields cannot be empyty.",
					)
                );
                
            }


            // Step 4: Check if ID is in valid format (integer)
			if (!is_numeric($_POST["wpid"]) ) {
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
            $value = $_POST['search'];

            // table names and POST Variables
            $store_table           = TP_STORES_TABLE;
            $categories_table      = TP_CATEGORIES_TABLE;
            $categories_revs_table = TP_CATEGORIES_REVS_TABLE;
            $product_table         = TP_PRODUCT_TABLE;
            $product_revs_table    = TP_PRODUCT_REVS_TABLE;
            $table_revs            = TP_REVISION_TABLE;
            $table_address = 'dv_address';
            // datavice table variables declarations
            $dv_geo_brgy    = DV_BRGY_TABLE;
            $dv_revs        =  DV_REVS_TABLE;
            $dv_address     = DV_ADDRESS_TABLE;
            $dv_geo_city    = DV_CITY_TABLE;
            $dv_geo_prov    = DV_PROVINCE_TABLE;
            $dv_geo_court   = DV_COUNTRY_TABLE;     
            // query
            $result = $wpdb->get_results("SELECT
                tp_str.ID,
                tp_rev.child_val AS title,
                ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_str.short_info ) AS `short_info`,
                ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_str.long_info ) AS `long_info`,
                ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_str.logo ) AS `logo`,
                ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_str.banner ) AS `banner`,
                ( SELECT dv_rev.child_val FROM $dv_revs as dv_rev WHERE ID = dv_add.street ) AS `street`,
                ( SELECT brgy_name FROM $dv_geo_brgy WHERE ID = ( SELECT child_val FROM $dv_revs WHERE ID = dv_add.brgy ) ) AS brgy,
                ( SELECT city_name FROM $dv_geo_city WHERE ID = ( SELECT child_val FROM $dv_revs WHERE ID = dv_add.city ) ) AS city,
                ( SELECT prov_name FROM $dv_geo_prov WHERE ID = ( SELECT child_val FROM $dv_revs WHERE ID = dv_add.province ) ) AS province,
                ( SELECT country_name FROM $dv_geo_court WHERE ID = ( SELECT child_val FROM $dv_revs WHERE ID = dv_add.country ) ) AS country 
            FROM
                $store_table tp_str
                INNER JOIN $table_revs tp_rev ON tp_rev.ID = tp_str.title 
                INNER JOIN $dv_address dv_add ON tp_str.address = dv_add.ID	
            WHERE
                tp_rev.child_val REGEXP '^$value';
                ");


            if (empty( $result) ) {
                // reutrn success result
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. No Product with this value"
                    )
                );

            }else{
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
