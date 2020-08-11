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
    class TP_Store_Select_by_Category {

        public static function listen(){
            return rest_ensure_response( 
                TP_Store_Select_by_Category:: list_open()
            );
        }

        public static function list_open(){

            global $wpdb;

            $user = TP_Store_Select_by_Category::catch_post();

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
            if (!isset($_POST["catid"])) {
                return array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Request unknown!",
                );
            }

            // Step4 : Sanitize if variable is empty
            if ( empty($_POST["catid"]) ){
                return array(
                        "status" => "failed",
                        "message" => "Required fields cannot be empty.",
                );
            }

            // Step6 : Query
            $result = $wpdb->get_results("SELECT
                tp_str.ID,
                (select child_val from $table_revisions where id = (select title from tp_categories where id = tp_str.ctid)) AS category,
                tp_rev.child_val AS title,
                (select child_val from $table_revisions where id = tp_str.short_info) AS short_info,
                (select child_val from $table_revisions where id = tp_str.long_info) AS long_info,
                (select child_val from $table_revisions where id = tp_str.logo) AS avatar,
                (select child_val from $table_revisions where id = tp_str.banner) AS banner,
                (select child_val from $table_revisions where id = tp_str.`status`) AS status,
                (select child_val from $table_dv_revisions where id = dv_add.street) as street,
                (SELECT brgy_name FROM $table_brgy WHERE ID = (select child_val from $table_dv_revisions where id = dv_add.brgy)) as brgy,
                (SELECT city_name FROM $table_city WHERE city_code = (select child_val from $table_dv_revisions where id = dv_add.city)) as city,
                (SELECT prov_name FROM $table_province WHERE prov_code = (select child_val from $table_dv_revisions where id = dv_add.province)) as province,
                (SELECT country_name FROM $table_country WHERE id = (select child_val from $table_dv_revisions where id = dv_add.country)) as country,
                (SELECT child_val from dv_revisions where id = max( IF ( dv_cont.types = 'phone', dv_cont.revs, '' )) ) AS phone,
                (SELECT child_val from dv_revisions where id = max( IF ( dv_cont.types = 'email', dv_cont.revs, '' )) ) AS email
            FROM
                $table_store tp_str
            INNER JOIN 
                $table_revisions tp_rev ON tp_rev.ID = tp_str.title 
            INNER JOIN 
                $table_add dv_add ON tp_str.address = dv_add.ID
            INNER JOIN 
                $table_cotnacts dv_cont ON tp_str.ID = dv_cont.stid
            WHERE 
                tp_str.ctid  = '{$user["category_id"]}'
            ");
            
            // Step7 : Check if no result
            if (!$result)
            {
                return array(
                        "status" => "unknown",
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
               
                $cur_user['created_by']  = $_POST["wpid"];
                $cur_user['category_id'] = $_POST["catid"];
  
              return  $cur_user;
        }
    }