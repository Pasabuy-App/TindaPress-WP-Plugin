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
?>
<?php

    class TP_Select_Store {
        public static function listen(){
            global $wpdb;
            // variables for query
            $table_store = TP_STORES_TABLE;
            $table_revs = TP_REVISION_TABLE;

            $table_cotnacts = DV_CONTACTS_TABLE;
            $table_brgy = DV_BRGY_TABLE;
            $table_city = DV_CITY_TABLE;
            $table_province = DV_PROVINCE_TABLE;
            $table_country = DV_COUNTRY_TABLE;
            $table_dv_revs = DV_REVS_TABLE;
            $table_add = DV_ADDRESS_TABLE;

            // Step1 : check if datavice plugin is activated
            if (TP_Globals::verify_datavice_plugin() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Plugin Missing!",
                    )
                );
            }
            
            // Step2 : Check if wpid and snky is valid
            if (DV_Verification::is_verified() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Verification Issues!",
                    )
                );
            }

            // Step3 : Sanitize all Request
            if (!isset($_POST["stid"])) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Request unknown!",
                    )
                );
            }

            // Step4 : sanitize if all variables is empty
            if ( empty($_POST["stid"]) ){
                return rest_ensure_response( 
                    array(
                        "status" => "failed",
                        "message" => "Required fields cannot be empty.",
                    )
                );
            }
            
            // Step5 : Check if ID is in valid format (integer)
            if ( !is_numeric($_POST["stid"]) ) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. ID is not in valid format.",
                    )
                );
            }

            // Step6 : Validation of store id
            $stid = $_POST["stid"];
            $get_store = $wpdb->get_row("SELECT ID FROM $table_store  WHERE ID = $stid  ");
                
             if ( !$get_store ) {
                return rest_ensure_response( 
                    array(
                        "status" => "failed",
                        "message" => "No store found.",
                    )
                );
			}

            $user = TP_Select_Store::catch_post();

            // Step7 : Query
            $result = $wpdb->get_results("SELECT
                    tp_str.ID,
            (select child_val from $table_revs where id = (select title from tp_categories where id = tp_str.ctid)) AS category,
            tp_rev.child_val AS title,
            (select child_val from $table_revs where id = tp_str.short_info) AS bio,
            (select child_val from $table_revs where id = tp_str.long_info) AS details,
            (select child_val from $table_revs where id = tp_str.logo) AS icon,
            (select child_val from $table_revs where id = tp_str.banner) AS bg,
            (select child_val from $table_revs where id = tp_str.`status`) AS stats,
            (select child_val from $table_dv_revs where id = dv_add.street) as street,
            (SELECT brgy_name FROM $table_brgy WHERE ID = (select child_val from $table_dv_revs where id = dv_add.brgy)) as brgy,
            (SELECT citymun_name FROM $table_city WHERE city_code = (select child_val from $table_dv_revs where id = dv_add.city)) as city,
            (SELECT prov_name FROM $table_province WHERE prov_code = (select child_val from $table_dv_revs where id = dv_add.province)) as province,
            (SELECT country_name FROM $table_country WHERE id = (select child_val from $table_dv_revs where id = dv_add.country)) as country,
            (SELECT child_val from dv_revisions where id = max( IF ( dv_cont.types = 'phone', dv_cont.revs, '' )) ) AS phone,
            (SELECT child_val from dv_revisions where id = max( IF ( dv_cont.types = 'email', dv_cont.revs, '' )) ) AS email
                FROM
                    $table_store tp_str
                    INNER JOIN $table_revs tp_rev ON tp_rev.ID = tp_str.title 
                    INNER JOIN $table_add dv_add ON tp_str.address = dv_add.ID
                    INNER JOIN $table_cotnacts as dv_cont ON tp_str.ID = dv_cont.stid
                WHERE tp_str.ID = '{$user["store_id"]}'
                ");
            
            // Step8 : Check if no result
            if (!$result)
            {
                return rest_ensure_response( 
                    array(
                        "status" => "failed",
                        "message" => "No store found with this value."
                    )
                );
            }
            
            // Step9 : Return Result 
            return rest_ensure_response( 
                array(
                    "status" => "success",
                    "data" => array($result, 
                    )
                )
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