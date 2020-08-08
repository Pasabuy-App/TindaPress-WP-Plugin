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
    class TP_Category_List {
        
        public static function listen(){
            return rest_ensure_response( 
                TP_Category_List:: list_type()
            );
        }
        
        public static function list_type(){
            global $wpdb;

            //  Step1 : Verify if Datavice Plugin is Active
			if (TP_Globals::verify_datavice_plugin() == false) {
                return  array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Plugin Missing!",
                );
                
			}
			
			//  Step2 : Validate if user is exist
			if (DV_Verification::is_verified() == false) {
                return array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Request Unknown!",
                );
                
            }
            
            if (!isset($_POST["types"])  ) {
				return array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Request unknown!",
                );
            }

            if (empty($_POST["types"])  ) {
				return array(
						"status" => "failed",
						"message" => "Required fields cannot be empty.",
                );
            }

            if ( !($_POST['types'] === 'store') && !($_POST['types'] === 'product')) {
                return array(
                    "status" => "failed",
                    "message" => "Category must be product or store only.",
                );
            }

            $types = $_POST['types'];
            $table_revs = TP_REVISION_TABLE;
            $table_categories = TP_CATEGORIES_TABLE;

            $categories = $wpdb->get_results("SELECT
                cat.ID, cat.types,
                ( SELECT rev.child_val FROM $table_revs rev WHERE ID = cat.title ) AS title,
                ( SELECT rev.child_val FROM $table_revs rev WHERE ID = cat.info ) AS info,
                ( SELECT rev.child_val FROM $table_revs rev WHERE ID = cat.status ) AS status
            FROM
                $table_categories cat
                INNER JOIN $table_revs rev ON rev.parent_id = cat.id 
            WHERE
                cat.types = '$types'
            AND
                (SELECT rev.child_val FROM $table_revs rev WHERE ID = cat.status) = 1
            GROUP BY
                cat.id");
            
            if (!$categories) {
                return array(
                    "status" => "failed",
                    "message" => "No results found.",
                );
            }

            return array(
                "status" => "success",
                "data" => $categories
            );
        
        }

    }