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

    //QA done 2020-08-12 4:28pm
    class TP_Product_Store_Active {

        public static function listen(){
            return rest_ensure_response( 
                TP_Product_Store_Active:: listen_open()
            );
        }

        public static function listen_open(){
        }
    }