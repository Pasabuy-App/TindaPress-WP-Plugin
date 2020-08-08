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

    class TP_Category_Insert {

        //REST API Call
        public static function listen(){
            return 'test';
            return rest_ensure_response( 
                TP_Category_Insert:: insert_category()
            );
        }
        
        
        //Inserting Category function
        public static function insert_category(){
            
            global $wpdb;

            //  Step1 : Verify if Datavice Plugin is Active
			if (TP_Globals::verifiy_datavice_plugin() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Plugin Missing!",
                    )
                );
			}
			
			//  Step2 : Validate if user is exist
			if (TP_Globals::validate_user() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Request Unknown!",
                    )
                );
            }

            


        }

    }