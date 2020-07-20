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

        public static function retrieve($table_name, $fields){
            return "delete";
        }

        public static function retrieveById($table_name, $fields, $id){
            return "delete";
        }

        public static function delete($table_name , $id){
            return "delete";

            $wpdb->get_row("UPDATE products set status = 0 where id = $id")
        }

        public static function update($table_name, $id){
            return "update";

            
        }
        
    }
?>