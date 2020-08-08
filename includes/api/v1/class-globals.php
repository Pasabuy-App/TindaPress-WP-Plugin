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
        
    }