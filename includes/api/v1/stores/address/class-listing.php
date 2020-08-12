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

    class TP_Store_Listing_Address {
        public static function listen(){
            return rest_ensure_response( 
                TP_Store_Listing_Address:: listen_open()
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
                        "message" => "Please contact your administrator. Verification issues!",
                );
            }

            if (!isset($_POST["stid"])) {
                return array(
					"status" => "unknown",
					"message" => "Please contact your administrator. Request unknown!",
                );
            }

            if (empty($_POST["stid"])) {
                return array(
                    "status" => "failed",
                    "message" => "Required fileds cannot be empty.",
                );
            }

            if (!is_numeric($_POST["stid"])) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. ID is not in valid format!",
                );
            }

            $user = TP_Store_Listing_Address::catch_post();
           return $check_store = $wpdb->get_row("SELECT ID FROM tp_stores  WHERE ID = '{$user["store_id"]}'  AND  (SELECT child_val FROM tp_revisions WHERE ID = tp_stores.`status`  ) = 1)");
            if ($check_store->status != 1) {
                return array(
                    "status" => "failed",
                    "message" => "This address is deactivated.."
                );
            }

            $result = $wpdb->get_row("SELECT
                `add`.ID,
                `add`.types,
                ( SELECT `child_val` FROM $table_revisions WHERE ID = ( SELECT `title` FROM tp_stores WHERE ID = `add`.stid ) ) as store_name,
                ( SELECT child_val FROM $table_dv_revisions WHERE id = `add`.street ) AS street,
                ( SELECT brgy_name FROM $table_brgy WHERE ID = ( SELECT child_val FROM $table_dv_revisions WHERE id = `add`.brgy ) ) AS brgy,
                ( SELECT city_name FROM $table_city WHERE city_code = ( SELECT child_val FROM $table_dv_revisions WHERE id = `add`.city ) ) AS city,
                ( SELECT prov_name FROM $table_province WHERE prov_code = ( SELECT child_val FROM $table_dv_revisions WHERE id = `add`.province ) ) AS province,
                ( SELECT country_name FROM $table_country WHERE id = ( SELECT child_val FROM $table_dv_revisions WHERE id = `add`.country ) ) AS country,
                IF (( select child_val from $table_dv_revisions where id = `add`.`status` ) = 1, 'Active' , 'Inactive' ) AS `status`,
                `add`.date_created
            FROM
                $table_add `add`
            WHERE
             `add`.stid = '{$user["store_id"]}' ");

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

            return  $cur_user;
        }
    }