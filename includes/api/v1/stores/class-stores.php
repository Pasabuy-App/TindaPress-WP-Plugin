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
            if (TP_Globals::verifiy_datavice_plugin() == false) {
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
			if (!isset($_POST["wpid"]) || !isset($_POST["snky"]) ) {
				return rest_ensure_response( 
					array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Request unknown!",
					)
                );
                
            }

            // Step 4: Check if ID is in valid format (integer)
			if (!is_numeric($_POST["wpid"])|| !is_numeric($_POST['catid'])) {
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
            // Step 6: sanitize if all variables is empty
            if (empty($_POST["wpid"]) || empty($_POST["snky"]) ) {
				return rest_ensure_response( 
					array(
						"status" => "unknown",
						"message" => "Required fileds cannot be empty!",
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
            st.id,
            cat.types,
            (SELECT child_val FROM tp_categories_revs WHERE ID = cat.title) as cat_title,
            (SELECT child_val FROM tp_categories_revs WHERE ID = cat.info) as cat_info,
                max( IF ( st_r.child_key = 'title', st_r.child_val,'' ) ) AS title,
                max( IF ( st_r.child_key = 'short_info', st_r.child_val,'' ) ) AS short_info,
                max( IF ( st_r.child_key = 'long_info', st_r.child_val,'' ) ) AS long_info,
                max( IF ( st_r.child_key = 'logo', st_r.child_val,'' ) ) AS logo,
                max( IF ( st_r.child_key = 'banner', st_r.child_val, '') ) AS banner,
            addr.types as add_types,
            (SELECT child_val FROM tp_address_revs WHERE ID = addr.status) as status,
            (SELECT child_val FROM tp_address_revs WHERE ID = addr.street) as street,
            (SELECT brgy_name FROM dv_brgys WHERE ID = (SELECT child_val FROM tp_address_revs WHERE ID = addr.brgy)) as brgy,
            (SELECT citymun_name FROM dv_cities WHERE ID = (SELECT child_val FROM tp_address_revs WHERE ID = addr.city)) as city,
            (SELECT prov_name FROM dv_provinces WHERE ID = (SELECT child_val FROM tp_address_revs WHERE ID = addr.province)) as province,
            (SELECT country_name FROM dv_countries WHERE ID = (SELECT child_val FROM tp_address_revs WHERE ID = addr.country)) as country
            
            FROM
                tp_stores st
                INNER JOIN tp_stores_revs st_r ON st.title = st_r.ID 
                OR st.short_info = st_r.ID 
                OR st.long_info = st_r.ID 
                OR st.logo = st_r.ID 
                OR st.banner = st_r.ID
                INNER JOIN tp_categories cat ON st.ctid = cat.ID 
                INNER JOIN tp_address addr ON st.address = addr.ID 
                WHERE st.ctid = $catid
                GROUP BY 
            st.id
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

        public static function get_store_category_list(){
            global $wpdb;
            

        }
        
    }
