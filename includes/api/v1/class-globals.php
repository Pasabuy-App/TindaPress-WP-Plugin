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
?>
<?php
  	class TP_Globals {
         
        public static function create($table_name, $data){
            global $wpdb;
        
            return $wpdb->insert($table_name, $data);
                       
        }
        
        // NOTE: unfinished
        public static function retrieve($table_name, $fields, $sort_field, $sort){
            global $wpdb;
            // fields
            $data = implode( ', ', $fields );
            
            // sort_fields
            $str_sortFiled = implode( ', ', $sort_field );
            $sorted_field = preg_replace('/[0-9,]+/', '', $str_sortFiled);
            
            // sort
            $sorted = implode( ', ', $sort );
            // $sorts = preg_replace('/[0-9,]+/', '', $str_sort);


            return $wpdb->get_results("SELECT $data FROM $table_name $sorted_field $sorted ");
        }

        public static function delete($table_name , $id){
            global $wpdb;
        
            return $wpdb->delete( $table_name, array( 'id' => $id ) );

        }

        public static function update($table_name, $id, $fields){
            global $wpdb;
            
            return $wpdb->update( $table_name , $fields, array('id' => $id) );
        }

        // date stamp 
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

        public static function verify_datavice_plugin(){
            if(!class_exists('DV_Verification') ){
                return false;
            }else{
                return true;
            }
        }

        public static function verify_role($wpid, $store_id, $role){
            global $wpdb;

            if ($store_id === 0) {
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

            
                
            //Check if current user is one of the personnels or one of our staff
            if (!$personnels || (DV_Globals::check_roles('subscriber') == true) ) {
                return array(
                    "status" => "failed",
                    "message" => "User not associated with this store",
                );
            }

            $role_id = $personnels->roid;

            //Get all access from that role_id 
            $get_access = $wpdb->get_results("SELECT rm.access
                FROM `tp_roles` r 
                    LEFT JOIN tp_roles_meta rm ON rm.roid = r.ID
                WHERE r.id = $role_id");
                
            $access = array_column($get_access, 'access');

            //Check if user has role access of `can_delete_contact` or one of our staff
            if ( !in_array($role , $access, true) || (DV_Globals::check_roles('subscriber') == true) ) {
                return array(
                    "status" => "failed",
                    "message" => "Current user has no access in inserting contacts",
                );
            }

        }

        public static function get_timezone($wpid){
            global $wpdb;

            $result = $wpdb->get_row("SELECT
                (SELECT tzone_name FROM dv_geo_timezone WHERE country_code =   (SELECT country_code FROM dv_geo_countries WHERE ID = dv_address.country)) as time_zone
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
            return date("Y-m-d h:i:s");
        }
        
    }