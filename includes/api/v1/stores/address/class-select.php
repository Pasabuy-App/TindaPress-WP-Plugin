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

    class TP_Store_Select_Address {
        public static function listen(){
            return rest_ensure_response( 
                TP_Store_Select_Address:: listen_open()
            );
        }

        public static function listen_open (){
            global $wpdb;

            

            // declaring table names to variable
            $table_store = TP_STORES_TABLE;
            $table_revisions = TP_REVISIONS_TABLE;
            $table_contacts = DV_CONTACTS_TABLE;
            $table_brgy = DV_BRGY_TABLE;
            $table_city = DV_CITY_TABLE;
            $table_province = DV_PROVINCE_TABLE;
            $table_country = DV_COUNTRY_TABLE;
            $table_dv_revisions = DV_REVS_TABLE;
            $table_add = DV_ADDRESS_TABLE;

            $user = TP_Store_Select_Address::catch_post();

            
            $check_addres_id = $wpdb->get_row("SELECT `child_val`as`status` FROM dv_revisions WHERE ID = (SELECT `status` FROM dv_address WHERE ID = '{$user["address_id"]}')");
            if ($check_addres_id->status != 1) {
                return array(
                    "status" => "failed",
                    "message" => "This address is deactivated.."
                );
            }

            $result = $wpdb->get_row("SELECT
                `add`.ID,
                `add`.types,
                ( SELECT `child_val` FROM tp_revisions WHERE ID = ( SELECT `title` FROM tp_stores WHERE ID = `add`.stid ) ) as store_name,
                ( SELECT child_val FROM dv_revisions WHERE id = `add`.street ) AS street,
                ( SELECT brgy_name FROM dv_geo_brgys WHERE ID = ( SELECT child_val FROM dv_revisions WHERE id = `add`.brgy ) ) AS brgy,
                ( SELECT city_name FROM dv_geo_cities WHERE city_code = ( SELECT child_val FROM dv_revisions WHERE id = `add`.city ) ) AS city,
                ( SELECT prov_name FROM dv_geo_provinces WHERE prov_code = ( SELECT child_val FROM dv_revisions WHERE id = `add`.province ) ) AS province,
                ( SELECT country_name FROM dv_geo_countries WHERE id = ( SELECT child_val FROM dv_revisions WHERE id = `add`.country ) ) AS country,
                IF (( select child_val from dv_revisions where id = `add`.`status` ) = 1, 'Active' , 'Inactive' ) AS `status`,
                `add`.date_created
            FROM
                dv_address `add`
            WHERE
            `add`.id = $address_id AND `add`.stid = $store_id ");

            if (!$result) {
                return array(
                    "status" => "unknown",
                    "message" => "An erro occured while fetching data to database!"
                );

            }else{

                return array(
                    "status" => "success",
                    "data" => $result
                );
            }
            
        }

        public static function catch_post(){
            
            $cur_user = array();

            $cur_user["store_id"] = $_POST["stid"];
            $cur_user["address_id"] = $_POST["addr"];

            return  $cur_user;
        }
    }