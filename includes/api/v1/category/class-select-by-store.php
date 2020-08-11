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
    class TP_Category_Select_Store {
        
        public static function listen(){
            return rest_ensure_response( 
                TP_Category_Select_Store:: select_by_store()
            );
        }
        
        public static function select_by_store(){
            
            //Inital QA done 2020-08-11 10:40 AM
            global $wpdb;
            $table_revs = TP_REVISIONS_TABLE;
            $table_categories = TP_CATEGORIES_TABLE;
            $table_stores = TP_STORES_TABLE;

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
            
            // Step 3: Check if parameters are passed
            if (!isset($_POST["stid"])  ) {
				return array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Request unknown!",
                );
            }

            // Step 4: Check if parameters passed are not null
            if (empty($_POST["stid"])  ) {
				return array(
						"status" => "failed",
						"message" => "Required fields cannot be empty.",
                );
            }

            $store_id = $_POST['stid'];

            // Step 5: Start mysql query
            $categories = $wpdb->get_results("SELECT
                cat.ID, cat.types,
                ( SELECT rev.child_val FROM $table_revs rev WHERE ID = cat.title ) AS title,
                ( SELECT rev.child_val FROM $table_revs rev WHERE ID = cat.info ) AS info
            FROM
                $table_categories cat
            INNER JOIN
                $table_stores s ON s.ctid = cat.id
            INNER JOIN
                $table_revs rev ON rev.parent_id = cat.id
            WHERE
                s.id = $store_id 
            AND
                (SELECT rev.child_val FROM $table_revs rev WHERE ID = cat.status) = 1
            GROUP BY
                cat.id");
            
            // Step 6: Check results if empty
            if (!$categories) {
                return array(
                    "status" => "failed",
                    "message" => "No results found.",
                );
            }

            // Step 7: Return a success status and message 
            return array(
                "status" => "success",
                "data" => $categories
            );
        
        }

    }