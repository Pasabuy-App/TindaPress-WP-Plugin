<?php
	// Exit if accessed directly
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	/**
        * @package tindapress-wp-plugin
		* @version 0.2.0
		* This is the primary gateway of all the rest api request.
    */

  	class TP_Globals_v2 {

        /**
		 * GENERATING PUBLICKEY
		 * @param primary_key = primary key
		 * @param table_name = table name
		 * @param column_name = Column name to be updated
		 */
		public static function generating_pubkey($primary_key, $table_name, $column_name, $get_key, $lenght){
            global $wpdb;

            $sql = "UPDATE  $table_name SET $column_name = concat(
                substring('abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ', rand(@seed:=round(rand($primary_key)*4294967296))*36+1, 1), ";

            for ($i=0; $i < $lenght ; $i++) {
                $sql .= "substring('abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ', rand(@seed:=round(rand(@seed)*4294967296))*36+1, 1),";
            }

            $sql .=" substring('abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ', rand(@seed)*36+1, 1)
            )
            WHERE ID = $primary_key;";


            $results = $wpdb->query($sql);

            if ($get_key = true) {
                $key  = $wpdb->get_row("SELECT `$column_name` as `key` FROM $table_name WHERE ID = '$primary_key' ");
                return $key->key;
            }

            if ($results < 1) {
				return false;
			}else{
				if ($results == 1) {
					return true;
				}
			}
        }

        public static function check_listener($array_post){
			$var = array();
			$keys = array();

			foreach ($array_post as $key => $value) {
				$var[] = $value;
				$keys[] = $key;
			}

			for ($count=0; $count < count($var) ; $count++) {
				if (empty($var[$count])) {
					return $keys[$count];
				}
			}
			return true;

		}
    }