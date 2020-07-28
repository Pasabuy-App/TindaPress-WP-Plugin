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
			if (!is_numeric($_POST["wpid"])|| !is_numeric($_POST['catid'])) {
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
            $table_store_revs = TP_STORES_REVS_TABLE;
            $table_category = TP_CATEGORIES_TABLE;

            $catid = $_POST['catid'];

                        
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
