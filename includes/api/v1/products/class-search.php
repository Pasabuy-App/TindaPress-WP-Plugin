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

    class TP_Search_Products {

        public static function listen(){

            global $wpdb;

            // Variable for Tables
            $tp_revs = TP_REVISIONS_TABLE;
            $table_product = TP_PRODUCT_TABLE;
            $table_categories = TP_CATEGORIES_TABLE;

            // Step 1 : Check if prerequisites plugin are missing
            $plugin = TP_Globals::verify_prerequisites();
            if ($plugin !== true) {

                return array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. ".$plugin." plugin missing!",
                );
            }

			//  Step 2 : Validate if user is exist
			if (DV_Verification::is_verified() == false) {

                return array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Verification issues!",
                );
                
            }

            // Step 3 : Sanitize all Request
			if ( !isset($_POST['search']) ) {

				return array(
					"status" => "unknown",
					"message" => "Please contact your administrator. Request unknown!",
                );
            }

             // Step 4 : Sanitize all Request if emply
			if ( empty($_POST['search']) ) {

				return array(
					"status" => "unknown",
					"message" => "Required fields cannot be empyty.",
                );
            }

            $user = TP_Search_Products::catch_post();
            // Query
            $result = $wpdb->get_results("SELECT
                tp_prod.ID,
                ( SELECT tp_rev.child_val FROM tp_stores INNER JOIN $tp_revs tp_rev ON tp_rev.ID = tp_stores.title WHERE tp_prod.stid = tp_stores.id ) AS `store_name`,
                ( SELECT tp_cat.types FROM tp_categories tp_cat WHERE ID = tp_prod.ctid ) AS `category`,
                ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.title ) AS `title`,
                ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.preview ) AS `preview`,
                ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.short_info ) AS `short_info`,
                ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.long_info ) AS `long_info`,
                ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.`status` ) AS `status`,
                ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.sku ) AS `sku`,
                ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.price ) AS `price`,
                ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.weight ) AS `weight`,
                ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.dimension ) AS `dimension`,
                ( SELECT tp_rev.child_val FROM $tp_revs tp_rev WHERE ID = tp_prod.short_info ) AS `short_info` 
            FROM
                $table_product tp_prod
                INNER JOIN $tp_revs tp_rev ON tp_rev.ID = tp_prod.title 
            WHERE
                tp_rev.child_val REGEXP '^{$user["search"]}';", OBJECT);
            // Step 5 : Return results
            if(empty($result)){
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "No results found",
                    )
                );
                
            }else{
                return rest_ensure_response( 
                    array(
                        "status" => "success",
                        "data" => $result
                    )
                );
            }
        }

        // Catch Post 
        public static function catch_post()
        {
              $cur_user = array();
               
              $cur_user['created_by'] = $_POST["wpid"];
              $cur_user['search'] = $_POST["search"];
  
              return  $cur_user;
        }

    }