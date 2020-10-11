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

    class TP_Featured_Store_Listing {

        public static function listen(){
            return rest_ensure_response(
                self::list_open()
            );
        }

        public static function catch_post(){
            $curl_user = array();
            $curl_user['store_id'] = $_POST['stid'];
            $curl_user['type'] = $_POST['type'];
            return $curl_user;
        }

        public static function list_open(){
            global $wpdb;


            return $wpdb->get_results("SELECT * FROM tp_featured_store");



        }
    }