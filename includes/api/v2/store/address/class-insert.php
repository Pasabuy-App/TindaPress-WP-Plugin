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

    class TP_Store_Insert_address {

        //REST API Call
        public static function listen(){
            return rest_ensure_response(
                self:: listen_open()
            );
        }

        public static function listen_open(){

            $data = array( "st" => "streeeettt", "bg" => 36800, "ct" => 137603, "pv" => 1376, "co" => "PH", "type" => "office" );
            return DV_Address_Config::add_address( $data,  11);

            #$data = DV_Address_Config::get_geo_location(  'dv_geo_countries', 'country_code', 'PH');

            #$data = DV_Address_Config::update_address(  $data = array(), $user_id = 0, $store_id = 0, 2 );
            return DV_Address_Config::get_address();
            // 2nd Initial QA 2020-08-24 7:27 PM - Miguel
            global $wpdb;

            // Global constant to variable

            //TindaPress
            $table_store = TP_STORES_v2;

            //DataVice
            $table_contact = DV_CONTACTS_TABLE;
            $table_dv_revs = DV_REVS_TABLE;
            $rev_fields = DV_INSERT_REV_FIELDS;
            $dv_rev_table = DV_REVS_TABLE;
            $table_address = DV_ADDRESS_TABLE;
            $dv_geo_countries = DV_COUNTRY_TABLE;
            $revs_type = "stores";

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

            // Step 3: Check if required parameters are passed
            if ( !isset($_POST["stid"]) || !isset($_POST["st"])
                || !isset($_POST["co"])
                || !isset($_POST["pv"])
                || !isset($_POST["ct"])
                || !isset($_POST["bg"])
                || !isset($_POST["type"])
                ) {
				return array(
					"status" => "unknown",
					"message" => "Please contact your administrator. Request unknown!",
                );
            }

            $user = self::catch_post();

            // Step 4: Check if parameters passed are empty
            $validate = TP_Globals_v2::check_listener($user);
            if ($validate !== true) {
                return array(
                    "status" => "failed",
                    "message" => "Required fileds cannot be empty "."'".ucfirst($validate)."'"."."
                );
            }

            // Step 5: Check if address fields if exists in database.

            // Country
                $country_code = $_POST['co'];
                $co_status = DV_Globals:: check_availability(DV_COUNTRY_TABLE, "WHERE `country_code` = '$country_code'");

                if ( $co_status == false ) {
                    return rest_ensure_response(
                        array(
                                "status" => "failed",
                                "message" => "Invalid value for country.",
                        )
                    );
                }

                if ( $co_status === "unavail" ) {
                    return rest_ensure_response(
                        array(
                                "status" => "failed",
                                "message" => "Not available yet in selected country",
                        )
                    );
                }

            // Provice
                $pv_status = DV_Globals:: check_availability(DV_PROVINCE_TABLE, 'WHERE `prov_code` = '.$_POST['pv']);

                if ( $pv_status == false ) {
                    return rest_ensure_response(
                        array(
                                "status" => "failed",
                                "message" => "Invalid value for province.",
                        )
                    );
                }

                if ( $pv_status === "unavail" ) {
                    return rest_ensure_response(
                        array(
                                "status" => "failed",
                                "message" => "Not available yet in selected province",
                        )
                    );
                }

                // City
                $ct_status = DV_Globals:: check_availability(DV_CITY_TABLE, 'WHERE `city_code` = '.$_POST['ct']);

                if ( $ct_status == false ) {
                    return rest_ensure_response(
                        array(
                                "status" => "failed",
                                "message" => "Invalid value for city.",
                        )
                    );
                }

                if ( $ct_status === "unavail" ) {
                    return rest_ensure_response(
                        array(
                                "status" => "failed",
                                "message" => "Not available yet in selected city",
                        )
                    );
                }

            // Barangay
                $bg_status = DV_Globals:: check_availability(DV_BRGY_TABLE, 'WHERE `id` = '.$_POST['bg']);

                if ( $bg_status == false ) {
                    return rest_ensure_response(
                        array(
                                "status" => "failed",
                                "message" => "Invalid value for barangay.",
                        )
                    );
                }

                if ( $bg_status === "unavail" ) {
                    return rest_ensure_response(
                        array(
                                "status" => "failed",
                                "message" => "Not available yet in selected barangay",
                        )
                    );
                }

            // Declare catch_post function
            $date_created = TP_Globals_v2::date_stamp();

            $check_store = $wpdb->get_row("SELECT ID FROM $table_store WHERE hsid = '{$user["store_id"]}' AND `status` = 'active' AND  id IN ( SELECT MAX( id ) FROM $table_store s WHERE s.hsid = hsid  GROUP BY hsid ) ");
            if (empty($check_store)) {
                return array(
                    "status" => "failed",
                    "message" => "This store does not exists"
                );
            }

            // Step 7 : Check if address type is valid
            if ($user["type"] !== 'business' && $user["type"] !== 'office' ) {
                return array(
                    "status" => "failed",
                    "message" => "Invalid type of address.",
                );
            }

            // Step 8: Start mysql transaction
            $wpdb->query("START TRANSACTION");

            //get country id
            $get_country = $wpdb->get_row("SELECT ID FROM $dv_geo_countries WHERE `country_code` = '{$user["country"]}'");


            //Save the address in the parent table
            $wpdb->query("INSERT INTO $table_address (`status`, `types`, `stid`, `street`, `brgy`, `city`, `province`, `country`, `date_created`, `latitude`, `longitude`)
                 VALUES ('$status', '{$user["type"]}', '{$user["store_id"]}', $street, $brgy, $city, $province, $country, '$date_created', '$latitude', '$longtitude')");
            $address_id = $wpdb->insert_id;

            //Update dv_revisions table
            $update_table_rev = $wpdb->query("UPDATE $dv_rev_table SET `parent_id` = $address_id WHERE ID IN ($status, $street, $brgy, $city, $province, $country)  ");

            //Update tp_stores table
            $update_store = $wpdb->query("UPDATE $table_store SET `adid` = $address_id WHERE hsid = '{$user["store_id"]}' ");

            // Step 9: Check if any queries above failed
            if ($status < 1 || $street < 1 || $brgy < 1 || $city < 1 || $province < 1 || $country < 1 || $update_table_rev < 1 || $update_store < 1 || $longtitude < 1 || $latitude < 1) {
                //Do a rollback if any of the above queries failed
                $wpdb->query("ROLLBACK");
                return array(
                    "status" => "failed",
                    "message" => "An error occured while submitting data to database.",
                );

            }else{
                //Commit if no errors found
                $wpdb->query("COMMIT");
                return array(
                    "status" => "success",
                    "message" => "Data has been added successfully.",
                );
            }
        }

        public static function catch_post()
        {
            $cur_user = array();

            $cur_user['created_by']  = $_POST["wpid"];
            $cur_user['store_id']  = $_POST["stid"];

            // Address Listen
            $cur_user['street']     = $_POST["st"];
            $cur_user['country']    = $_POST["co"];
            $cur_user['province']   = $_POST["pv"];
            $cur_user['city']       = $_POST["ct"];
            $cur_user['barangy']    = $_POST["bg"];
            $cur_user['type']       = $_POST["type"];
            $cur_user['latitude']       = $_POST["lat"];
            $cur_user['longtitude']       = $_POST["long"];

            return  $cur_user;
        }
    }