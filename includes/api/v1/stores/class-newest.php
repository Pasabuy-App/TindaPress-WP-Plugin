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
    class TP_Newest {

        public static function listen(){
            return rest_ensure_response( 
                TP_Newest:: list_open()
            );
        }
        
        public static function list_open(){
            
            global $wpdb;

            // variable for time stamp
            $later = TP_Globals::date_stamp();

            // variable for newest date
            $date = date_create($later);
            date_sub( $date, date_interval_create_from_date_string("7 days"));
            $date_expected =  date_format($date,"Y-m-d");
            
            // declaring table names to variable
            $table_store = TP_STORES_TABLE;
            $table_category = TP_CATEGORIES_TABLE;
            $table_revs = TP_REVISIONS_TABLE;
            $table_address = DV_ADDRESS_TABLE;
            $table_dv_revs =  DV_REVS_TABLE;
            $table_brgy = DV_BRGY_TABLE;
            $table_city = DV_CTY_TABLE;
            $table_province = DV_PRV_TABLE;
            $table_country = DV_COUNTRY_TABLE;
            
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

            // Step3 : Sanitize request
			if (!isset($_POST["wpid"]) || !isset($_POST["snky"]) ) {
				return array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Request unknown!",
                );
            }

             // Step4 : Check if variable is not empty
            if (empty($_POST["wpid"]) 
                || empty($_POST["snky"]) ) {
				return array(
						"status" => "failed",
						"message" => "Required fields cannot be empty.",
                ); 
            }

            // step5 : Query
            $result = $wpdb->get_results("SELECT
                st.id,
                cat.types,
                ( SELECT child_val FROM $table_revs WHERE ID = cat.title ) AS cat_title,
                ( SELECT child_val FROM $table_revs WHERE ID = cat.info ) AS cat_inf,
                max( IF ( st_r.child_key = 'title', st_r.child_val, '' ) ) AS title,
                max( IF ( st_r.child_key = 'short_info', st_r.child_val, '' ) ) AS short_info,
                max( IF ( st_r.child_key = 'long_info', st_r.child_val, '' ) ) AS long_info,
                max( IF ( st_r.child_key = 'logo', st_r.child_val, '' ) ) AS avatar,
                max( IF ( st_r.child_key = 'banner', st_r.child_val, '' ) ) AS banner,
                addr.types AS add_types,
                ( SELECT child_val FROM $table_dv_revs WHERE ID = addr.STATUS ) AS STATUS,
                ( SELECT child_val FROM $table_address WHERE ID = addr.street ) AS street,
                ( SELECT brgy_name FROM $table_brgy WHERE ID = ( SELECT child_val FROM $table_dv_revs WHERE ID = addr.brgy ) ) AS brgy,
                ( SELECT citymun_name FROM $table_city WHERE ID = ( SELECT child_val FROM $table_dv_revs WHERE ID = addr.city ) ) AS city,
                ( SELECT prov_name FROM $table_province WHERE ID = ( SELECT child_val FROM $table_dv_revs WHERE ID = addr.province ) ) AS province,
                ( SELECT country_name FROM $table_country WHERE ID = ( SELECT child_val FROM $table_dv_revs WHERE ID = addr.country ) ) AS country 
            FROM
                $table_store st
            INNER JOIN
                $table_revs st_r ON st.title = st_r.ID 
                OR st.short_info = st_r.ID 
                OR st.long_info = st_r.ID 
                OR st.logo = st_r.ID 
                OR st.banner = st_r.ID
            INNER JOIN 
                $table_category cat ON st.ctid = cat.ID
            INNER JOIN 
                $table_address addr ON st.address = addr.ID 
            WHERE  
                SUBSTRING(st.date_created, 1, 10) BETWEEN '$date_expected' AND '$later'
            GROUP BY
                st.id 
            ORDER BY
                RAND( ) 
            LIMIT 10
            ");
    
            // step6 : return result
            return array(
                    "status" => "success",
                    "data" => $result
            );
        }

    }
