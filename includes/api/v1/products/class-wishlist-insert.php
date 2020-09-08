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

    class TP_Product_Wishlist {

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
            $date = TP_Globals::date_stamp();

            $wpdb->query("START TRANSACTION");

            // Check first if user has already wishlist
            $wish_data = $wpdb->get_row("SELECT * FROM $table_wishlist WHERE product_id = $product_id AND created_by = $created_by ");


            if (empty($wish_data)) {
                $wish_1075 = $wpdb->query($wpdb->prepare("INSERT INTO $table_wishlist ($table_wishlist_fields) VALUES (%d, '%s', %d, '%s')",$product_id, '1', $created_by, $date ) );
                $id = $wpdb->insert_id;
                $wpdb->query("UPDATE $table_wishlist SET hash_id = sha2($id, 256) WHERE ID = $id");
            }else{

                switch ($wish_data->status) {

                    // Update wishlist to 0 which is active
                    case '1':
                        $wish_1075 = $wpdb->query("UPDATE $table_wishlist SET `status` = '0' WHERE ID = $wish_data->ID AND created_by = $created_by AND product_id = $product_id  ");

                        break;
                    // Update wishlist to 1 which is active
                    case '0':
                        $wish_1075 = $wpdb->query("UPDATE $table_wishlist SET `status` = '1' WHERE ID = $wish_data->ID AND created_by = $created_by AND product_id = $product_id  ");

                        break;
                }

            }

            if ($wish_1075 == false) {
                $wpdb->query("ROLLBACK");

                return array(
                    "status" => "failed",
                    "message" => "An error occured while submitting data to server."
                );
            }else{
                $wpdb->query("COMMIT");
                return array(
                    "status" => "success",
                    "message" => "Data has been successfully added."
                );
            }
        }
    }