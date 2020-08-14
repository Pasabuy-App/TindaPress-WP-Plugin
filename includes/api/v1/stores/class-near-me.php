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

    class TP_Store_Near_ME {
        
        //REST API Call
        public static function listen(){
            return rest_ensure_response( 
                TP_Store_Near_ME:: listen_open()
            );
        }

        //QA done 2020-08-12 8:09 pm
        public static function listen_open(){
            global $wpdb;
            
            // NOTE :This query calculates Store in Kilometers radius which is "6371" to calculate in miles change "6371" to "3959"
            
            // Step 1: Check if prerequisites plugin are missing
            $plugin = TP_Globals::verify_prerequisites();
            if ($plugin !== true) {
                return array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. ".$plugin." plugin missing!",
                );
            }

            // Step 2: Validate user
            if (DV_Verification::is_verified() == false) {
                return array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Verification issues!",
                );
            }

            if (!isset($_POST['lat']) || !isset($_POST['long']) ) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Request unknown missing paramiters!",
                );
            }

            if (empty($_POST['lat']) || empty($_POST['long']) ) {
                return array(
                    "status" => "failed",
                    "message" => "Required fileds cannot be empty.",
                );
            }

            $lat2 = $_POST['lat'];

            $long2 = $_POST['long'];
            
            $results = $wpdb->get_results(" SELECT
                    ID,
                    IF(`add`.types = 'business', 'Business', 'Office' )as `type`,
                    ( SELECT `child_val` FROM dv_revisions WHERE ID = ( SELECT `title` FROM tp_stores WHERE ID = `add`.stid ) ) as store_name,
                    ( SELECT child_val FROM dv_revisions WHERE id = `add`.street ) AS street,
                    ( SELECT brgy_name FROM dv_geo_brgys WHERE ID = ( SELECT child_val FROM dv_revisions WHERE id = `add`.brgy ) ) AS brgy,
                    ( SELECT city_name FROM dv_geo_cities WHERE city_code = ( SELECT child_val FROM dv_revisions WHERE id = `add`.city ) ) AS city,
                    ( SELECT prov_name FROM dv_geo_provinces WHERE prov_code = ( SELECT child_val FROM dv_revisions WHERE id = `add`.province ) ) AS province,
                    ( SELECT country_name FROM dv_geo_countries WHERE id = ( SELECT child_val FROM dv_revisions WHERE id = `add`.country ) ) AS country,
                    IF (( select child_val from dv_revisions where id = `add`.`status` ) = 1, 'Active' , 'Inactive' ) AS `status`,
                     ROUND((SELECT `distance`(( SELECT child_val FROM dv_revisions WHERE ID = add.latitude ), ( SELECT child_val FROM dv_revisions WHERE ID = add.longitude ) ,'$lat2', '$long2' ) ), 3)AS `distance in kilomiters`
                FROM
                    dv_address `add` ");

            if (!$results) {
                return array(
                    "status" => "success",
                    "message" => "No results found."
                );
            }else{
                return array(
                    "status" => "success",
                    "data" => $results
                );
            }

        }
    }