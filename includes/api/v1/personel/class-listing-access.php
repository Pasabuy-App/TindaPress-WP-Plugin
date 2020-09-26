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

    class TP_Listing_Access {

        //REST API Call
        public static function listen(){

            return rest_ensure_response(
                self::listen_open()
            );
        }

        //QA Done 2020-08-12 4:10 pm
        public static function listen_open(){
            global $wpdb;


            $plugin = TP_Globals::verify_prerequisites();
            if ($plugin !== true) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. ".$plugin." plugin missing!",
                );
            }

            // Step2 : Check if wpid and snky is valid
            if (DV_Verification::is_verified() == false) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Verification Issues!",
                );
            }

            if (!isset($_POST['roid'])) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Request unknown!",
                );
            }

            if (empty($_POST['roid'])) {
                return array(
                    "status" => "failed",
                    "message" => "Required fileds cannot be empty.",
                );
            }

            $roid = $_POST['roid'];

            $access_id = $wpdb->get_results("SELECT * FROM tp_roles_meta WHERE roid = '$roid'");
            if (empty($access_id)) {
                return array(
                    "status" => "failed",
                    "message" => "This role id does not exists."
                );
            }

            $access = array();
            $access_value = array();

            for ($i=0; $i < COUNT($access_id) ; $i++) {
                $id = $access_id[$i]->access;
                $access[] = $wpdb->get_row("SELECT `access` FROM tp_access WHERE ID = '$id' ");
            }
           /*  foreach ($access as $key => $value) {
                $access_value[]["access"] = $value->access;
            } */
            return array(
                "status" => "success",
                "data" =>  $access
            );
        }
    }