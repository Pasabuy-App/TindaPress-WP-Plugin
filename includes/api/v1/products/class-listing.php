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

            // 2nd Initial QA 2020-08-24 6:36 PM - Miguel
            global $wpdb;
            $table_revisions = TP_REVISIONS_TABLE;
            $table_product = TP_PRODUCT_TABLE;
            $table_categories = TP_CATEGORIES_TABLE;
            $table_variants = TP_VARIANTS_TABLE;

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
                    "message" => "Please contact your administrator. verification issues!",
                );
                
            }

            // Start MYSQL Query 
            $sql = "SELECT
                tp_prod.ID,
                tp_prod.stid,
                tp_prod.ctid AS catid,
                ( SELECT COUNT(pdid) FROM $table_variants WHERE pdid = tp_prod.ID AND parent_id = 0 ) as `total`,
                ( SELECT tp_rev.child_val FROM $table_revisions tp_rev WHERE ID = ( SELECT `title` FROM tp_stores WHERE ID = tp_prod.stid ) AND revs_type = 'stores' AND child_key ='title' AND tp_rev.ID = (SELECT MAX(ID) FROM $table_revisions WHERE ID = tp_rev.ID )  ) AS `store_name`,
                ( SELECT tp_rev.child_val FROM $table_revisions tp_rev WHERE ID = c.title AND revs_type = 'categories' AND child_key ='title' AND tp_rev.ID = (SELECT MAX(ID) FROM $table_revisions WHERE ID = tp_rev.ID ) ) AS `cat_name`,
                ( SELECT tp_rev.child_val FROM $table_revisions tp_rev WHERE tp_rev.ID = tp_prod.title  AND revs_type = 'products' AND child_key ='title' AND tp_rev.ID = (SELECT MAX(ID) FROM $table_revisions WHERE ID = tp_rev.ID )  ) AS product_name,
                ( SELECT tp_rev.child_val FROM $table_revisions tp_rev WHERE ID = tp_prod.short_info  AND revs_type = 'products' AND child_key ='short_info' AND tp_rev.ID = (SELECT MAX(ID) FROM $table_revisions WHERE ID = tp_rev.ID ) ) AS `short_info`,
                ( SELECT tp_rev.child_val FROM $table_revisions tp_rev WHERE ID = tp_prod.long_info AND revs_type = 'products' AND child_key ='long_info' AND tp_rev.ID = (SELECT MAX(ID) FROM $table_revisions WHERE ID = tp_rev.ID )  ) AS `long_info`,
                ( SELECT tp_rev.child_val FROM $table_revisions tp_rev WHERE ID = tp_prod.sku   AND revs_type = 'products' AND child_key ='sku' AND tp_rev.ID = (SELECT MAX(ID) FROM $table_revisions WHERE ID = tp_rev.ID ) ) AS `sku`,
                ( SELECT tp_rev.child_val FROM $table_revisions tp_rev WHERE ID = tp_prod.price AND revs_type = 'products' AND child_key ='price' AND tp_rev.ID = (SELECT MAX(ID) FROM $table_revisions WHERE ID = tp_rev.ID )  ) AS `price`,
                ( SELECT tp_rev.child_val FROM $table_revisions tp_rev WHERE ID = tp_prod.weight AND revs_type = 'products' AND child_key ='weight' AND tp_rev.ID = (SELECT MAX(ID) FROM $table_revisions WHERE ID = tp_rev.ID )  ) AS `weight`,
                ( SELECT tp_rev.child_val FROM $table_revisions tp_rev WHERE ID = tp_prod.preview AND revs_type = 'products' AND child_key ='preview' AND tp_rev.ID = (SELECT MAX(ID) FROM $table_revisions WHERE ID = tp_rev.ID )  ) AS `preview`,
                ( SELECT tp_rev.child_val FROM $table_revisions tp_rev WHERE ID = tp_prod.dimension AND revs_type = 'products' AND child_key ='dimension' AND tp_rev.ID = (SELECT MAX(ID) FROM $table_revisions WHERE ID = tp_rev.ID ) ) AS `dimension`,
                ( SELECT tp_rev.child_val FROM $table_revisions tp_rev WHERE ID = tp_prod.dimension AND revs_type = 'products' AND child_key ='dimension' AND tp_rev.ID = (SELECT MAX(ID) FROM $table_revisions WHERE ID = tp_rev.ID ) ) AS `dimension`,
                IF  ( ( SELECT tp_rev.child_val FROM $table_revisions tp_rev WHERE ID = tp_prod.`status` AND revs_type = 'products' AND child_key = 'status' AND tp_rev.ID = ( SELECT MAX(ID) FROM $table_revisions WHERE ID = tp_rev.ID )  ) = 1, 'Active', 'Inactive' ) AS `status`,
                null as discount
            FROM
                $table_product tp_prod
                INNER JOIN $table_revisions tp_rev ON tp_rev.ID = tp_prod.title
                INNER JOIN $table_categories c ON c.ID = tp_prod.ctid  ";

            // Ternary Condition for isset 
            isset($_POST['status']) ? $sts = $_POST['status'] : $sts = NULL  ;
            isset($_POST['stid'])   ? $std = $_POST['stid']   : $std = NULL  ;
            isset($_POST['catid'])  ? $ctd = $_POST['catid']  : $ctd = NULL  ;
            isset($_POST['pid'])  ? $pid = $_POST['pid']  : $pid = NULL  ;

            // Ternary Condition for isset value
            (int)$status = $sts == '0'? NULL:($sts == '2'? '0':'1')  ;
            (int)$catid = $ctd == '0'? '0': $catid = $ctd;
            (int)$stid = $std == '0'? '0': $stid = $std;
            (int)$pdid = $pid == '0'? '0': $pdid = $pid;

            // Status filtering
            if($status != NULL){

                $sql .= " WHERE   ( SELECT tp_rev.child_val FROM $table_revisions tp_rev WHERE ID = tp_prod.`status` AND revs_type = 'products' AND child_key = 'status' AND tp_rev.ID = ( SELECT MAX(ID) FROM $table_revisions WHERE ID = tp_rev.ID )  ) = $status";
                
            }

            // Category filtering
            if ($catid != NULL && $catid != '0') {
                if ($status !== NULL  ) {

                    $sql .= " AND tp_prod.ctid = $catid ";
                }else{
                    $sql .= " WHERE tp_prod.ctid = $catid ";

                }
            }

            // Store ID filtering
            if ($stid != NULL && $stid != '0' ) {
                if ($status !== NULL || $catid != '0') {
                   
                    $sql .= " AND tp_prod.stid = $stid ";
                }else{
                    
                    $sql .= " WHERE tp_prod.stid = $stid ";

                }
            }

            // product filtering
            if ($pdid != NULL && $pdid != '0' ) {
                if ($status !== NULL || $catid != '0' ||  $stid != '0') {
                   
                    $sql .= " AND tp_prod.ID = $pdid ";
                }else{
                    
                    $sql .= " WHERE tp_prod.ID = $pdid ";

                }
            }

            // Uncomment for debugging
            //return $sql;
            
            // Execute query
            $results =  $wpdb->get_results($sql);
            
            foreach ($results as $key => $value) {
                
                $value->discount =  $wpdb->get_row("SELECT
                (SELECT child_val  FROM tp_revisions rev  WHERE child_key = 'discount_name'  AND revs_type = 'products'  AND parent_id = '$value->ID' 	AND ID = ( SELECT max(ID) FROM tp_revisions WHERE child_key = 'discount_name' AND parent_id = '$value->ID' AND revs_type = 'products' ) ) as  `name`,
                (SELECT child_val  FROM tp_revisions rev  WHERE child_key = 'discount_value'  AND revs_type = 'products'  AND parent_id = '$value->ID' 	AND ID = ( SELECT max(ID) FROM tp_revisions WHERE child_key = 'discount_value' AND parent_id = '$value->ID' AND revs_type = 'products' )) as  `value`, 
                (SELECT child_val  FROM tp_revisions rev  WHERE child_key = 'discount_expiry'  AND revs_type = 'products'  AND parent_id = '$value->ID' 	AND ID = ( SELECT max(ID) FROM tp_revisions WHERE child_key = 'discount_expiry' AND parent_id = '$value->ID' AND revs_type = 'products' )) as  `expiry`,
                IF ( (SELECT child_val  FROM tp_revisions rev  WHERE child_key = 'discount_status'  AND revs_type = 'products'  AND parent_id = '$value->ID' 	AND ID = ( SELECT max(ID) FROM tp_revisions WHERE child_key = 'discount_status' AND parent_id = '$value->ID' AND revs_type = 'products' )) = 1 , 'Active', 'Inactive') as  `status` 
                ");

            }

            // return results
            return array(
                "status" => "success",
                "data" => $results,
            );
        }

        public static function validateDate($date, $format = 'Y-m-d h:i:s')
        {
            $d = DateTime::createFromFormat($format, $date);
            return $d && $d->format($format) == $date;
        }

        public static function catch_post(){
            $curl_user = array();

            $curl_user['wpid'] = $_POST['wpid'];
            $curl_user['type'] = $_POST['type'];
            $curl_user['product_id'] = $_POST['pdid'];
            $curl_user['value'] = $_POST['value'];
            $curl_user['expiry'] = $_POST['exp'];
            
            return $curl_user;

        }
        
    }
