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
    class TP_Insert_Store {

        public static function listen(){
            return rest_ensure_response( 
                TP_Insert_Store::add_store()
            );
        }
        
        public static function add_store(){

            // 2nd Initial QA 2020-08-24 10:42 PM - Miguel
            global $wpdb;

            // declaring table names to variable
            $table_store        = TP_STORES_TABLE;
            $table_store_fields = TP_STORES_FIELDS;
            $table_tp_revs      = TP_REVISIONS_TABLE;
            $table_revs_fields  = TP_REVISION_FIELDS;
            $revs_type          = "stores";
            $table_contact      = DV_CONTACTS_TABLE;
            $table_dv_revs      = DV_REVS_TABLE;
            $rev_fields         = DV_INSERT_REV_FIELDS;
            $dv_rev_table       = DV_REVS_TABLE;
            $table_address      = DV_ADDRESS_TABLE;
            $table_category     = TP_CATEGORIES_TABLE;
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
            if ( !isset($_POST["title"]) 
                || !isset($_POST["logo"]) 
                || !isset($_POST["banner"]) 
                || !isset($_POST["st"]) 
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
            if ( empty($_POST["title"]) 
                || empty($_POST["logo"]) 
                || empty($_POST["banner"]) 
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
                // TODO : char length == 2 and trim and convert to ucase // DONE
                if ( strlen($_POST['co']) !== 2 ) {
                    return array(
                        "status" => "failed",
                        "message" => "Invalid value for country.",
                    );
                }

              // Step 2 : Check if country_id is in database. 
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
            //end of country validation

            //Province input validation
                // Step 2 : Check if province passed is in integer format.
                if ( !is_numeric($_POST['pv']) ) {
                    return array(
                        "status" => "failed",
                        "message" => "Invalid value for province.",
                    );
                }

                // Step 2 : Check if province is in database. 
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
            // end of province validation

            //City input validation
                // Step 2 : Check if city passed is in integer format.
                if ( !is_numeric($_POST['ct']) ) {
                    return array(
                        "status" => "failed",
                        "message" => "Invalid value for city.",
                    );
                }

                // Step 2 : Check if city is in database. 
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
            // end of city validation

            //Barangay input validation
                // Step 2 : Check if barangay passed is in integer format.
                if ( !is_numeric($_POST['bg']) ) {
                    return array(
                        "status" => "failed",
                        "message" => "Invalid value for barangay.",
                    );
                }

                // Step 2 : Check if barangay is in database. 
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
            // end of barangay validation



            // Validate Category
                if (!isset($_POST['catid'])) {
                    return array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Request unknown!",
                    );
                }

                if (empty($_POST['catid'])) {
                    return array(
                        "status" => "failed",
                        "message" => "Required fields cannot be empty.",
                    );
                }

                if (!is_numeric($_POST['catid'])) {
                    return array(
                        "status" => "failed",
                        "message" => "Category is not in valid format",
                    );
                }
                $cat = $_POST['catid'];
                $check_category = $wpdb->get_row(
                    $wpdb->prepare("SELECT `child_val` FROM $table_tp_revs WHERE ID = (SELECT `status` FROM $table_category WHERE ID = %d) AND revs_type = 'categories' AND child_key = 'status' AND parent_id = %d ", $cat, $cat )
                );

                if (!$check_category) {
                    return array(
                        "status" => "failed",
                        "message" => "This category does not exists",
                    );
                }
                
                if ($check_category->child_val == 0) {
                    return array(
                        "status" => "failed",
                        "message" => "This category is currently inactive",
                    );
                }
                
            // End of category validation

            // Step5 : Validate permission
            $permission = TP_Globals::verify_role($_POST['wpid'], '0', 'can_add_store' );
            if ($permission == true) {
                return array(
                    "status" => "failed",
                    "message" => "Current user has no access in manipulation of data.",
                );
            }

            $date_created = TP_Globals::date_stamp();
            $user = TP_Insert_Store::catch_post();

            // Step7 : Query
            $wpdb->query("START TRANSACTION");

                //get country id
                $get_country = $wpdb->get_row("SELECT ID FROM dv_geo_countries WHERE `country_code` = '$country_code'");

                $wpdb->query("INSERT INTO $table_tp_revs $table_revs_fields  VALUES ('$revs_type', '0', 'title', '{$user["title"]}', '{$user["created_by"]}', '$date_created')");
                $title = $wpdb->insert_id;

                $wpdb->query("INSERT INTO $table_tp_revs $table_revs_fields  VALUES ('$revs_type', '0', 'short_info', '{$user["short_info"]}', '{$user["created_by"]}', '$date_created')");
                $short_info = $wpdb->insert_id;

                $wpdb->query("INSERT INTO $table_tp_revs $table_revs_fields  VALUES ('$revs_type', '0', 'long_info', '{$user["long_info"]}', '{$user["created_by"]}', '$date_created')");
                $long_info = $wpdb->insert_id;

                $wpdb->query("INSERT INTO $table_tp_revs $table_revs_fields  VALUES ('$revs_type', '0', 'logo', '{$user["logo"]}', '{$user["created_by"]}', '$date_created')");
                $logo = $wpdb->insert_id;

                $wpdb->query("INSERT INTO $table_tp_revs $table_revs_fields  VALUES ('$revs_type', '0', 'banner', '{$user["banner"]}', '{$user["created_by"]}', '$date_created')");
                $banner = $wpdb->insert_id;

                $wpdb->query("INSERT INTO $table_tp_revs $table_revs_fields  VALUES ('$revs_type', '0', 'status', '1', '{$user["created_by"]}', '$date_created')");
                $status = $wpdb->insert_id;

                $wpdb->query(" INSERT INTO $table_tp_revs $table_revs_fields VALUES ( '$revs_type', '0', 'commission', '20', '{$user["created_by"]}', '$date_created' ) ");
                $comm = $wpdb->insert_id;


                // Insert query for store                                          
                $wpdb->query("INSERT INTO $table_store $table_store_fields VALUES ('{$user["catid"]}', $title, $short_info, $long_info, $logo, $banner, $status, '0', '{$user["created_by"]}', '$date_created' )");
                $store_id = $wpdb->insert_id;
                
                $wpdb->query("UPDATE $table_store SET hash_id = sha2($store_id, 256) WHERE ID = $store_id");
                
                // End query for store

                // update table revision
                 $result_update_tp_rev_store = $wpdb->query("UPDATE $table_tp_revs SET `parent_id` = $store_id WHERE ID IN ($title, $short_info, $long_info, $logo, $banner, $status, $comm) ");
            
            !isset($_POST['phone']) || empty($_POST['phone']) ? $phone =   NULL : $phone = $_POST['phone']; 
            !isset($_POST['email']) || empty($_POST['email']) ? $email =  NULL : $email = $_POST['email'] ; 

            if( $phone && $email ){
                    filter_var($email, FILTER_VALIDATE_EMAIL);

                    // Query of store contact.
                    // Phone
                    $wpdb->query("INSERT INTO `$table_dv_revs` (revs_type, parent_id, child_key, child_val, created_by, date_created) 
                                                    VALUES ( 'contacts', 0, 'phone', '$phone', '{$user["created_by"]}', '$date_created'  )");
                    $phone_last_id = $wpdb->insert_id;

                    $wpdb->query("INSERT INTO `$table_contact` (`status`, `types`, `revs`, `stid`, `created_by`, `date_created`) 
                                                        VALUES ('1', 'phone', '$phone_last_id', $store_id, '{$user["created_by"]}', '$date_created');");
                    $contact_phone_id = $wpdb->insert_id;
                    
                    $update_contact_phone = $wpdb->query("UPDATE `$table_dv_revs` SET `parent_id` = $contact_phone_id WHERE ID = $phone_last_id ");

                    // Email
                    $wpdb->query("INSERT INTO `$table_dv_revs` (revs_type, parent_id, child_key, child_val, created_by, date_created) 
                                                    VALUES ( 'contacts', 0, 'email', '$email', '{$user["created_by"]}', '$date_created'  )");
                    $email_last_id = $wpdb->insert_id;

                    $wpdb->query("INSERT INTO `$table_contact` (`status`, `types`, `revs`, `stid`, `created_by`, `date_created`) 
                                                        VALUES ('1', 'email', '$email_last_id', $store_id, '{$user["created_by"]}', '$date_created');");
                    $contact_email_id = $wpdb->insert_id;
                    
                    $update_contact_email = $wpdb->query("UPDATE `$table_dv_revs` SET `parent_id` = $contact_email_id WHERE ID = $email_last_id ");

                    // End of store contact query
                
            }

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
                    VALUES ('$status', 'business', '$store_id', $street, $brgy, $city, $province, $country, '$date_created')");
                $address_id = $wpdb->insert_id;

                $wpdb->query("UPDATE $dv_rev_table SET hash_id = sha2($address_id, 256) WHERE ID = $address_id");

                //Update revision table for saving the parent_id(address_id)
                $result_update_dv_rev_address =  $wpdb->query("UPDATE $dv_rev_table SET `parent_id` = {$address_id} WHERE ID IN ($status, $street, $brgy, $city, $province, $country)");

                
                // End of store address.

                // Update store for address column
                $result = $wpdb->query("UPDATE $table_store SET `address` = $address_id WHERE ID = $store_id ");

            // Step8 : Check if failed
            if ( $title < 1 || $short_info < 1 || $long_info < 1 || $logo < 1 || $banner < 1 || $status < 1 || $store_id < 1 || $result_update_tp_rev_store < 1 || 
                 $result_update_tp_rev_store < 1 || 
                $status < 1 || $street < 1 || $brgy < 1 || $city < 1 || $province < 1 || $country < 1 || $address_id < 1 || 
                $result_update_dv_rev_address < 1 || $result < 1 || $store_id < 1
               ) {
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

        // Catch Post 
        public static function catch_post()
        {
            $cur_user = array();
               
            $cur_user['created_by']  = $_POST["wpid"];
            $cur_user['catid']       = $_POST["catid"];

            $cur_user['title']       = $_POST["title"];
            $cur_user['short_info']  = isset($_POST["short_info"]) ? $_POST["short_info"] : "";
            $cur_user['long_info']   = isset($_POST["long_info"]) ? $_POST["short_info"] : "";
            $cur_user['logo']        = $_POST["logo"];
            $cur_user['banner']      = $_POST["banner"];

            // Address Listen
            $cur_user['street']     = $_POST["st"];
            $cur_user['country']    = $_POST["co"];
            $cur_user['province']   = $_POST["pv"];
            $cur_user['city']       = $_POST["ct"];
            $cur_user['barangy']    = $_POST["bg"];
               
            return  $cur_user;
        }
    }