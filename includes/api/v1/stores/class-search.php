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
                TP_SearchStore:: list_open()
            );
        }

        public static function list_open(){
            global $wpdb;

           //Check if prerequisites plugin are missing
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

            // Step3 : Sanitize all Request
			if (!isset($_POST['search']) ) {
				return array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Request unknown!",
                );
                
            }

             // Step4 : Sanitize all Request if emply
			if (empty($_POST['search']) ) {
				return array(
						"status" => "unknown",
						"message" => "Required fields cannot be empyty!",
                );
                
            }
            
            $value = $_POST['search'];

            // table names and POST Variables
            $store_table           = TP_STORES_TABLE;
            $table_revs            = TP_REVISIONS_TABLE;
            $table_address = 'dv_address';
            // datavice table variables declarations
            $dv_geo_brgy    = DV_BRGY_TABLE;
            $dv_revs        =  DV_REVS_TABLE;
            $dv_address     = DV_ADDRESS_TABLE;
            $dv_geo_city    = DV_CITY_TABLE;
            $dv_geo_prov    = DV_PROVINCE_TABLE;
            $dv_geo_court   = DV_COUNTRY_TABLE;     
            // Step5 : Query
            $result = $wpdb->get_results("SELECT
                tp_str.ID,
                tp_rev.child_val AS title,
                ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_str.short_info ) AS `short_info`,
                ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_str.long_info ) AS `long_info`,
                ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_str.logo ) AS `logo`,
                ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_str.banner ) AS `banner`,
                ( SELECT dv_rev.child_val FROM $dv_revs as dv_rev WHERE ID = dv_add.street ) AS `street`,
                ( SELECT brgy_name FROM $dv_geo_brgy WHERE ID = ( SELECT child_val FROM $dv_revs WHERE ID = dv_add.brgy ) ) AS brgy,
                ( SELECT citymun_name FROM $dv_geo_city WHERE city_code = ( SELECT child_val FROM $dv_revs WHERE ID = dv_add.city ) ) AS city,
                ( SELECT prov_name FROM $dv_geo_prov WHERE prov_code = ( SELECT child_val FROM $dv_revs WHERE ID = dv_add.province ) ) AS province,
                ( SELECT country_name FROM $dv_geo_court WHERE ID = ( SELECT child_val FROM $dv_revs WHERE ID = dv_add.country ) ) AS country 
            FROM
                $store_table tp_str
                INNER JOIN $table_revs tp_rev ON tp_rev.ID = tp_str.title 
                INNER JOIN $dv_address dv_add ON tp_str.address = dv_add.ID	
            WHERE
                tp_rev.child_val REGEXP '^$value';
                ");

            // Step6 : Check if no result
            if (!$result ) {
                return array(
                        "status" => "failed",
                        "message" => "No results found!",
                );

            }
            
            // Step9 : Return Result 
            return array(
                    "status" => "success",
                    "data" => $result
            );
              
        }
    }