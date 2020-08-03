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

    class TP_Storelist {

        public static function initialize(){
            global $wpdb;

            // step1 check if datavice plugin is activated
            if (TP_Globals::verifiy_datavice_plugin() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Plugin Missing!",
                    )
                );
            }
            // step2: check if user is existed in database
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


            // Step6 : Sanitize all Request
			if (empty($_POST["wpid"]) || empty($_POST["snky"]) ) {
				return rest_ensure_response( 
					array(
						"status" => "unknown",
						"message" => "Required fields cannot be empty",
					)
                );
                
            }

            // table names and POST Variables
            $store_table           = TP_STORES_TABLE;
            $store_revs_table      = TP_STORES_REVS_TABLE;
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
            $dv_geo_city    = DV_CTY_TABLE;
            $dv_geo_prov    = DV_PRV_TABLE;
            $dv_geo_court   = DV_COUNTRY_TABLE;     

            // query
            $result = $wpdb->get_results("SELECT
                st.id,
                cat.types,
                    ( SELECT child_val FROM $table_revs WHERE ID = cat.title ) AS cat_title,
                    ( SELECT child_val FROM $table_revs WHERE ID = cat.info ) AS cat_info,
                    max( IF ( st_r.child_key = 'title', st_r.child_val, '' ) ) AS title,
                    max( IF ( st_r.child_key = 'short_info', st_r.child_val, '' ) ) AS short_info,
                    max( IF ( st_r.child_key = 'long_info', st_r.child_val, '' ) ) AS long_info,
                    max( IF ( st_r.child_key = 'logo', st_r.child_val, '' ) ) AS logo,
                    max( IF ( st_r.child_key = 'banner', st_r.child_val, '' ) ) AS banner,
                    addr.types AS add_types,
                    ( SELECT child_val FROM $dv_revs WHERE ID = addr.STATUS ) AS STATUS,
                    ( SELECT child_val FROM $dv_address WHERE ID = addr.street ) AS street,
                    ( SELECT brgy_name FROM $dv_geo_brgy WHERE ID = ( SELECT child_val FROM $dv_revs WHERE ID = addr.brgy ) ) AS brgy,
                    ( SELECT citymun_name FROM $dv_geo_city WHERE ID = ( SELECT child_val FROM $dv_revs WHERE ID = addr.city ) ) AS city,
                    ( SELECT prov_name FROM $dv_geo_prov WHERE ID = ( SELECT child_val FROM $dv_revs WHERE ID = addr.province ) ) AS province,
                    ( SELECT country_name FROM $dv_geo_court WHERE ID = ( SELECT child_val FROM $dv_revs WHERE ID = addr.country ) ) AS country 
            FROM
                $store_table st
                INNER JOIN $table_revs st_r ON st.title = st_r.ID 
                OR st.short_info = st_r.ID 
                OR st.long_info = st_r.ID 
                OR st.logo = st_r.ID 
                OR st.banner = st_r.ID
                INNER JOIN $categories_table cat ON st.ctid = cat.ID
                INNER JOIN $dv_address addr ON st.address = addr.ID 
            GROUP BY
                st.id
            ");
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
