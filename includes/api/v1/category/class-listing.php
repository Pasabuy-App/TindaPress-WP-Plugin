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
    
    class TP_Category_Listing {

        public static function listen(){
            
            return rest_ensure_response( 
                TP_Category_Insert:: get_list()
            );
            
        }

        public static function get_list(){

            global $wpdb;


           
        }

    }//end of class