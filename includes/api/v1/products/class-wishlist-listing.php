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

    class TP_Wishlist_Listing {

        public static function listen(){
            return rest_ensure_response(
                self::listen_open()
            );
        }

        public static function listen_open(){
            global $wpdb;
            $table_wishlist = TP_WISHLIST_TABLE;
            $table_wishlist_fields = TP_WISHLIST_FIELDS;
            $table_revs = TP_REVISIONS_TABLE;
            $table_product = TP_PRODUCT_TABLE;


            isset($_POST['pdid'])? $product_id = $_POST['pdid']: $product_id = NULL;


            $wpid = $_POST['wpid'];

            $sql = "SELECT
                *
            FROM
                $table_wishlist
            WHERE
                created_by = $wpid
            ";

            if (isset($_POST['pdid'])) {
                if ($product_id != null) {
                    $sql .= " AND product_id = $product_id ";
                }
            }


        }
    }