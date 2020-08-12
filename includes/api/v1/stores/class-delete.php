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

    class TP_Delete_Store {

        public static function listen(){
            return rest_ensure_response( 
                TP_Delete_Store::list_open()
            );
        }
        
        public static function list_open(){

            global $wpdb;
            
            $user = TP_Delete_Store::catch_post();

            // declaring table names to variable
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
                        "message" => "Please contact your administrator. Verification Issues!",
                );
            }

            // Step3 : Sanitize request
            if (!isset($_POST["stid"])) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Missing paramiters.",
                );
            }
            
            // Step4 : Sanitize variable is empty
            if (empty($_POST["stid"])) {
                return array(
                    "status" => "failed",
                    "message" => "Required fields cannot be empty.",
                );
            }
            
            // Step5 :  Query
            $store_data = $wpdb->get_row("SELECT child_val as stats FROM tp_revisions WHERE ID = (SELECT `status` FROM tp_stores WHERE ID = '{$user["store_id"]}')");
               
            // Step6 :  Check if failed
            if (!$store_data) {
                return array(
                    "status" => "failed",
                    "message" => "This store does not exists..",
                );
            }

            if ($store_data->stats == 0) {
                return array(
                    "status" => "failed",
                    "message" => "This store is already deactivated.",
                );
            }

            $wpdb->query("START TRANSACTION");

                $get_last_value = $wpdb->get_row("SELECT
                    tp_rev.child_val AS title,
                    (select child_val from $table_revisions where id = tp_str.short_info) AS short_info,
                    (select child_val from $table_revisions where id = tp_str.long_info) AS long_info,
                    (select child_val from $table_revisions where id = tp_str.logo) AS logo,
                    (select child_val from $table_revisions where id = tp_str.banner) AS banner,
                    (select child_val from $table_revisions where id = tp_str.status) AS `status`
                FROM
                    $table_store tp_str
                INNER JOIN 
                    $table_revisions tp_rev ON tp_rev.ID = tp_str.title 
                INNER JOIN 
                    $table_add dv_add ON tp_str.address = dv_add.ID
                INNER JOIN 
                    $table_contacts as dv_cont ON tp_str.ID = dv_cont.stid
                WHERE 
                    tp_str.ID = '{$user["store_id"]}'
                ");

                $wpdb->query("INSERT INTO $table_revisions ");




            $wpdb->query("ROLLBACK");

            $get_store_data = $wpdb->get_row("SELECT * FROM tp_stores WHERE ID = '{$user["store_id"]}'");


            // Step7 :  Query
            $result = $wpdb->query("UPDATE $table_revs SET `child_val` = '0' WHERE ID = $get_store_data->status ");

            // Step8 :  Check if failed
            if ($result < 1 ) {
                return array(
                    "status" => "failed",
                    "message" => "An error occured while submmiting data to database.",
                );
            } else{
                return array(
                    "status" => "success",
                    "message" => "Data has been deleted successfully.",
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