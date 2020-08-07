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
            if (TP_Globals::validate_user() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Request Unknown!",
                    )
                );
            }

            if (!isset($_POST["stid"])) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Request Unknown!",
                    )
                );
            }
            
            if ( !is_numeric($_POST["stid"]) ) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. ID is not in valid format.",
                    )
                );
            }


            if ( empty($_POST["stid"]) ){
                return rest_ensure_response( 
                    array(
                        "status" => "failed",
                        "message" => "Required fields cannot be empty.",
                    )
                );
            }


            // variables for query
            $table_store = TP_STORES_TABLE;
            $table_revs = TP_REVISION_TABLE;

            $table_brgy = DV_BRGY_TABLE;
            $table_city = DV_CITY_TABLE;
            $table_province = DV_PROVINCE_TABLE;
            $table_country = DV_COUNTRY_TABLE;
            $table_dv_revs = DV_REVS_TABLE;
            $table_add = DV_ADDRESS_TABLE;
            
            $user = TP_Select_Store::catch_post();
            
            $result = $wpdb->get_results("SELECT
                    tp_str.ID,
                    tp_rev.child_val AS title,
                    ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_str.short_info ) AS `short_info`,
                    ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_str.long_info ) AS `long_info`,
                    ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_str.logo ) AS `logo`,
                    ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_str.banner ) AS `banner`,
                    ( SELECT dv_rev.child_val FROM $table_revs dv_rev WHERE dv_rev.ID = dv_add.street ) AS `street`,
                    ( SELECT brgy_name FROM $table_brgy WHERE ID = ( SELECT child_val FROM $table_revs dv_rev WHERE dv_rev.ID = dv_add.brgy ) ) AS brgy,
                    ( SELECT city_name FROM $table_city WHERE ID = ( SELECT child_val FROM $table_revs dv_rev WHERE dv_rev.ID = dv_add.city ) ) AS city,
                    ( SELECT prov_name FROM $table_province WHERE ID = ( SELECT child_val FROM $table_revs dv_rev WHERE dv_rev.ID = dv_add.province ) ) AS province,
                    ( SELECT country_name FROM $table_country WHERE ID = ( SELECT child_val FROM $table_revs dv_rev WHERE dv_rev.ID = dv_add.country ) ) AS country 
                FROM
                    $table_store tp_str
                    INNER JOIN $table_revs tp_rev ON tp_rev.ID = tp_str.title 
                    INNER JOIN $table_add dv_add ON tp_str.address = dv_add.ID
                WHERE tp_str.ID = '{$user["store_id"]}'
                ");


            if (!$result ) {
                return array(
                    "status" => "failed",
                    "message" => "An error occured while fetching data to database.",
                );
            }else{
                return  array(
                    "status" => "success",
                    "data" => array(
                        'list' => $result, 
                        
                    )
                );
            }
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