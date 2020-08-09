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

    class TP_StorebyCategory {

        public static function listen(){
            global $wpdb;

            // declater table names to vatiable
            $table_store = TP_STORES_TABLE;
            $table_category = TP_CATEGORIES_TABLE;
            $table_revs = TP_REVISION_TABLE;
            $table_brgy = DV_BRGY_TABLE;
            $table_city = DV_CITY_TABLE;
            $table_province = DV_PROVINCE_TABLE;
            $table_country = DV_COUNTRY_TABLE;
            $table_dv_revs = DV_REVS_TABLE;
            $table_add = DV_ADDRESS_TABLE;
            $catid = $_POST['catid'];
            
            // Step1: verify of datavice plugin is activated
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
			if (!isset($_POST['catid']) ) {
				return rest_ensure_response( 
					array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Request unknown!",
					)
                );
                
            }

            
            // Step 4: sanitize if all variables is empty
            if (empty($_POST["catid"])) {
				return rest_ensure_response( 
					array(
						"status" => "unknown",
						"message" => "Required fileds cannot be empty!",
					)
                );
                
            }

            // Step 5: Check if ID is in valid format (integer)
			if (!is_numeric($_POST['catid'])) {
				return rest_ensure_response( 
					array(
						"status" => "failed",
						"message" => "Please contact your administrator. ID not in valid format!",
					)
                );
                
			}
            $ctid = $_POST["catid"];
            $get_cat = $wpdb->get_row("SELECT ID FROM $table_category  WHERE ID = $ctid  ");
                
             if ( !$get_cat ) {
                return rest_ensure_response( 
                    array(
                        "status" => "failed",
                        "message" => "No category found.",
                    )
                );
			}

            // Step7 : Query
            $result = $wpdb->get_results("SELECT
            tp_str.ID,
            (select child_val from $table_revs where id = (select title from tp_categories where id = tp_str.ctid)) AS cat,
            (select child_val from $table_revs where id = tp_str.title) AS stname,
            (select child_val from $table_revs where id = tp_str.short_info) AS bio,
            (select child_val from $table_revs where id = tp_str.long_info) AS details,
            (select child_val from $table_revs where id = tp_str.logo) AS icon,
            (select child_val from $table_revs where id = tp_str.banner) AS bg,
            (select child_val from $table_revs where id = tp_str.`status`) AS stats,
            (select child_val from $table_dv_revs where id = dv_add.street) as street,
            (SELECT brgy_name FROM $table_brgy WHERE ID = (select child_val from $table_dv_revs where id = dv_add.brgy)) as brgy,
            (SELECT citymun_name FROM $table_city WHERE city_code = (select child_val from $table_dv_revs where id = dv_add.city)) as city,
            (SELECT prov_name FROM $table_province WHERE prov_code = (select child_val from $table_dv_revs where id = dv_add.province)) as province,
            (SELECT country_name FROM $table_country WHERE id = (select child_val from $table_dv_revs where id = dv_add.country)) as country
            FROM
            $table_store tp_str
            INNER JOIN $table_add dv_add ON tp_str.address = dv_add.ID
            WHERE
            tp_str.ctid = '$catid'
            ");
            
            if (!$result)
            {
                return rest_ensure_response( 
                    array(
                        "status" => "failed",
                        "message" => "No store found with this value."
                    )
                );
            }
            
            // Step8 : Return Result 
            return rest_ensure_response( 
                array(
                    "status" => "success",
                    "data" => array(
                        'list' => $result, 
                    
                    )
                )
            );

        }   

        

        
    }
