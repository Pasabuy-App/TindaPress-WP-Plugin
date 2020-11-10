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

    //Qa done 2020-08-12 9:55 pm
    class TP_Store_Update_address {

        //REST API Call
        public static function listen(){
            return rest_ensure_response(
                self:: listen_open()
            );
        }

        public static function listen_open (){

            // 2nd Initial QA 2020-08-24 7:29 PM - Miguel
            global $wpdb;

            // declaring table names to variable
            $table_store = TP_STORES_TABLE;
            $table_store_fields = TP_STORES_FIELDS;
            $table_tp_revs = TP_REVISIONS_TABLE;
            $table_revs_fields = TP_REVISION_FIELDS;
            $revs_type = "stores";
            $table_contact = DV_CONTACTS_TABLE;
            $table_dv_revs = DV_REVS_TABLE;
            $rev_fields = DV_INSERT_REV_FIELDS;
            $dv_rev_table = DV_REVS_TABLE;
            $table_address = DV_ADDRESS_TABLE;

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
                || !isset($_POST["adid"])
                ) {
				return array(
					"status" => "unknown",
					"message" => "Please contact your administrator. Request unknown!",
                );
            }

            // Step 4: Check if parameters passed are empty
            $validate = TP_Globals_v2::check_listener($user);
            if ($validate !== true) {
                return array(
                    "status" => "failed",
                    "message" => "Required fileds cannot be empty "."'".ucfirst($validate)."'"."."
                );
            }

            // Step 5: Check if address fields if exists in database.

            //Country
                $country_code = $_POST['co'];
                $co_status = DV_Globals:: check_availability(DV_COUNTRY_TABLE, "WHERE `country_code` = '$country_code'");

                if ( $co_status == false ) {
                    return array(
                        "status" => "failed",
                        "message" => "Invalid value for country.",
                    );
                }

                if ( $co_status === "unavail" ) {
                    return array(
                        "status" => "failed",
                        "message" => "Not available yet in selected country",
                    );
                }

            //Province
                $pv_status = DV_Globals:: check_availability(DV_PROVINCE_TABLE, 'WHERE `prov_code` = '.$_POST['pv']);

                if ( $pv_status == false ) {
                    return array(
                        "status" => "failed",
                        "message" => "Invalid value for province.",
                    );
                }


                if ( $pv_status === "unavail" ) {
                    return array(
                        "status" => "failed",
                        "message" => "Not available yet in selected province",
                    );
                }

            //City
                $ct_status = DV_Globals:: check_availability(DV_CITY_TABLE, 'WHERE `city_code` = '.$_POST['ct']);

                if ( $ct_status == false ) {
                    return array(
                        "status" => "failed",
                        "message" => "Invalid value for city.",
                    );
                }

                if ( $ct_status === "unavail" ) {
                    return array(
                        "status" => "failed",
                        "message" => "Not available yet in selected city",
                    );
                }

            //Barangay
                $bg_status = DV_Globals:: check_availability(DV_BRGY_TABLE, 'WHERE `id` = '.$_POST['bg']);

                if ( $bg_status == false ) {
                    return array(
                        "status" => "failed",
                        "message" => "Invalid value for barangay.",
                    );
                }

                if ( $bg_status === "unavail" ) {
                    return array(
                        "status" => "failed",
                        "message" => "Not available yet in selected barangay",
                    );
                }


            $user = self::catch_post();
            $date_created = TP_Globals_v2::date_stamp();

            // Step 6 : Check if this store id exists
            $check_store = $wpdb->get_row("SELECT ID FROM $table_store WHERE hsid = '{$user["store_id"]}' AND `status` = 'active' ");
            if (empty($check_store)) {
                return array(
                    "status" => "failed",
                    "message" => "This store does not exists"
                );
            }

            $check_address = $wpdb->get_row("SELECT ID FROM $table_address  WHERE ID = '{$user["address_id"]}' AND (SELECT child_val FROM $table_dv_revs WHERE ID = $table_address.`status`  ) = 1 AND stid = '{$user["store_id"]}' ");

            //Check if no rows found
            if ( !$check_address ) {
                return array(
                    "status" => "failed",
                    "message" => "This address does not exits.",
                );
            }

            // Step 7: Start mysql transaction
            $wpdb->query("START TRANSACTION");

                $get_address = $wpdb->get_row("SELECT * FROM dv_address WHERE ID  = '{$user["address_id"]}' ");

                $wpdb->query("UPDATE dv_revisions SET `child_val` = 0 WHERE ID = $get_address->status  ");

                //get country id
                $get_country = $wpdb->get_row("SELECT ID FROM dv_geo_countries WHERE `country_code` = '{$user["country"]}'");

                // Query of store address.
                $wpdb->query("INSERT INTO $dv_rev_table (parent_id, $rev_fields) VALUES ('$get_address->ID','address', 'status', '1', '{$user["created_by"]}', '$date_created');");
                $status = $wpdb->insert_id;

                $wpdb->query("INSERT INTO $dv_rev_table (parent_id, $rev_fields) VALUES ('$get_address->ID','address', 'street', '{$user["street"]}', '{$user["created_by"]}', '$date_created');");
                $street = $wpdb->insert_id;

                $wpdb->query("INSERT INTO $dv_rev_table (parent_id, $rev_fields) VALUES ('$get_address->ID','address', 'brgy', '{$user["barangy"]}', '{$user["created_by"]}', '$date_created');");
                $brgy = $wpdb->insert_id;

                $wpdb->query("INSERT INTO $dv_rev_table (parent_id, $rev_fields) VALUES ('$get_address->ID','address', 'city', '{$user["city"]}', '{$user["created_by"]}', '$date_created');");
                $city = $wpdb->insert_id;

                $wpdb->query("INSERT INTO $dv_rev_table (parent_id, $rev_fields) VALUES ('$get_address->ID','address', 'province', '{$user["province"]}', '{$user["created_by"]}', '$date_created');");
                $province = $wpdb->insert_id;

                $wpdb->query("INSERT INTO $dv_rev_table (parent_id, $rev_fields) VALUES ('$get_address->ID','address', 'country', '$get_country->ID', '{$user["created_by"]}', '$date_created');");
                $country = $wpdb->insert_id;

                //Save the address in the parent table
                $update_address =  $wpdb->query("UPDATE $table_address SET `status` = $status, `street` = $street, `brgy` = $brgy, `city` = $city, `province` = $province, `country` = $country  WHERE ID =  $get_address->ID  ");

            // Step 8: Check if any queries above failed
            if ($status < 1 || $street < 1 || $brgy < 1 || $city < 1 || $province < 1 || $country < 1 || $update_address < 1 ) {
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

            return  $cur_user;
        }
    }