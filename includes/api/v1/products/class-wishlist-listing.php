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

    class TP_Product_Wishlist_Listing {

        public static function listen(){
            return rest_ensure_response(
                self:: listen_open()
            );
        }

        //QA done 2020-08-12 11:01 am
        public static function listen_open(){
            global $wpdb;
            $table_wishlist = TP_WISHLIST_TABLE;
            $table_wishlist_fields = TP_WISHLIST_FIELDS;

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
                    "message" => "Please contact your administrator. verification issues!",
                );

            }

            if (!isset($_POST['pdid'])) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Reuqest unknown!"
                );
            }

            if (empty($_POST['pdid'])) {
                return array(
                    "status" => "failed",
                    "message" => "Required fields cannot be empty."
                );
            }

            $product_id = $_POST['pdid'];
            $created_by = $_POST['wpid'];

            $sql ="SELECT
                *
            FROM
                $table_wishlist
            WHERE
                product_id = $product_id
                AND created_by = $created_by
            ";


            $data = $wpdb->get_results($sql);

            return array(
                "status" => "success",
                "data" => $data
            );
        }
    }