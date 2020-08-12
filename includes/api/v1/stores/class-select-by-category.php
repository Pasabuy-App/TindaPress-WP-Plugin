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
            $table_contacts = DV_CONTACTS_TABLE;
            $table_brgy = DV_BRGY_TABLE;
            $table_city = DV_CITY_TABLE;
            $table_province = DV_PROVINCE_TABLE;
            $table_country = DV_COUNTRY_TABLE;
            $table_dv_revisions = DV_REVS_TABLE;
            $table_add = DV_ADDRESS_TABLE;
            $table_category = TP_CATEGORIES_TABLE;

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
                ( SELECT rev.child_val FROM $table_revisions rev WHERE rev.ID = (SELECT ID FROM $table_category WHERE ID = tp_str.ctid and types = 'store' ) ) AS `category_name`,
                ( SELECT rev.child_val FROM $table_revisions rev WHERE rev.ID = tp_str.title ) AS `title`,
                ( SELECT rev.child_val FROM $table_revisions rev WHERE rev.ID = tp_str.short_info ) AS `short_info`,
                ( SELECT rev.child_val FROM $table_revisions rev WHERE rev.ID = tp_str.long_info ) AS `long_info`,
                ( SELECT rev.child_val FROM $table_revisions rev WHERE rev.ID = tp_str.logo ) AS `logo`,
                ( SELECT rev.child_val FROM $table_revisions rev WHERE rev.ID = tp_str.banner ) AS `banner`,
                ( SELECT dv_rev.child_val FROM $table_dv_revisions dv_rev WHERE dv_rev.ID = dv_add.street ) AS `street`,
                ( SELECT brgy_name FROM $table_brgy WHERE ID = ( SELECT child_val FROM $table_dv_revisions WHERE ID = dv_add.brgy ) ) AS brgy,
                ( SELECT city_name FROM $table_city WHERE city_code = ( SELECT child_val FROM $table_dv_revisions WHERE ID = dv_add.city ) ) AS city,
                ( SELECT prov_name FROM $table_province WHERE prov_code = ( SELECT child_val FROM $table_dv_revisions WHERE ID = dv_add.province ) ) AS province,
                ( SELECT country_name FROM $table_country WHERE id = ( SELECT child_val FROM $table_dv_revisions WHERE ID = dv_add.country ) ) AS country, 
                ( SELECT child_val FROM $table_dv_revisions WHERE ID  = ( SELECT revs FROM $table_contacts WHERE  types = 'phone' and stid =tp_str.ID  ) ) AS phone,
                ( SELECT child_val FROM $table_dv_revisions WHERE ID  = ( SELECT revs FROM $table_contacts WHERE  types = 'email' and stid =tp_str.ID  ) ) AS email
            FROM
                $table_store tp_str
                INNER JOIN  $table_add dv_add ON tp_str.address = dv_add.ID	
            WHERE 
                tp_str.ctid = '{$user["category_id"]}'
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