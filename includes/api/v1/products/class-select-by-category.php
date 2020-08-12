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

    class TP_Product_Select_Category {

        public static function listen(){
            return rest_ensure_response( 
                TP_Product_Select_Category:: get_prods_by_cat()
            );
        }

        //QA done 2020-08-12 4:44pm 
        public static function get_prods_by_cat(){

            global $wpdb;
            // Variables for Table
            $table_revs = TP_REVISIONS_TABLE;
            $table_product = TP_PRODUCT_TABLE;
            $table_categories = TP_CATEGORIES_TABLE;
            
            // Step 1: Check if prerequisites plugin are missing
            $plugin = TP_Globals::verify_prerequisites();
            if ($plugin !== true) {

                return array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. ".$plugin." plugin missing!",
                );
            }

			//  Step 2: Validate user
			if (DV_Verification::is_verified() == false) {
                return array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Verification Issues!",
                );
                
            }

            // Step 3: Check is params are passed
			if (!isset($_POST['catid']) ) {

				return array(
					"status" => "unknown",
					"message" => "Please contact your administrator. Request unknown!",
                );
            }

            // Step 4: Check if params passed are not empty
			if (empty($_POST['catid']) ) {

				return array(
					"status" => "unknown",
					"message" => "Required fields cannot be empty.",
                );
            }

            // Step 5: Check if this category exists and if its activated
            $category_id = $_POST['catid'];
            $get_category = $wpdb->get_row("SELECT
                    cat.ID, 
                    ( SELECT rev.child_val FROM $table_revs rev WHERE ID = cat.title ) AS title,
                    ( SELECT rev.child_val FROM $table_revs rev WHERE ID = cat.info ) AS info,
                    ( SELECT rev.child_val FROM $table_revs rev WHERE ID = cat.status) as `status`
                FROM
                    $table_product p
                INNER JOIN
                    $table_revs rev ON rev.parent_id = p.id
                INNER JOIN
                    $table_categories cat ON cat.id = p.ctid
                WHERE
                    p.ctid = $category_id
                GROUP BY
                    cat.id");
            
            //Check if 0 rows found
            if ( !$get_category ) {
                return array(
                    "status" => "failed",
                    "message" => "This category does not exists.",
                );
            }

            //Check if category is activated
            if ( $get_category->status == 0 ) {
                return array(
                    "status" => "failed",
                    "message" => "This category is currently inactive.",
                );
            }
            
            // Step 6: Start mysql query
            $result = $wpdb->get_results("SELECT
                tp_prod.ID,
                tp_prod.stid,
                tp_prod.ctid as catid,
                (select child_val from $table_revs where id = (select title from tp_categories where id = tp_prod.ctid)) AS cat_name,
                ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = (SELECT `title` FROM tp_stores WHERE ID  = tp_prod.stid ) ) AS `store_name`,
                ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE tp_rev.ID = tp_prod.title ) AS product_name,
                IF (( select child_val from $table_revs where id = tp_prod.`status` ) = 1, 'Active' , 'Inactive' ) AS `status`,
                ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.short_info ) AS `short_info`,
                ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.long_info ) AS `long_info`,
                ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.sku ) AS `sku`,
                ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.price ) AS `price`,
                ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.weight ) AS `weight`,
                ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.dimension ) AS `dimension`
            FROM
                $table_product tp_prod
            INNER JOIN 
                $table_revs tp_rev ON tp_rev.ID = tp_prod.title 
            WHERE
                tp_prod.ctid = $category_id
            GROUP BY
                tp_prod.ID ");
           
           // Step 7: Check if 0 rows found 
            if(!$result){

                return array(
                    "status" => "failed",
                    "message" => "No results found.",
                );
            // Return success status and complete object
            }else{

                return array(
                    "status" => "success",
                    "data" => $result
                );
            }
        }

    }