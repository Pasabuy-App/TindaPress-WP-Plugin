<?php
	// Exit if accessed directly
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	/**
        * @package tindapress-wp-plugin
		* @version 0.1.0
		* This is the primary gateway of all the rest api request.
    */

		// Hardening QA 12:05 8/31/2020
        // Miguel Igdalino

  	class TP_Globals {

        public static function create($table_name, $data){
            global $wpdb;

            return $wpdb->insert($table_name, $data);

        }

        public static function delete($table_name , $id){
            global $wpdb;

            return $wpdb->delete( $table_name, array( 'id' => $id ) );

        }

        public static function update($table_name, $id, $fields){
            global $wpdb;

            return $wpdb->update( $table_name , $fields, array('id' => $id) );
        }


        public static function date_stamp(){
            date_default_timezone_set('Asia/Manila');

            return date("Y-m-d h:i:s");
        }

        public static function check_roles($role){

            $wp_user = get_userdata($_POST['wpid']);

            if ( in_array($role , $wp_user->roles, true) ) {
                return true;
            }

            return false;
        }

        public static function verify_prerequisites(){

            if(!class_exists('DV_Verification') ){
                return 'DataVice';
            }

            return true;
        }

        public static function verify_role($wpid, $store_id, $role){
            global $wpdb;

            if ($store_id == 0) {
                //Check if personnel is part of the store
                 $personnels = $wpdb->get_row("SELECT `wpid`, `roid`
                    FROM `tp_personnels`
                    WHERE `wpid` = $wpid");
            }else{
                //Check if personnel is part of the store
                $personnels = $wpdb->get_row("SELECT `wpid`, `roid`
                    FROM `tp_personnels`
                    WHERE `stid` = $store_id
                    AND `wpid` = $wpid");
            }

            if (!$personnels) {
                return false;
            }

            $role_id = $personnels->roid;

            //Get all access from that role_id
            $get_access = $wpdb->get_results("SELECT rm.access
                FROM `tp_roles` r
                    LEFT JOIN tp_roles_meta rm ON rm.roid = r.ID
                WHERE r.id = $role_id");

             $access = array_column($get_access, 'access');

            //Check if user has permitted role access or one of our staff
            if ( !in_array($role , $access, true) || DV_Globals::check_roles('editor') === true
            || DV_Globals::check_roles('contributor') === true || DV_Globals::check_roles('administrator') === true || DV_Globals::check_roles('Author') === true ) {
                return true;
            }
            return false;

        }

        public static function get_timezone($wpid){
            global $wpdb;

            $result = $wpdb->get_row("SELECT
                (SELECT tzone_name FROM dv_geo_timezone WHERE country_code =   (SELECT country_code FROM dv_geo_countries WHERE ID =  (SELECT child_val FROM dv_revisions WHERE child_key = 'country' AND ID = dv_address.country  ))) as time_zone
            FROM
                dv_address
            WHERE
                wpid = $wpid");

            if (! $result  ) {
                return false;

            }else{
                return $result;

            }
        }

        public static function get_user_date($wpid){
            global $wpdb;
            $user_timezone = TP_Globals::get_timezone($wpid);
            date_default_timezone_set($user_timezone->time_zone);
            return date("Y-m-d H:i:s");

        }

        public static function convert_date($wpid, $date){
            global $wpdb;
            $user_timezone = TP_Globals::get_timezone($wpid);
            date_default_timezone_set($user_timezone->time_zone);

            return date('Y-m-d H:i:s', strtotime($date));
        }

        public static function wp_admin_url() {
            return site_url() . "/wp-admin/admin.php?page=";
        }

        public static function custom_update($parent_id, $wpid, $rev_type, $parent_table, $revisions_table, $data, $where){

            global $wpdb;

            $date = DV_Globals:: date_stamp();

            if ( ! is_array( $data ) || ! is_array( $where ) ) {
                return false;
            }

            //Initialize empty array
            $fields     = array();
            $insert_fields = array();
            $insert_values = array();
            $conditions = array();
            $values     = array();

            //Remove null data
            foreach ( $data as $field => $value ) {
                if ( is_null( $value ) ) {
                    unset($data[$field]);
                    continue;
                }
            }
            $wpdb->query("START TRANSACTION");
            //Insert into revisions table
            foreach ($data as $key => $value) {
                $insert_result = $wpdb->query("INSERT INTO $revisions_table (`revs_type`, `parent_id`, `child_key`, `child_val`, `created_by`, `date_created`) VALUES ('$rev_type', '$parent_id', '$key', '$value', '$wpid', '$date')");
                if ($insert_result < 1) {
                    $wpdb->query("ROLLBACK");
                    return false;
                }
                $insert_values[$key] = $wpdb->insert_id;
            }

            //Get all `where` conditions
            foreach ( $where as $field => $value ) {
                if ( is_null( $value ) ) {
                    $conditions[] = "`$field` IS NULL";
                    continue;
                }

                $conditions[] = "`$field` = " . $value;
            }

            //Make fields a comma seperated values
            $conditions = implode( ' AND ', $conditions );

            foreach ($insert_values as $key => $value) {
                $result = $wpdb->query("UPDATE $parent_table SET $key = $value WHERE `ID` = $parent_id");
                if ($result < 1) {
                    $wpdb->query("ROLLBACK");
                    return false;
                }
            }

            $wpdb->query("COMMIT");
            return true;

        }

        public static function get_product($catid, $stid){
            global $wpdb;
            $table_revisions = TP_REVISIONS_TABLE;
            $table_product = TP_PRODUCT_TABLE;
            $table_categories = TP_CATEGORIES_TABLE;
            $table_variants = TP_VARIANTS_TABLE;

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
                INNER JOIN $table_categories c ON c.ID = tp_prod.ctid
            WHERE   ( SELECT tp_rev.child_val FROM $table_revisions tp_rev WHERE ID = tp_prod.`status` AND revs_type = 'products' AND child_key = 'status' AND tp_rev.ID = ( SELECT MAX(ID) FROM $table_revisions WHERE ID = tp_rev.ID )  ) = 1
                AND tp_prod.ctid = '$catid'
                AND  tp_prod.stid = '$stid'
            ";


            $results =  $wpdb->get_results($sql);

            foreach ($results as $key => $value) {

                if($value->preview == null || $value->preview == 'None' ){
                    $value->preview =  TP_PLUGIN_URL . "assets/images/default-product.png" ;
                }

                $get_discount =  $wpdb->get_row("SELECT
                (SELECT child_val  FROM tp_revisions rev  WHERE child_key = 'discount_name'  AND revs_type = 'products'  AND parent_id = '$value->ID' 	AND ID = ( SELECT max(ID) FROM tp_revisions WHERE child_key = 'discount_name' AND parent_id = '$value->ID' AND revs_type = 'products' )) as  `name`,
                (SELECT child_val  FROM tp_revisions rev  WHERE child_key = 'discount_value'  AND revs_type = 'products'  AND parent_id = '$value->ID' 	AND ID = ( SELECT max(ID) FROM tp_revisions WHERE child_key = 'discount_value' AND parent_id = '$value->ID' AND revs_type = 'products' )) as  `value`,
                (SELECT child_val  FROM tp_revisions rev  WHERE child_key = 'discount_expiry'  AND revs_type = 'products'  AND parent_id = '$value->ID' 	AND ID = ( SELECT max(ID) FROM tp_revisions WHERE child_key = 'discount_expiry' AND parent_id = '$value->ID' AND revs_type = 'products' )) as  `expiry`,
                IF ( (SELECT child_val  FROM tp_revisions rev  WHERE child_key = 'discount_status'  AND revs_type = 'products'  AND parent_id = '$value->ID' 	AND ID = ( SELECT max(ID) FROM tp_revisions WHERE child_key = 'discount_status' AND parent_id = '$value->ID' AND revs_type = 'products' )) = 1 , 'Active', 'Inactive') as  `status`
                ");
                if ($get_discount->name == null) {
                    $get_discount->name = '';
                }
                if ($get_discount->value == null) {
                    $get_discount->value = '';
                }

                if ($get_discount->expiry == null) {
                    $get_discount->expiry = '';
                }

                $value->discount = $get_discount;

            }

            return $results;
        }
    }