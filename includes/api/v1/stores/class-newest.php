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
            $date_stamp = TP_Globals::date_stamp();

            // variable for newest date
            $date = date_create($date_stamp);
             date_sub( $date, date_interval_create_from_date_string("7 days"));
        
            $date_expected =  date_format($date,"Y-m-d H:i:s");
            
            // declaring table names to variable
            $table_store = TP_STORES_TABLE;
            $table_category = TP_CATEGORIES_TABLE;
            $table_revisions = TP_REVISIONS_TABLE;
            
            $table_address = DV_ADDRESS_TABLE;
            $table_dv_revisions =  DV_REVS_TABLE;
            $table_brgy = DV_BRGY_TABLE;
            $table_city = DV_CITY_TABLE;
            $table_province = DV_PROVINCE_TABLE;
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
                tp_str.ID,
                tp_str.ctid AS `catid`,
                ( SELECT child_val FROM $table_revisions WHERE id = ( SELECT title FROM $table_category WHERE id = tp_str.ctid ) ) AS catname,
                ( SELECT child_val FROM $table_revisions  WHERE id = tp_str.title ) AS title,
                ( SELECT child_val FROM $table_revisions  WHERE id = tp_str.short_info ) AS short_info,
                ( SELECT child_val FROM $table_revisions  WHERE id = tp_str.long_info ) AS long_info,
                ( SELECT child_val FROM $table_revisions  WHERE id = tp_str.logo ) AS avatar,
                ( SELECT child_val FROM $table_revisions  WHERE id = tp_str.banner ) AS banner,
                IF  ( ( SELECT child_val FROM $table_revisions  WHERE id = tp_str.`status` ) = 1, 'Active', 'Inactive' ) AS `status`,
                ( SELECT child_val FROM $table_dv_revisions WHERE id = dv_add.street ) AS street,
                ( SELECT brgy_name FROM $table_brgy WHERE ID = ( SELECT child_val FROM $table_dv_revisions WHERE id = dv_add.brgy ) ) AS brgy,
                ( SELECT city_name FROM $table_city WHERE city_code = ( SELECT child_val FROM $table_dv_revisions WHERE id = dv_add.city ) ) AS city,
                ( SELECT prov_name FROM $table_province WHERE prov_code = ( SELECT child_val FROM $table_dv_revisions WHERE id = dv_add.province ) ) AS province,
                ( SELECT country_name FROM $table_country WHERE id = ( SELECT child_val FROM $table_dv_revisions WHERE id = dv_add.country ) ) AS country,
                ( SELECT child_val FROM $table_dv_revisions WHERE ID = ( SELECT revs FROM dv_contacts WHERE types = 'phone' AND stid = tp_str.ID ) ) AS phone,
                ( SELECT child_val FROM $table_dv_revisions WHERE ID = ( SELECT revs FROM dv_contacts WHERE types = 'email' AND stid = tp_str.ID ) ) AS email ,
                tp_str.date_created
            FROM
                $table_store tp_str
                INNER JOIN $table_address dv_add ON tp_str.address = dv_add.ID 
            WHERE
                MONTH(tp_str.date_created)  BETWEEN MONTH('$date_expected') 
                AND MONTH('$date_stamp')
            GROUP BY
                tp_str.id 
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
