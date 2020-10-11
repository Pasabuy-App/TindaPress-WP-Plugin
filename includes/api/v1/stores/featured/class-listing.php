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

            // Step 1: Check if prerequisites plugin are missing
            $plugin = TP_Globals::verify_prerequisites();
            if ($plugin !== true) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. ".$plugin." plugin missing!",
                );
            }

			// Step 2: Validate user
			if (DV_Verification::is_verified() == false) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Verification issues!",
                );

            }

            $logo = TP_PLUGIN_URL . "assets/default-avatar.png";
            $banner = TP_PLUGIN_URL . "assets/default-banner.png";

            $sql = "SELECT
                    ID,
                    type,
                    stid,
                    IF(logo is null OR logo = '' , '$logo', logo) as logo,
                    IF(banner is null OR banner = '' , '$banner', banner) as banner,
                    status,
                    date_created
                FROM
                    tp_featured_store WHERE status = 'active'";

            return array(
                "status" => "success",
                "data" =>  $wpdb->get_results($sql)
            );
        }
    }