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

    class TP_Store_Nearme {
        
        //REST API Call
        public static function listen(){
            return rest_ensure_response( 
                TP_Store_Nearme:: listen_open()
            );
        }

        //QA done 2020-08-12 8:09 pm
        public static function listen_open(){
            global $wpdb;

            $table_store = TP_STORES_TABLE;
            $table_revision = TP_REVISIONS_TABLE;
            $table_address = DV_ADDRESS_TABLE;
            $table_dv_revision = DV_REVS_TABLE;
            $table_brgy = DV_BRGY_TABLE;
            $table_city = DV_CITY_TABLE;
            $table_province = DV_PROVINCE_TABLE;
            $table_country = DV_COUNTRY_TABLE;
            $table_category = TP_CATEGORIES_TABLE;
            $table_contacts = DV_CONTACTS_TABLE;
            $table_dv_revisions = DV_REVS_TABLE;
            
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

            // Step 2: Sanitize Request
            if (!isset($_POST['lat']) || !isset($_POST['long']) ) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Request unknown missing paramiters!",
                );
            }
            
            // Step 2: Sanitize Request
            if (empty($_POST['lat']) || empty($_POST['long']) ) {
                return array(
                    "status" => "failed",
                    "message" => "Required fileds cannot be empty.",
                );
            }

            $lat2 = $_POST['lat'];
            $long2 = $_POST['long'];

            // Step 4: Start query
            $results = $wpdb->get_results(" SELECT
                tp_str.ID,
                ( SELECT tp_rev.child_val FROM $table_revision tp_rev WHERE ID = tp_str.short_info ) AS `short_info`,
                ( SELECT tp_rev.child_val FROM $table_revision tp_rev WHERE ID = tp_str.long_info ) AS `long_info`,
                ( SELECT tp_rev.child_val FROM $table_revision tp_rev WHERE ID = tp_str.logo ) AS `logo`,
                ( SELECT tp_rev.child_val FROM $table_revision tp_rev WHERE ID = tp_str.banner ) AS `banner`,
                ( SELECT dv_rev.child_val FROM $table_dv_revision dv_rev WHERE ID = dv_add.street ) AS `street`,
                ( SELECT brgy_name FROM $table_brgy WHERE ID = ( SELECT child_val FROM $table_dv_revision WHERE ID = dv_add.brgy ) ) AS brgy,
                ( SELECT city_name FROM $table_city WHERE city_code = ( SELECT child_val FROM $table_dv_revision WHERE ID = dv_add.city ) ) AS city,
                ( SELECT prov_name FROM $table_province WHERE prov_code = ( SELECT child_val FROM $table_dv_revision WHERE ID = dv_add.province ) ) AS province,
                ( SELECT country_name FROM $table_country WHERE ID = ( SELECT child_val FROM $table_dv_revision WHERE ID = dv_add.country ) ) AS country ,
                ROUND((SELECT `distance_kilometer`(( SELECT child_val FROM $table_dv_revision WHERE ID = dv_add.latitude ), ( SELECT child_val FROM $table_dv_revision WHERE ID = dv_add.longitude ) ,'$lat2', '$long2' ) ), 3)AS `distance`
            FROM
                $table_store tp_str
                INNER JOIN $table_revision tp_rev ON tp_rev.ID = tp_str.`status` 
                INNER JOIN $table_address dv_add ON tp_str.address = dv_add.ID	
                WHERE tp_rev.child_val = 1 LIMIT 20
            ");

            // Step 5: Return results
            return array(
                "status" => "success",
                "data" => $results
            );
        }
    }