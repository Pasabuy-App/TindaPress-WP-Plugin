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
                self:: listen_open()
            );
        }

        public static function catch_post(){
            $curl_user = array();
            isset($_POST['type']) && !empty($_POST['type'])? $curl_user['type'] =  $_POST['type'] :  $curl_user['type'] = null ;
            isset($_POST['adid']) && !empty($_POST['adid'])? $curl_user['adid'] =  $_POST['adid'] :  $curl_user['adid'] = null ;
            isset($_POST['stid']) && !empty($_POST['stid'])? $curl_user['stid'] =  $_POST['stid'] :  $curl_user['stid'] = null ;
            isset($_POST['status']) && !empty($_POST['status'])? $curl_user['status'] =  $_POST['status'] :  $curl_user['status'] = null ;
            return $curl_user;
        }

        public static function listen_open (){

            // 2nd Initial QA 2020-08-24 7:29 PM - Miguel
            global $wpdb;

            // NOTE : POST 'type' is not required even if its not listen in client it will not show error
            // declaring table names to variable
            $table_contacts = DV_CONTACTS_TABLE;
            $table_brgy = DV_BRGY_TABLE;
            $table_city = DV_CITY_TABLE;
            $table_province = DV_PROVINCE_TABLE;
            $table_country = DV_COUNTRY_TABLE;
            $table_dv_revisions = DV_REVS_TABLE;
            $table_add = DV_ADDRESS_TABLE;

            // Step 1: Check if prerequisites plugin are missing
            $plugin = TP_Globals_v2::verify_prerequisites();
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

            // Step 6: Start mysql query
            $sql = "SELECT
                `add`.ID,
                `add`.stid,
                IF(`add`.types = 'business', 'Business', 'Office' )as `type`,
                ( SELECT child_val FROM $table_dv_revisions WHERE id = `add`.street ) AS street,
                ( SELECT brgy_name FROM $table_brgy WHERE ID = ( SELECT child_val FROM $table_dv_revisions WHERE id = `add`.brgy ) ) AS brgy,
                ( SELECT city_name FROM $table_city WHERE city_code = ( SELECT child_val FROM $table_dv_revisions WHERE id = `add`.city ) ) AS city,
                ( SELECT prov_name FROM $table_province WHERE prov_code = ( SELECT child_val FROM $table_dv_revisions WHERE id = `add`.province ) ) AS province,
                ( SELECT country_name FROM $table_country WHERE id = ( SELECT child_val FROM $table_dv_revisions WHERE id = `add`.country ) ) AS country,
                IF (( select child_val from $table_dv_revisions where id = `add`.`status` ) = 1, 'Active' , 'Inactive' ) AS `status`,
                `add`.date_created
            FROM
                $table_add `add`
            ";

            $user = self::catch_post();

            if ($user["adid"] != null) {
                if ( !is_numeric($address_id) ) {
                    return array(
                        "status" => "failed",
                        "message" => "ID is not in valid format."
                    );
                }

                $sql .=" WHERE `add`.ID = '{$user["adid"]}'";
            }

            if ($user['type'] != null) {
                if ($type != 'business' && $type != 'office' ) {
                    return array(
                        "status" => "failed",
                        "message" => "Invalid type of address."
                    );

                }else{

                    if ($user["adid"] != null) {
                        $sql .= " AND `add`.types = '{$user["type"]}' ";

                    }else{
                        $sql .= " WHERE `add`.types = '{$user["type"]}' ";

                    }

                }
            }

            if ($user['stid'] != null) {
                if ($user['type'] != null || $user["adid"] != null  ) {

                    $sql .= " AND `add`.stid = '{$user["stid"]}' ";

                }else{

                    $sql .= " WHERE `add`.stid = '{$user["stid"]}' ";
                }
            }

            if ($user['status'] != null) {
                if ($user['type'] != null || $user["adid"] != null || $user['stid'] != null  ) {

                    $sql .= " AND ( select child_val from $table_dv_revisions where id = `add`.`status` ) = '{$user["status"]}' ";

                }else{
                    $sql .= " WHERE ( select child_val from $table_dv_revisions where id = `add`.`status` ) = '{$user["status"]}' ";
                }
            }

            // return $sql;
            $result = $wpdb->get_results($sql);
            return array(
                "status" => "success",
                "data" => $result
            );
        }
    }