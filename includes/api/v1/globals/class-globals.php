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

        /**
         * Not working 
         
            *public static function retrieveById($table_name, $fields, $id){
            *    global $wpdb;
            *    $data = implode( ', ', $fields );
            *    return $data;
            *    // return $wpdb->get_results("SELECT $data FROM $table_name WHERE id = $id ");

            *}
         */

        public static function delete($table_name , $id){
            global $wpdb;
        
            return $wpdb->delete( $table_name, array( 'id' => $id ) );

        }

        public static function update($table_name, $id, $fields){
            global $wpdb;
            
            return $wpdb->update( $table_name , $fields, array('id' => $id) );
        }


        public static function validate_user(){
            $verified = DV_Verification::initialize();
            //Convert object to array
            $array =  (array) $verified;
            // Pass request status in a variable
            $response =  $array['data']['status'];
            if ($response != 'success') {
                    return $verified;
            } else {
                    return true;
            }
        }

        // date stamp 
        public static function date_stamp(){
            date_default_timezone_set('Asia/Manila');

            
            return date("Y-m-d h:i:sa");

        }
        
    }
?>