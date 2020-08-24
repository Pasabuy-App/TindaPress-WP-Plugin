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

    class TP_Activate_Store {

        public static function listen(){
            return rest_ensure_response( 
                TP_Activate_Store::list_open()
            );
        }
        
        //QA done 2020-08-12 10:10 PM
        public static function list_open(){

            // 2nd Initial QA 2020-08-24 10:33 PM - Miguel
            global $wpdb;
            
            $user = TP_Activate_Store::catch_post();

            // declaring table names to variable
            $table_store = TP_STORES_TABLE;
            $table_revisions = TP_REVISIONS_TABLE;
            $table_revision_field = TP_REVISION_FIELDS;
            $table_category = TP_CATEGORIES_TABLE;
            $date = TP_Globals::date_stamp();

            // Step 1: Check if prerequisites plugin are missing
            $plugin = TP_Globals::verify_prerequisites();
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
                    "message" => "Please contact your administrator. Verification Issues!",
                );
            }

            // Step 3: Check if required parameters are passed
            if (!isset($_POST["stid"])) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Missing paramiters.",
                );
            }
            
           // Step 4: Check if parameters passed are empty
            if (empty($_POST["stid"])) {
                return array(
                    "status" => "failed",
                    "message" => "Required fields cannot be empty.",
                );
            }

            $user = self::catch_post();
            
            // Step 5: Check if store exists
            $store_data = $wpdb->get_row("SELECT child_val as stats FROM $table_revisions WHERE ID = (SELECT `status` FROM $table_store WHERE ID = '{$user["store_id"]}')");
               
            // Check if no rows found
            if (!$store_data) {
                return array(
                    "status" => "failed",
                    "message" => "This store does not exists.",
                );
            }

            //Fails if already activated
            if ($store_data->stats == 1) {
                return array(
                    "status" => "failed",
                    "message" => "This store is already activated.",
                );
            }

            // Step 6: Start mysql transaction
            $wpdb->query("START TRANSACTION");

                //Get current value of this store
                $get_store_data = $wpdb->get_row("SELECT
                        str.ID,
                        str.ctid AS `catid`,
                        ( SELECT rev.child_val FROM $table_revisions rev WHERE rev.ID = cat.title  AND rev.date_created = (SELECT MAX(tp_rev.date_created) FROM $table_revisions tp_rev WHERE ID = rev.ID  AND revs_type ='categories'   )  ) as cat_name,
                        ( SELECT rev.child_val FROM $table_revisions rev WHERE rev.id = str.title AND  rev.date_created = ( SELECT MAX(date_created) FROM $table_revisions tp_rev WHERE tp_rev.ID = rev.ID AND revs_type = 'stores' )  ) AS title,
                        ( SELECT rev.child_val FROM $table_revisions rev WHERE rev.id = str.short_info AND  rev.date_created = ( SELECT MAX(date_created) FROM $table_revisions tp_rev WHERE tp_rev.ID = rev.ID AND revs_type = 'stores' ) ) AS short_info,
                        ( SELECT rev.child_val FROM $table_revisions rev WHERE rev.id = str.long_info AND  rev.date_created = ( SELECT MAX(date_created) FROM $table_revisions tp_rev WHERE tp_rev.ID = rev.ID AND revs_type = 'stores' ) ) AS long_info,
                        ( SELECT rev.child_val FROM $table_revisions rev WHERE rev.id = str.logo AND  rev.date_created = ( SELECT MAX(date_created) FROM $table_revisions tp_rev WHERE tp_rev.ID = rev.ID AND revs_type = 'stores' ) ) AS avatar,
                        ( SELECT rev.child_val FROM $table_revisions rev WHERE rev.id = str.banner AND  rev.date_created = ( SELECT MAX(date_created) FROM $table_revisions tp_rev WHERE tp_rev.ID = rev.ID AND revs_type = 'stores' ) ) AS banner,
                        ( SELECT rev.child_val FROM $table_revisions rev WHERE rev.id = str.`status` AND ID = (SELECT MAX(tp_rev.ID) FROM $table_revisions tp_rev WHERE tp_rev.ID = rev.ID AND tp_rev.child_key = 'status' AND tp_rev.revs_type = 'stores')   )  AS `status`,
                        ( SELECT dv_rev.child_val FROM dv_revisions  dv_rev WHERE dv_rev.ID = `add`.street AND dv_rev.date_created = (SELECT MAX(date_created)  FROM dv_revisions WHERE ID = dv_rev.ID AND revs_type ='address')   ) AS street,
                        ( SELECT brgy_name FROM dv_geo_brgys WHERE ID = ( SELECT dv_rev.child_val FROM dv_revisions dv_rev WHERE dv_rev.id = `add`.brgy  AND dv_rev.date_created = (SELECT MAX(date_created)  FROM dv_revisions WHERE ID = dv_rev.ID AND revs_type ='address') ) ) AS brgy,
                        ( SELECT city_name FROM dv_geo_cities WHERE city_code = ( SELECT dv_rev.child_val FROM dv_revisions dv_rev WHERE dv_rev.id = `add`.city  AND dv_rev.date_created = (SELECT MAX(date_created)  FROM dv_revisions WHERE ID = dv_rev.ID AND revs_type ='address')  ) ) AS city,
                        ( SELECT prov_name FROM dv_geo_provinces WHERE prov_code = ( SELECT dv_rev.child_val FROM dv_revisions dv_rev WHERE dv_rev.id = `add`.province AND dv_rev.date_created = (SELECT MAX(date_created)  FROM dv_revisions WHERE ID = dv_rev.ID AND revs_type ='address')  ) ) AS province,
                        ( SELECT country_name FROM dv_geo_countries WHERE id = ( SELECT dv_rev.child_val FROM dv_revisions dv_rev WHERE dv_rev.id = `add`.country  AND dv_rev.date_created = (SELECT MAX(date_created)  FROM dv_revisions WHERE ID = dv_rev.ID AND revs_type ='address')  ) ) AS country,
                        ( SELECT child_val FROM dv_revisions WHERE ID = ( SELECT revs FROM dv_contacts WHERE types = 'phone' AND stid = str.ID LIMIT 1 ) LIMIT 1 ) AS phone,
                        ( SELECT child_val FROM dv_revisions WHERE ID = ( SELECT revs FROM dv_contacts  WHERE types = 'email' AND stid = str.ID LIMIT 1 ) LIMIT 1 ) AS email 
                    FROM
                        $table_store str
                        INNER JOIN dv_address `add` ON str.address = `add`.ID
                        INNER JOIN $table_category cat ON cat.ID = str.ctid
                        WHERE 
                            str.ID = '{$user["store_id"]}'
                ");

            if ($get_store_data->status == '1') {
                return array(
                    "status" => "failed",
                    "message" => "This store is already activated."
                );
            }
            if (empty($get_store_data)) {
                return array(
                    "status" => "failed",
                    "message" => "This store does not exists."
                );
            }
            
            $results = $wpdb->query(" INSERT INTO $table_revisions $table_revision_field VALUES ('stores', '$get_store_data->ID', 'status', '1', '{$user["created_by"]}', '$date') ");
            $results_ID = $wpdb->insert_id;

            $update_store = $wpdb->query("UPDATE $table_store SET `status` = '$results_ID' WHERE ID = '$get_store_data->ID' ");

            // Step 7: Check if any queries above failed
            if ($results < 1 ) {
                //Do a rollback if any of the above queries failed
                $wpdb->query("ROLLBACK");
                return array(
                    "status" => "failed",
                    "message" => "An error occured while submmiting data to database.",
                );
            } else{
                //Commit if no errors found
                $wpdb->query("COMMIT");
                return array(
                    "status" => "success",
                    "message" => "Data has been activated successfully.",
                );
            }
        }  
        
        // Catch Post 
        public static function catch_post()
        {
              $cur_user = array();
               
                $cur_user['created_by'] = $_POST["wpid"];
                $cur_user['store_id']      = $_POST["stid"];
  
              return  $cur_user;
        }
    }