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
    class TP_Store_Listing {

        public static function listen(){
            return rest_ensure_response( 
                TP_Store_Listing:: list_open()
            );
        }

        public static function list_open(){

            global $wpdb;

            // declaring table names to variable
            $table_store = TP_STORES_TABLE;
            $table_revs = TP_REVISIONS_TABLE;
            $table_address = DV_ADDRESS_TABLE;
            $table_dv_revs = DV_REVS_TABLE;
            $table_brgy = DV_BRGY_TABLE;
            $table_city = DV_CITY_TABLE;
            $table_province = DV_PROVINCE_TABLE;
            $table_country = DV_COUNTRY_TABLE;
            $table_category = TP_CATEGORIES_TABLE;
            $table_contacts = DV_CONTACTS_TABLE;
            $table_dv_revisions = DV_REVS_TABLE;
            
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
     
            // Step3 : Query
            $result = $wpdb->get_results("SELECT
                tp_str.ID,
                tp_str.ctid AS `catid`,
                (select child_val from $table_revs where id = (select title from tp_categories where id = tp_str.ctid)) AS catname,
                ( select child_val from $table_revs where id = tp_str.title) AS title,
                ( select child_val from $table_revs where id = tp_str.short_info) AS short_info,
                ( select child_val from $table_revs where id = tp_str.long_info) AS long_info,
                ( select child_val from $table_revs where id = tp_str.logo) AS avatar,
                ( select child_val from $table_revs where id = tp_str.banner) AS banner,
                IF (( select child_val from $table_revs where id = tp_str.`status` ) = 1, 'Active' , 'Inactive' ) AS `status`,
                ( select child_val from $table_dv_revs where id = dv_add.street) as street,
                ( SELECT brgy_name FROM $table_brgy WHERE ID = (select child_val from $table_dv_revs where id = dv_add.brgy)) as brgy,
                ( SELECT city_name FROM $table_city WHERE city_code = (select child_val from $table_dv_revs where id = dv_add.city)) as city,
                ( SELECT prov_name FROM $table_province WHERE prov_code = (select child_val from $table_dv_revs where id = dv_add.province)) as province,
                ( SELECT country_name FROM $table_country WHERE id = (select child_val from $table_dv_revs where id = dv_add.country)) as country,
                ( SELECT child_val FROM $table_dv_revisions WHERE ID  = ( SELECT revs FROM $table_contacts WHERE  types = 'phone' and stid =tp_str.ID  ) ) AS phone,
                ( SELECT child_val FROM $table_dv_revisions WHERE ID  = ( SELECT revs FROM $table_contacts WHERE  types = 'email' and stid =tp_str.ID  ) ) AS email
            FROM
                $table_store tp_str
            INNER JOIN 
                $table_address dv_add ON tp_str.address = dv_add.ID	
            ");

            // Step4 : Check if no result
            if (!$result ) {
                return array(
                        "status" => "failed",
                        "message" => "No results found.",
                );

            }
            
            // Step5 : Return Result 
            return array(
                    "status" => "success",
                    "data" => $result,
            );
            
        }

    }