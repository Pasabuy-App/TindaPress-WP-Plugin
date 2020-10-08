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

        // 2nd Initial QA 2020-08-24 10:59 PM - Miguel
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
            $date = TP_Globals::date_stamp();

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
            if (!isset($_POST['lat']) || !isset($_POST['long']) || !isset($_POST['type'])) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Request unknown!",
                );
            }

            // Step 2: Sanitize Request
            if (empty($_POST['lat']) || empty($_POST['long']) || empty($_POST['type']) ) {
                return array(
                    "status" => "failed",
                    "message" => "Required fileds cannot be empty.",
                );
            }

            if ($_POST['type'] != "close" && $_POST['type'] != "open" && $_POST['type'] != "all" ) {
                return array(
                    "status" => "failed",
                    "message" => "Invalid value of type.",
                );
            }

            $lat = $_POST['lat'];
            $long = $_POST['long'];

            // Step 4: Start query
            $results = $wpdb->get_results("SELECT
            tp_str.ID,
            ( SELECT rev.child_val FROM tp_revisions rev WHERE rev.id = tp_str.title AND  rev.date_created = ( SELECT MAX(date_created) FROM tp_revisions tp_rev WHERE tp_rev.ID = rev.ID AND revs_type = 'stores' )  ) AS title,
            ( SELECT tp_rev.child_val FROM tp_revisions tp_rev WHERE ID = tp_str.short_info ) AS `short_info`,
            ( SELECT tp_rev.child_val FROM tp_revisions tp_rev WHERE ID = tp_str.long_info ) AS `long_info`,
            ( SELECT tp_rev.child_val FROM tp_revisions tp_rev WHERE ID = tp_str.logo ) AS `logo`,
            ( SELECT tp_rev.child_val FROM tp_revisions tp_rev WHERE ID = tp_str.banner ) AS `banner`,
            ( SELECT dv_rev.child_val FROM dv_revisions dv_rev WHERE ID = dv_add.street ) AS `street`,
            ( SELECT brgy_name FROM dv_geo_brgys WHERE ID = ( SELECT child_val FROM dv_revisions WHERE ID = dv_add.brgy ) ) AS brgy,
            ( SELECT city_name FROM dv_geo_cities WHERE city_code = ( SELECT child_val FROM dv_revisions WHERE ID = dv_add.city ) ) AS city,
            ( SELECT prov_name FROM dv_geo_provinces WHERE prov_code = ( SELECT child_val FROM dv_revisions WHERE ID = dv_add.province ) ) AS province,
            ( SELECT country_name FROM dv_geo_countries WHERE ID = ( SELECT child_val FROM dv_revisions WHERE ID = dv_add.country ) ) AS country,
            (SELECT date_close FROM mp_operations WHERE stid = tp_str.ID) as date_close,
            (SELECT dv_revisions.child_val FROM dv_address INNER JOIN dv_revisions ON  dv_address.latitude = dv_revisions.ID WHERE dv_address.stid =  tp_str.ID) as store_lat,
            (SELECT dv_revisions.child_val FROM dv_address INNER JOIN dv_revisions ON  dv_address.longitude = dv_revisions.ID WHERE dv_address.stid =  tp_str.ID) as store_long
        FROM
            tp_stores tp_str
            INNER JOIN tp_revisions tp_rev ON tp_rev.ID = tp_str.`status`
            INNER JOIN dv_address dv_add ON tp_str.address = dv_add.ID
        WHERE
            -- Validating status to active
            tp_rev.child_val = 1
            LIMIT 20;
            ");

            // Remove store from array if store is close
            foreach ($results as $key => $value) {
                $store_id = $value->ID;
                $get_date_close = $wpdb->get_row("SELECT date_close FROM mp_operations WHERE stid = '$store_id'");

                if (!empty($get_date_close) ) {

                    $origin = new DateTime($get_date_close->date_close);
                    $target = new DateTime($date);

                    $interval = $origin->diff($target);
                    $smp = $interval->format('%R%a days');

                    switch ($_POST['type']) {
                        case 'close':
                            if ($smp >= 0) {
                                //unset($results[$key]);
                                array_splice($results, $key, $key);
                            }
                            break;

                        case 'open':
                            if ($smp <= 0) {
                                //unset($results[$key]);
                                array_splice($results, $key, $key);
                            }
                            break;
                    }

                }
                if ($value->store_lat != null && $value->store_long != null) {
                    $get_distance = TP_Globals::GetDistance($lat, $long, $value->store_lat, $value->store_long, 'Km' );
                    if ($get_distance > 6) {
                        array_splice($results, $key, $key);
                    }
                }
            }

            // Step 5: Return results
            return array(
                "status" => "success",
                "data" => $results
            );
        }

    }