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
    
    class TP_Category_Insert {
        public static function listen(){
            global $wpdb;
            return rest_ensure_response( 
                TP_Category_Insert:: get_list()
            );

            
        }
        
        // Catch Post 
        public static function get_list()
        {
            $cur_user = array();
               
            $cur_user['created_by'] = $_POST["wpid"];
            $cur_user['store_id']      = $_POST["stid"];
  
            return  $cur_user;
        }

    }