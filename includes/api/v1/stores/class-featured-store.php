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

    class TP_Delete_Store {

        public static function listen(){
            return rest_ensure_response(
                TP_Delete_Store::list_open()
            );
        }

        // 2nd Initial QA 2020-08-24 10:38 PM - Miguel
        //QA done 2020-08-12 10:18 pm
        public static function list_open(){

            global $wpdb;
        }
    }