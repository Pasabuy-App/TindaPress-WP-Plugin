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

    class TP_Wishlist_Insert {

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

            // Validating product

               $get_product = $wpdb->get_row("SELECT
                    tp_prod.ID, tp_prod.ctid, tp_prod.status as status_id,
                    ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE tp_rev.ID = tp_prod.title ) AS product_name,
                    ( SELECT tp_rev.child_val FROM $table_revs tp_rev WHERE ID = tp_prod.status ) AS `status`
                FROM
                    $table_product tp_prod
                INNER JOIN
                    $table_revs tp_rev ON tp_rev.ID = tp_prod.title
                WHERE
                    tp_prod.ID = $product_id
                GROUP BY
                    tp_prod.ID
                ");

                //Check if no rows found
                if (!$get_product) {
                    return array(
                        "status" => "failed",
                        "message" => "This product does not exists",
                    );
                }

                //Fails if product is currently inactive
                if ($get_product->status == 0) {
                    return array(
                        "status" => "failed",
                        "message" => "This product is currently inactive.",
                    );
                }

            // End validating product


            $data = $wpdb->get_row("SELECT * FROM $table_wishlist WHERE product_id = $product_id AND created_by = $wpid ");

            if (empty($data)) {
                $wish_1075 = $wpdb->query($wpdb->prepare("INSERT INTO $table_wishlist ($table_wishlist_fields) VALUES (%d, '%s', %d)", $product_id, '1', $wpid));
                $id = $wpdb->insert_id;

                $wpdb->query("UPDATE $table_wishlist SET hash_id = sha2($id, 256) WHERE ID = $id");

            }else{

                switch ($data->status) {
                    case '1':
                        $wish_1075 = $wpdb->query("UPDATE $table_wishlist SET `status` = '0' WHERE product_id = $product_id AND created_by = $wpid");

                        break;

                    case '0':
                        $wish_1075 = $wpdb->query("UPDATE $table_wishlist SET `status` = '1' WHERE product_id = $product_id AND created_by = $wpid");

                        break;
                }
            }


            if ($wish_1075 == false) {
                return array(
                    "status" => "failed",
                    "message" => "An error occured while submitting data to server."
                );
            }else{
                return array(
                    "status" => "success",
                    "message" => "Data has been added successfully."
                );
            }
        }
    }