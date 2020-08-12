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
    class TP_Category_List_Store_Inactive {
        
        public static function listen(){
            return rest_ensure_response( 
                TP_Category_List_Store_Inactive:: list_store_inactive()
            );
        }
        
        public static function list_store_inactive(){
            
            global $wpdb;
            $table_revs = TP_REVISIONS_TABLE;
            $table_categories = TP_CATEGORIES_TABLE;

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
                        "message" => "Please contact your administrator. Verification issues!",
                );
                
            }
            
            // Step 3: Start a query
            $categories = $wpdb->get_results("SELECT
                cat.ID as catid,
                cat.ID as stid,
                cat.types,
                ( SELECT child_val FROM $table_revs WHERE ID = cat.title ) AS `title`,
                ( SELECT child_val FROM $table_revs WHERE ID = cat.info ) AS `info`,
            IF
                ( ( SELECT child_val FROM $table_revs WHERE ID = cat.`status` ) = 1, 'Active', 'Inactive' ) AS `status` 
            FROM
                $table_categories cat 
            WHERE
                ( SELECT child_val FROM $table_revs WHERE ID = cat.`status` ) = 0
            AND cat.types = 'store'
                GROUP BY
                    cat.id");
            
            // Step 4: Check results if empty
            if (!$categories) {
                return array(
                    "status" => "failed",
                    "message" => "No results found.",
                );
            }

            // Step 5: Return a success status and message 
            return array(
                "status" => "success",
                "data" => $categories
            );
        
        }

    }