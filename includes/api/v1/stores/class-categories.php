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

            // Step1 : Sanitize all Request
			if (!isset($_GET["wpid"]) || !isset($_GET["snky"])) {
				return rest_ensure_response( 
					array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Request unknown!",
					)
                );
                
            }


            // Step 2: Check if ID is in valid format (integer)
			if (!is_numeric($_GET["wpid"])) {
				return rest_ensure_response( 
					array(
						"status" => "failed",
						"message" => "Please contact your administrator. ID not in valid format!",
					)
                );
                
			}

			// Step 3: Check if ID exists
			if (!get_user_by("ID", $_GET['wpid'])) {
				return rest_ensure_response( 
					array(
						"status" => "failed",
						"message" => "User not found!",
					)
                );
                
            }


            //Step 4: Create table name for posts (tp_categories, tp_categories_revs)
			$table_categories = TP_PREFIX.'categories';
			$table_categories_revs = TP_PREFIX.'categories_revs';
				
			//Step 5: Get results from database 
			$result= $wpdb->get_results("SELECT
                tp_categories.id,
                tp_categories.types,
                max( IF ( tp_categories_revs.child_key = 'title', tp_categories_revs.child_val, '' ) ) AS title,
                max( IF ( tp_categories_revs.child_key = 'info', tp_categories_revs.child_val, '' ) ) AS info
                
            FROM
                tp_categories
                INNER JOIN tp_categories_revs ON tp_categories.title = tp_categories_revs.ID 
                OR tp_categories.info = tp_categories_revs.ID 
            GROUP BY
                tp_categories_revs.parent_id DESC", OBJECT);
            //Step 6: return result
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