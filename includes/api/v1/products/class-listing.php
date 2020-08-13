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
    class TP_Product_Listing {
        public static function listen(){
            return rest_ensure_response( 
                TP_Product_Listing:: list_type()
            );
        }

        public static function list_type(){
            global $wpdb;
            $table_revs = TP_REVISIONS_TABLE;
            $table_product = TP_PRODUCT_TABLE;
            $table_categories = TP_CATEGORIES_TABLE;

            $sql = "SELECT
                tp_prod.ID,
                tp_prod.stid,
                tp_prod.ctid AS catid,
                ( SELECT tp_rev.child_val FROM tp_revisions tp_rev WHERE ID = ( SELECT `title` FROM tp_stores WHERE ID = tp_prod.stid ) ) AS `store_name`,
                ( SELECT tp_rev.child_val FROM tp_revisions tp_rev WHERE ID = c.title ) AS `cat_name`,
                ( SELECT tp_rev.child_val FROM tp_revisions tp_rev WHERE tp_rev.ID = tp_prod.title ) AS product_name,
                ( SELECT tp_rev.child_val FROM tp_revisions tp_rev WHERE ID = tp_prod.short_info ) AS `short_info`,
                ( SELECT tp_rev.child_val FROM tp_revisions tp_rev WHERE ID = tp_prod.long_info ) AS `long_info`,
                ( SELECT tp_rev.child_val FROM tp_revisions tp_rev WHERE ID = tp_prod.sku ) AS `sku`,
                ( SELECT tp_rev.child_val FROM tp_revisions tp_rev WHERE ID = tp_prod.price ) AS `price`,
                ( SELECT tp_rev.child_val FROM tp_revisions tp_rev WHERE ID = tp_prod.weight ) AS `weight`,
                ( SELECT tp_rev.child_val FROM tp_revisions tp_rev WHERE ID = tp_prod.dimension ) AS `dimension`,
            IF
                ( ( SELECT tp_rev.child_val FROM tp_revisions tp_rev WHERE ID = tp_prod.STATUS ) = 1, 'Active', 'Inactive' ) AS `status` 
            FROM
                tp_products tp_prod
                INNER JOIN tp_revisions tp_rev ON tp_rev.ID = tp_prod.title
                INNER JOIN tp_categories c ON c.ID = tp_prod.ctid  ";

            
            if (isset($_POST['stid']) && $_POST['stid'] > 0  ) {

                $sql .= "WHERE cat.`stid` = '$stid' AND cat.types = 'product' ";

            }
        
           

        return $wpdb->get_results($sql);
                    
                


            



        }
        
    }
