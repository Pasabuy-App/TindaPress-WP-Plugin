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
    class TP_Store_Select {

        public static function listen(){
            return rest_ensure_response( 
                TP_Store_Select:: list_open()
            );
        }

        public static function list_open(){

            global $wpdb;

            $user = TP_Store_Select::catch_post();

            // declaring table names to variable
            $table_store = TP_STORES_TABLE;
            $table_revisions = TP_REVISIONS_TABLE;
            $table_cotnacts = DV_CONTACTS_TABLE;
            $table_brgy = DV_BRGY_TABLE;
            $table_city = DV_CITY_TABLE;
            $table_province = DV_PROVINCE_TABLE;
            $table_country = DV_COUNTRY_TABLE;
            $table_dv_revisions = DV_REVS_TABLE;
            $table_add = DV_ADDRESS_TABLE;

            // declaring variable
            $stid = $_POST["stid"];

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
            if (!isset($_POST["stid"])) {
                return array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Request unknown!",
                );
            }

            // Step4 : Sanitize if variable is empty
            if ( empty($_POST["stid"]) ){
                return array(
                        "status" => "failed",
                        "message" => "Required fields cannot be empty.",
                );
            }

            // Step5 : Validation of store id
            $get_store = $wpdb->get_row("SELECT ID FROM $table_store  WHERE ID = $stid ");    
            if ( !$get_store ) {
                return array(
                        "status" => "failed",
                        "message" => "No store found.",
                );
			}

            // Step6 : Query
            $result = $wpdb->get_results("SELECT
                tp_str.ID,
                (select child_val from $table_revisions where id = (select title from tp_categories where id = tp_str.ctid)) AS category,
                tp_rev.child_val AS title,
<<<<<<< HEAD
                (select child_val from $table_revisions where id = tp_str.short_info) AS short_info,
                (select child_val from $table_revisions where id = tp_str.long_info) AS long_info,
                (select child_val from $table_revisions where id = tp_str.logo) AS avatar,
                (select child_val from $table_revisions where id = tp_str.banner) AS banner,
                (select child_val from $table_revisions where id = tp_str.`status`) AS status,
                (select child_val from $table_dv_revisions where id = dv_add.street) as street,
                (SELECT brgy_name FROM $table_brgy WHERE ID = (select child_val from $table_dv_revisions where id = dv_add.brgy)) as brgy,
                (SELECT citymun_name FROM $table_city WHERE city_code = (select child_val from $table_dv_revisions where id = dv_add.city)) as city,
                (SELECT prov_name FROM $table_province WHERE prov_code = (select child_val from $table_dv_revisions where id = dv_add.province)) as province,
                (SELECT country_name FROM $table_country WHERE id = (select child_val from $table_dv_revisions where id = dv_add.country)) as country,
=======
                (select child_val from $table_revs where id = tp_str.short_info) AS short_info,
                (select child_val from $table_revs where id = tp_str.long_info) AS long_info,
                (select child_val from $table_revs where id = tp_str.logo) AS avatar,
                (select child_val from $table_revs where id = tp_str.banner) AS banner,
                (select child_val from $table_revs where id = tp_str.`status`) AS status,
                (select child_val from $table_dv_revs where id = dv_add.street) as street,
                (SELECT brgy_name FROM $table_brgy WHERE ID = (select child_val from $table_dv_revs where id = dv_add.brgy)) as brgy,
                (SELECT city_name FROM $table_city WHERE city_code = (select child_val from $table_dv_revs where id = dv_add.city)) as city,
                (SELECT prov_name FROM $table_province WHERE prov_code = (select child_val from $table_dv_revs where id = dv_add.province)) as province,
                (SELECT country_name FROM $table_country WHERE id = (select child_val from $table_dv_revs where id = dv_add.country)) as country,
>>>>>>> 9d33a812571ec6fcefe42f9d729f6b62c73833ab
                (SELECT child_val from dv_revisions where id = max( IF ( dv_cont.types = 'phone', dv_cont.revs, '' )) ) AS phone,
                (SELECT child_val from dv_revisions where id = max( IF ( dv_cont.types = 'email', dv_cont.revs, '' )) ) AS email
            FROM
                $table_store tp_str
            INNER JOIN 
                $table_revisions tp_rev ON tp_rev.ID = tp_str.title 
            INNER JOIN 
                $table_address dv_add ON tp_str.address = dv_add.ID
            INNER JOIN 
                $table_contacts as dv_cont ON tp_str.ID = dv_cont.stid
            WHERE 
                tp_str.ID = '{$user["store_id"]}'
            ");
            
            // Step7 : Check if no result
            if (!$result)
            {
                return array(
                        "status" => "failed",
                        "message" => "No results found.",
                );
            }
            
            // Step8 : Return Result 
            return array(
                    "status" => "success",
                    "data" => $result
            );
        }

        // Catch Post 
        public static function catch_post()
        {
              $cur_user = array();
               
                $cur_user['created_by'] = $_POST["wpid"];
                $cur_user['store_id'] = $_POST["stid"];
  
              return  $cur_user;
        }
    }