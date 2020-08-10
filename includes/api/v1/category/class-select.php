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
    class TP_Category_Select {
        
        public static function listen(){
            return rest_ensure_response( 
                TP_Category_Select:: select()
            );
        }
        
        public static function select(){
            global $wpdb;
           
			//Check if prerequisites plugin are missing
            $plugin = TP_Globals::verify_prerequisites();
            if ($plugin !== true) {
                return array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. ".$plugin." plugin missing!",
                );
            }
			
			//  Step2 : Validate if user is exist
			if (DV_Verification::is_verified() == false) {
                return array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Verification issues!",
                );
                
            }
            
            if (!isset($_POST["catid"])  ) {
				return array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Request unknown!",
                );
            }

            if (empty($_POST["catid"])  ) {
				return array(
						"status" => "failed",
						"message" => "Required fields cannot be empty.",
                );
            }

            $category_id = $_POST['catid'];
            $table_revs = TP_REVISIONS_TABLE;
            $table_categories = TP_CATEGORIES_TABLE;

            $category = $wpdb->get_row("SELECT
                cat.ID, cat.types,
                ( SELECT rev.child_val FROM $table_revs rev WHERE ID = cat.title ) AS title,
                ( SELECT rev.child_val FROM $table_revs rev WHERE ID = cat.info ) AS info,
                ( SELECT rev.child_val FROM $table_revs rev WHERE ID = cat.status ) AS status
            FROM
                $table_categories cat
            INNER JOIN 
                $table_revs rev ON rev.parent_id = cat.id 
            WHERE
                cat.id = $category_id
            AND
                (SELECT rev.child_val FROM $table_revs rev WHERE ID = cat.status) = 1");
            
            if (!$category) {
                return array(
                    "status" => "failed",
                    "message" => "No results found.",
                );
            }

            return array(
                "status" => "success",
                "data" => $category
            );
        
        }

    }