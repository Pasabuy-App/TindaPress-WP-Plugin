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
    class TP_Category_Delete {
        public static function listen(){
            global $wpdb;
            return 'test';

            
        }
        
        // Catch Post 
        public static function catch_post()
        {
            $cur_user = array();
               
            $cur_user['created_by'] = $_POST["wpid"];
            $cur_user['store_id']      = $_POST["stid"];
  
            return  $cur_user;
        }

    }