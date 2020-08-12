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
                TP_Store_Insert_address:: listen_open()
            );
        }

        public static function listen_open(){
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

            // Step3 : Sanitize all request
            if ( !isset($_POST["stid"]) || !isset($_POST["st"]) 
                || !isset($_POST["co"]) 
                || !isset($_POST["pv"]) 
                || !isset($_POST["ct"]) 
                || !isset($_POST["bg"]) 
                ) {
				return array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Request unknown!",
                ); 
            }
            
            // Step4 : Sanitize all variable is empty
            if ( empty($_POST["stid"]) 
                || empty($_POST["st"]) 
                || empty($_POST["co"]) 
                || empty($_POST["pv"]) 
                || empty($_POST["ct"]) 
                || empty($_POST["bg"]) 
                ) {
                return array(
                        "status" => "unknown",
                        "message" => "Required fields cannot be empty.",
                );
            }

            //Country input validation
                    // Step 2 : Check if country passed is in integer format.
                    // TODO : char length == 2 and trim and convert to ucase
                    // if ( !is_numeric($_POST['co']) ) {
                    //     return rest_ensure_response( 
                    //         array(
                    //                 "status" => "failed",
                    //                 "message" => "Invalid value for country.",
                    //         )
                    //     );
                    // }
                    
                // Step 2 : Check if country_id is in database. 
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
            //end of country validation

            //Province input validation
                // Step 2 : Check if province passed is in integer format.
                if ( !is_numeric($_POST['pv']) ) {
                    return rest_ensure_response( 
                        array(
                                "status" => "failed",
                                "message" => "Invalid value for province.",
                        )
                    );
                }

                // Step 2 : Check if province is in database. 
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
            // end of province validation

            //City input validation
                // Step 2 : Check if city passed is in integer format.
                if ( !is_numeric($_POST['ct']) ) {
                    return rest_ensure_response( 
                        array(
                                "status" => "failed",
                                "message" => "Invalid value for city.",
                        )
                    );
                }

                // Step 2 : Check if city is in database. 
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
            // end of city validation

            //Barangay input validation
                // Step 2 : Check if barangay passed is in integer format.
                if ( !is_numeric($_POST['bg']) ) {
                    return rest_ensure_response( 
                        array(
                                "status" => "failed",
                                "message" => "Invalid value for barangay.",
                        )
                    );
                }

                // Step 2 : Check if barangay is in database. 
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
            // end of barangay validation
          
            $user = TP_Store_Insert_address::catch_post();
            $date_created = TP_Globals::date_stamp();
            
            $get_store = $wpdb->get_row("SELECT ID FROM tp_stores  WHERE ID = '{$user["store_id"]}' AND  (SELECT child_val FROM tp_revisions WHERE ID = tp_stores.`status`  ) = 1
            ");
                
            // Step5 : Check if this store id exists
            if ( !$get_store ) {
                return array(
                        "status" => "failed",
                        "message" => "This store does not exists.",
                );
            }

            $wpdb->query("START TRANSACTION");

             //get country id
             $get_country = $wpdb->get_row("SELECT ID FROM dv_geo_countries WHERE `country_code` = '{$user["country"]}'");

             // Query of store address.
             $wpdb->query("INSERT INTO $dv_rev_table ($rev_fields) VALUES ('address', 'status', '1', '{$user["created_by"]}', '$date_created');");
             $status = $wpdb->insert_id;

             $wpdb->query("INSERT INTO $dv_rev_table ($rev_fields) VALUES ('address', 'street', '{$user["street"]}', '{$user["created_by"]}', '$date_created');");
             $street = $wpdb->insert_id;
 
             $wpdb->query("INSERT INTO $dv_rev_table ($rev_fields) VALUES ('address', 'brgy', '{$user["barangy"]}', '{$user["created_by"]}', '$date_created');");
             $brgy = $wpdb->insert_id;
 
             $wpdb->query("INSERT INTO $dv_rev_table ($rev_fields) VALUES ('address', 'city', '{$user["city"]}', '{$user["created_by"]}', '$date_created');");
             $city = $wpdb->insert_id;
                 
             $wpdb->query("INSERT INTO $dv_rev_table ($rev_fields) VALUES ('address', 'province', '{$user["province"]}', '{$user["created_by"]}', '$date_created');");
             $province = $wpdb->insert_id;
             
             $wpdb->query("INSERT INTO $dv_rev_table ($rev_fields) VALUES ('address', 'country', '$get_country->ID', '{$user["created_by"]}', '$date_created');");
             $country = $wpdb->insert_id;

             //Save the address in the parent table
             $wpdb->query("INSERT INTO $table_address (`status`, `types`, `stid`, `street`, `brgy`, `city`, `province`, `country`, `date_created`) 
                 VALUES ('$status', 'business', '{$user["store_id"]}', $street, $brgy, $city, $province, $country, '$date_created')");
             $address_id = $wpdb->insert_id;


            if ($status < 1 || $street < 1 || $brgy < 1 || $city < 1 || $province < 1 || $country < 1 ) {
                
                $wpdb->query("ROLLBACK");
                return array(
                    "status" => "failed",
                    "message" => "An error occured while submitting data to database.",
                );

            }else{

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