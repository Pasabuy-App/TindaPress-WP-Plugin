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

    class TP_Listing_Inactive_Store {
        public static function listen(){
            global $wpdb;

                  // Step1 : check if datavice plugin is activated
                  if (TP_Globals::verify_datavice_plugin() == false) {
                    return rest_ensure_response( 
                        array(
                            "status" => "unknown",
                            "message" => "Please contact your administrator. Plugin Missing!",
                        )
                    );
                }
                
                // Step2 : Check if wpid and snky is valid
                if (DV_Verification::is_verified() == false) {
                    return rest_ensure_response( 
                        array(
                            "status" => "unknown",
                            "message" => "Please contact your administrator. Request Unknown!",
                        )
                    );
                }


            $result = $wpdb->get_results("SELECT
                    tp_str.ID,
                    ( SELECT tp_rev.child_val FROM tp_revisions tp_rev WHERE ID = tp_str.short_info ) AS `short_info`,
                    ( SELECT tp_rev.child_val FROM tp_revisions tp_rev WHERE ID = tp_str.long_info ) AS `long_info`,
                    ( SELECT tp_rev.child_val FROM tp_revisions tp_rev WHERE ID = tp_str.logo ) AS `logo`,
                    ( SELECT tp_rev.child_val FROM tp_revisions tp_rev WHERE ID = tp_str.banner ) AS `banner`,
                    ( SELECT dv_revisions.child_val FROM dv_revisions WHERE ID = dv_add.street ) AS `street`,
                    ( SELECT brgy_name FROM dv_geo_brgys WHERE ID = ( SELECT child_val FROM dv_revisions WHERE ID = dv_add.brgy ) ) AS brgy,
                    ( SELECT citymun_name FROM dv_geo_city WHERE city_code = ( SELECT child_val FROM dv_revisions WHERE ID = dv_add.city ) ) AS city,
                    ( SELECT prov_name FROM dv_geo_province WHERE prov_code = ( SELECT child_val FROM dv_revisions WHERE ID = dv_add.province ) ) AS province,
                    ( SELECT country_name FROM dv_geo_countries WHERE ID = ( SELECT child_val FROM dv_revisions WHERE ID = dv_add.country ) ) AS country 
                FROM
                    tp_stores tp_str
                    INNER JOIN tp_revisions tp_rev ON tp_rev.ID = tp_str.`status` 
                    INNER JOIN dv_address dv_add ON tp_str.address = dv_add.ID	
                    WHERE tp_rev.child_val = 0
                ");


            if (!$result ) {
                return array(
                    "status" => "failed",
                    "message" => "An error occured while fetching data to database.",
                );
            }else{
                return  array(
                    "status" => "success",
                    "data" => array(
                        'list' => $result, 
                        
                    )
                );
            }
        }
    }