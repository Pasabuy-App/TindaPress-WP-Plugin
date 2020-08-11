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
    class TP_StorebyCategory {

        public static function listen(){
            return rest_ensure_response( 
                TP_StorebyCategory:: list_open()
            );
        }

        public static function list_open(){

            global $wpdb;

            // declaring table names to variable
            $table_store = TP_STORES_TABLE;
            $table_category = TP_CATEGORIES_TABLE;
            $table_revs = TP_REVISIONS_TABLE;
            $table_address = DV_ADDRESS_TABLE;
            $table_dv_revs = DV_REVS_TABLE;
            $table_brgy = DV_BRGY_TABLE;
            $table_city = DV_CITY_TABLE;
            $table_province = DV_PROVINCE_TABLE;
            $table_country = DV_COUNTRY_TABLE;

            // declaring variable
            $ctid = $_POST["catid"];
            
            //Step1 : Check if prerequisites plugin are missing
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
			if (!isset($_POST['catid']) ) {
				return array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Request unknown!",
                );
            }
            
            // Step4 : Sanitize if variable is empty
            if (empty($_POST["catid"])) {
				return array(
						"status" => "failed",
						"message" => "Required fileds cannot be empty.",
                );
            }

            // Step5 : Validation of category id
            $get_cat = $wpdb->get_row("SELECT ID FROM $table_category  WHERE ID = $ctid  ");
            if ( !$get_cat ) {
                return array(
                        "status" => "failed",
                        "message" => "No category found.",
                );
			}

            // Step6 : Query
            $result = $wpdb->get_results("SELECT
                tp_str.ID,
                (select child_val from $table_revs where id = (select title from tp_categories where id = tp_str.ctid)) AS category,
                (select child_val from $table_revs where id = tp_str.title) AS title,
                (select child_val from $table_revs where id = tp_str.short_info) AS short_info,
                (select child_val from $table_revs where id = tp_str.long_info) AS long_info,
                (select child_val from $table_revs where id = tp_str.logo) AS avatar,
                (select child_val from $table_revs where id = tp_str.banner) AS banner,
                (select child_val from $table_revs where id = tp_str.`status`) AS status,
                (select child_val from $table_dv_revs where id = dv_add.street) as street,
                (SELECT brgy_name FROM $table_brgy WHERE ID = (select child_val from $table_dv_revs where id = dv_add.brgy)) as brgy,
                (SELECT city_name FROM $table_city WHERE city_code = (select child_val from $table_dv_revs where id = dv_add.city)) as city,
                (SELECT prov_name FROM $table_province WHERE prov_code = (select child_val from $table_dv_revs where id = dv_add.province)) as province,
                (SELECT country_name FROM $table_country WHERE id = (select child_val from $table_dv_revs where id = dv_add.country)) as country
            FROM
                $table_store tp_str
            INNER JOIN 
                $table_address dv_add ON tp_str.address = dv_add.ID
            WHERE
                tp_str.ctid = '$ctid'
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

        

        
    }
