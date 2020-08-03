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

	class TP_Store {
        
        public static function category(){
            // Initialize WP global variable
			global $wpdb;

			//  Step1 : Verify if Datavice Plugin is Active
			if (TP_Globals::verifiy_datavice_plugin() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Plugin Missing!",
                    )
                );
			}
			
			//  Step2 : Validate if user is exist
			if (TP_Globals::validate_user() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Request Unknown!",
                    )
                );
            }

            // Step3 : Sanitize all Request
			if (!isset($_POST["wpid"]) || !isset($_POST["snky"])) {
				return rest_ensure_response( 
					array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Request unknown!",
					)
                );
                
            }


            // Step 4: Check if ID is in valid format (integer)
			if (!is_numeric($_POST["wpid"])) {
				return rest_ensure_response( 
					array(
						"status" => "failed",
						"message" => "Please contact your administrator. ID not in valid format!",
					)
                );
                
			}

			// Step 5: Check if ID exists
			if (!get_user_by("ID", $_POST['wpid'])) {
				return rest_ensure_response( 
					array(
						"status" => "failed",
						"message" => "User not found!",
					)
                );
                
			}
			

			
            // Step6 : Sanitize all Request
			if (empty($_POST["wpid"]) || empty($_POST["snky"])) {
				return rest_ensure_response( 
					array(
						"status" => "unknown",
						"message" => "Required fields cannot be empty.",
					)
                );
                
            }


            //Step 7: Create table name for posts (tp_categories, tp_categories_revs)
			$categories_table      = TP_CATEGORIES_TABLE;
			$categories_revs_table = TP_CATEGORIES_REVS_TABLE;
			
			//Step 8: Get results from database 
			$result= $wpdb->get_results("SELECT
				cat.id,
				cat.types,
				max( IF ( cat_r.child_key = 'title', cat_r.child_val, '' ) ) AS title,
				max( IF ( cat_r.child_key = 'info', cat_r.child_val, '' ) ) AS info
			FROM
				$categories_table cat
				INNER JOIN $categories_revs_table cat_r ON cat.title = cat_r.ID 
				OR cat.info = cat_r.ID 
			GROUP BY
				cat_r.parent_id DESC", OBJECT);


            //Step 9: return result
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
?>