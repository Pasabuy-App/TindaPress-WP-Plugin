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

    class TP_Product_Update {

        public static function listen(){
            return rest_ensure_response(
                self::listen_open()
            );
        }

        public static function listen_open(){
            global $wpdb;

            if (!isset($_POST['pdid'])) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Verification issues!",
                );
            }

            if (empty($_POST['pdid'])) {
                return array(
                    "status" => "failed",
                    "message" => "Required fields cannot be empty.",
                );
            }


            $product_id = $_POST['pdid'];
            $wpid = $_POST['wpid'];


            $data = $wpdb->get_row("SELECT * FROM ");


        }
    }