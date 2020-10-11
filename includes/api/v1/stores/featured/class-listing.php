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
                    ( SELECT rev.child_val FROM tp_revisions rev WHERE rev.id = (SELECT title FROM tp_stores WHERE ID = p.stid) AND  rev.date_created = ( SELECT MAX(date_created) FROM tp_revisions tp_rev WHERE tp_rev.ID = rev.ID AND revs_type = 'stores' )  ) AS title,
                    IF(logo is null OR logo = '' , '$logo', logo) as avatar,
                    IF(banner is null OR banner = '' , '$banner', banner) as banner,
                    status,
                    date_created
                FROM
                    tp_featured_store p WHERE status = 'active'";

            $data = $wpdb->get_results($sql);

            foreach ($data as $key => $value) {
               if (!empty($data)) {
                    $seen = TP_Globals::seen_store($_POST['wpid'], $value->ID );
                    if($seen == 'error'){
                        return array(
                            "status" => "failed",
                            "message" => "Please contact your administrator. Seen error"
                        );
                    }
               }
            }

            return array(
                "status" => "success",
                "data" => $data
            );
        }
    }