<?php
	// Exit if accessed directly
	if ( ! defined( 'ABSPATH' ) )
	{
		exit;
	}

	/**
        * @package tindapress-wp-plugin
        * @version 0.2.0
	*/

    class TP_Store_Insert_v2 {

        //REST API Call
        public static function listen(){

            return rest_ensure_response(
                self::listen_open()
            );
        }

        public static function listen_open(){

            global $wpdb;


        }
    }