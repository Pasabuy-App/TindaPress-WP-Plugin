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
    class TP_SearchStore {

        public static function listen(){
            return rest_ensure_response(
                self:: list_open()
            );
        }

        public static function list_open(){

            global $wpdb;
            //return "HAHAHA";
            // declaring table names to variable
            $table_store = TP_STORES_TABLE;
            $table_revs = TP_REVISIONS_TABLE;
            $table_address = DV_ADDRESS_TABLE;
            $table_dv_revs =  DV_REVS_TABLE;
            $table_brgy = DV_BRGY_TABLE;
            $table_city = DV_CITY_TABLE;
            $table_province = DV_PROVINCE_TABLE;
            $table_country = DV_COUNTRY_TABLE;

            // declaring variable
            $value = $_POST['search'];

            // Step1 : Check if prerequisites plugin are missing
            $plugin = TP_Globals::verify_prerequisites();
            if ($plugin !== true) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. ".$plugin." plugin missing!",
                );
                
            }

            // Step2 : Check if wpid and snky is valid
            if (DV_Verification::is_verified() == false) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Verification Issues!",
                );
            }

            // Step3 : Sanitize request
			if (!isset($_POST['search']) ) {
				return array(
					"status" => "unknown",
					"message" => "Please contact your administrator. Request unknown!",
                );
            }

             // Step4 : Sanitize variable if empty
			if (empty($_POST['search']) ) {
				return array(
					"status" => "failed",
					"message" => "Required fields cannot be empty.",
                );
            }

            // Step5 : Query
            $result = $wpdb->get_results("SELECT
                tp_str.ID,
                tp_rev.child_val AS title,
                ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_str.short_info ) AS `short_info`,
                ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_str.long_info ) AS `long_info`,
                ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_str.logo ) AS `avatar`,
                ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_str.banner ) AS `banner`,
                ( SELECT dv_rev.child_val FROM $table_dv_revs as dv_rev WHERE ID = dv_add.street ) AS `street`,
                ( SELECT brgy_name FROM $table_brgy WHERE ID = ( SELECT child_val FROM $table_dv_revs WHERE ID = dv_add.brgy ) ) AS brgy,
                ( SELECT city_name FROM $table_city WHERE city_code = ( SELECT child_val FROM $table_dv_revs WHERE ID = dv_add.city ) ) AS city,
                ( SELECT prov_name FROM $table_province WHERE prov_code = ( SELECT child_val FROM $table_dv_revs WHERE ID = dv_add.province ) ) AS province,
                ( SELECT country_name FROM $table_country WHERE ID = ( SELECT child_val FROM $table_dv_revs WHERE ID = dv_add.country ) ) AS country
            FROM
                $table_store tp_str
            INNER JOIN
                $table_revs tp_rev ON tp_rev.ID = tp_str.title
            INNER JOIN
                $table_address dv_add ON tp_str.address = dv_add.ID
            WHERE
                tp_rev.child_val REGEXP '^$value' OR ( SELECT city_name FROM dv_geo_cities WHERE city_code = ( SELECT child_val FROM tp_revisions WHERE ID = dv_add.city ) ) REGEXP '^$value';
            ");

            // Step7 : Return Result
            return array(
                "status" => "success",
                "data" => $result
            );
        }
    }