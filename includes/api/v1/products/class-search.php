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


              // Step 1 : Verfy if Datavice Plugin is Activated
			if (TP_Globals::verify_datavice_plugin() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Plugin Missing!",
                    )
                );
			}
			//step 2: validate User
			if (TP_Globals::validate_user() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Request Unknown!",
                    )
                );
            }

            // Step3 : Sanitize all Request
			if ( !isset($_POST['search']) ) {
				return rest_ensure_response( 
					array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Request unknown!",
					)
                );
                
            }

             // Step6 : Sanitize all Request if emply
			if ( empty($_POST['search']) ) {
				return rest_ensure_response( 
					array(
						"status" => "unknown",
						"message" => "Required fields cannot be empyty.",
					)
                );
                
            }

            $user = TP_Search_Products::catch_post();

            $tp_revs = TP_REVISION_TABLE;
            $table_product = TP_PRODUCT_TABLE;
            $table_categories = TP_CATEGORIES_TABLE;

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

            if(empty($result)){
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. No Product with this value",
                    )
                );
                
            }else{
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


        // Catch Post 
        public static function catch_post()
        {
              $cur_user = array();
               
              $cur_user['created_by'] = $_POST["wpid"];
              $cur_user['search'] = $_POST["search"];
  
              return  $cur_user;
        }

    }