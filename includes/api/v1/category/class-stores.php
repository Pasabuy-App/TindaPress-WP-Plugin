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


            if (TP_Globals::verifiy_datavice_plugin() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Plugin Missing!",
                    )
                );
            }

            if (TP_Globals::validate_user() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Request Unknown!",
                    )
                );
            }

            // Step1 : Sanitize all Request
			if (!isset($_POST["wpid"]) || !isset($_POST["snky"]) ) {
				return rest_ensure_response( 
					array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Request unknown!",
					)
                );
                
            }

            // Step 2: Check if ID is in valid format (integer)
			if (!is_numeric($_POST["wpid"]) ) {
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

            $table_store = TP_STORES_TABLE;
            $table_category = TP_CATEGORIES_TABLE;
            $table_revision = TP_REVISION;
            $table_address = 'dv_address';
                    
            $result = $wpdb->get_results("SELECT
                st.id,
                cat.types,
                ( SELECT child_val FROM tp_revisions WHERE ID = cat.title ) AS cat_title,
                ( SELECT child_val FROM tp_revisions WHERE ID = cat.info ) AS cat_info,
                max( IF ( st_r.child_key = 'title', st_r.child_val, '' ) ) AS title,
                max( IF ( st_r.child_key = 'short_info', st_r.child_val, '' ) ) AS short_info,
                max( IF ( st_r.child_key = 'long_info', st_r.child_val, '' ) ) AS long_info,
                max( IF ( st_r.child_key = 'logo', st_r.child_val, '' ) ) AS logo,
                max( IF ( st_r.child_key = 'banner', st_r.child_val, '' ) ) AS banner,
                addr.types AS add_types,
                ( SELECT child_val FROM dv_revisions WHERE ID = addr.STATUS ) AS STATUS,
                ( SELECT child_val FROM dv_address WHERE ID = addr.street ) AS street,
                ( SELECT brgy_name FROM dv_geo_brgy WHERE ID = ( SELECT child_val FROM dv_revisions WHERE ID = addr.brgy ) ) AS brgy,
                ( SELECT citymun_name FROM dv_geo_city WHERE ID = ( SELECT child_val FROM dv_revisions WHERE ID = addr.city ) ) AS city,
                ( SELECT prov_name FROM dv_geo_provinces WHERE ID = ( SELECT child_val FROM dv_revisions WHERE ID = addr.province ) ) AS province,
                ( SELECT country_name FROM dv_geo_countries WHERE ID = ( SELECT child_val FROM dv_revisions WHERE ID = addr.country ) ) AS country 
            FROM
                $table_store st
                INNER JOIN $table_revision st_r ON st.title = st_r.ID 
                OR st.short_info = st_r.ID 
                OR st.long_info = st_r.ID 
                OR st.logo = st_r.ID 
                OR st.banner = st_r.ID
                INNER JOIN $table_category cat ON st.ctid = cat.ID
                INNER JOIN $table_address addr ON st.address = addr.ID 
            GROUP BY
                st.id
            ");

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
