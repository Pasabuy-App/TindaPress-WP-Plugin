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
            if ( !in_array($role , $access, true) || DV_Globals::check_roles('editor') === true || DV_Globals::check_roles('contributor') === true || DV_Globals::check_roles('administrator') === true || DV_Globals::check_roles('Author') === true ) {
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
    }