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
    class TP_Popular_Store {

        public static function listen(){
            return rest_ensure_response( 
                TP_Popular_Store:: list_open()
            );
        }
        
        public static function list_open(){

            // 2nd Initial QA 2020-08-24 10:57 PM - Miguel
            global $wpdb;
            
            // declaring table names to variable
            $table_stores = TP_STORES_TABLE;
            $table_revs = TP_REVISIONS_TABLE;
            $table_address = DV_ADDRESS_TABLE;
            $table_dv_revs = DV_REVS_TABLE;
            $table_brgy = DV_BRGY_TABLE;
            $table_city = DV_CITY_TABLE;
            $table_province = DV_PROVINCE_TABLE;
            $table_country = DV_COUNTRY_TABLE;
            $table_orders = 'mp_orders';
               
            //Step1 : Check if prerequisites plugin are missing
            $plugin = TP_Globals::verify_prerequisites();
            if ($plugin !== true) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. ".$plugin." plugin missing!",
                );
            }

			//step 2: Validate User
            if (DV_Verification::is_verified() == false) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Verification Issues!",
                );
            }

            // Step5 : Query
            $result = $wpdb->get_results("SELECT
                Count( mp_ord.stid ) AS cnt,
                mp_ord.stid,
                ( SELECT rev.child_val FROM $table_revs rev WHERE ID = tp_st.title ) AS `title`,
                ( SELECT rev.child_val FROM $table_revs rev WHERE ID = tp_st.short_info ) AS `short_info`,
                ( SELECT rev.child_val FROM $table_revs rev WHERE ID = tp_st.long_info ) AS `long_info`,
                ( SELECT rev.child_val FROM $table_revs rev WHERE ID = tp_st.banner ) AS `banner`,
                ( SELECT dv_rev.child_val FROM $table_dv_revs dv_rev WHERE ID = dv_add.street ) AS `street`,
                ( SELECT brgy_name FROM $table_brgy WHERE ID = ( SELECT child_val FROM $table_dv_revs WHERE ID = dv_add.brgy ) ) AS brgy,
                ( SELECT city_name FROM $table_city WHERE ID = ( SELECT child_val FROM $table_dv_revs WHERE ID = dv_add.city ) ) AS city,
                ( SELECT prov_name FROM $table_province WHERE ID = ( SELECT child_val FROM $table_dv_revs WHERE ID = dv_add.province ) ) AS province,
                ( SELECT country_name FROM $table_country WHERE ID = ( SELECT child_val FROM $table_dv_revs WHERE ID = dv_add.country ) ) AS country 
            FROM
                $table_orders mp_ord
            INNER JOIN 
                $table_stores tp_st ON mp_ord.stid = tp_st.ID
            INNER JOIN 
                $table_revs tp_rev ON tp_st.title = tp_rev.ID
            INNER JOIN 
                $table_address dv_add ON tp_st.address = dv_add.ID 
            GROUP BY
                mp_ord.stid", OBJECT);

            // Step6 : Chech the result if empty
            return array(
                "status" => "success",
                "data" => MAX($result)
            );
        }
    }