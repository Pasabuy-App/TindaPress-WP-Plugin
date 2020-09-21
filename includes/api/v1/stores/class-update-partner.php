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
    class TP_Update_Partner {

        public static function listen(){
            return rest_ensure_response(
                self:: list_open()
            );
        }

        public static function list_open(){
            global $wpdb;

            $date_created = TP_Globals::date_stamp();

            // Step1 : Check if prerequisites plugin are missing
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
                    "message" => "Please contact your administrator. Verification issues!",
                );
            }

            if (!isset($_POST['stid']) || !isset($_POST['partner'])  ) {
                return array(
                    "status" => "unknwon",
                    "message" => "Please contact your administrator. Unknown response."
                );
            }

            if (empty($_POST['stid']) || empty($_POST['partner'])) {
                return array(
                    "status" => "failed",
                    "message" => "Required fields cannot be empty."
                );
            }

            $store_id = $_POST['stid'];
            $partner = $_POST['partner'];

            $store_data = $wpdb->get_row("SELECT child_val as stats FROM tp_revisions WHERE ID = (SELECT `status` FROM tp_stores WHERE ID = '$store_id' ) AND child_key = 'status' AND revs_type = 'stores' ");

            // Check if no rows found
            if (!$store_data) {
                return array(
                    "status" => "failed",
                    "message" => "This store does not exists.",
                );
            }

            //Fails if already activated
            if ($store_data->stats == 0) {
                return array(
                    "status" => "failed",
                    "message" => "This store is already activated.",
                );
            }

            $wpdb->query("START TRANSACTION");

            $get_data = $wpdb->get_row("SELECT child_key, child_val FROM tp_revisions WHERE parent_id = $store_id AND revs_type = 'stores' AND child_key = 'isPartner' ");
            if ($get_data !== null) {
                if ($get_data->child_val == $partner) {
                    return array(
                        "status" => "failed",
                        "message" => "Commission is already set to ".$partner."."
                    );
                }
            }



            if ($get_data == NULL) {

                $insert_data = $wpdb->query(" INSERT INTO tp_revisions ( revs_type, parent_id, child_key, child_val, date_created ) VALUES ( 'stores', '$store_id', 'isPartner', '$partner', '$date_created' ) ");

                if ($insert_data < 1) {
                    $wpdb->query("ROLLBACK");
                    return array(
                        "status" => "failed",
                        "message" => "An error occured while submitting data to server."
                    );
                }else{
                    $wpdb->query("COMMIT");
                    return array(
                        "status" => "success",
                        "message" => "Data has been successfully subbmmited."
                    );
                }

            }else{

                $insert_data = $wpdb->query(" UPDATE tp_revisions SET child_val = '$partner' WHERE parent_id = $store_id AND revs_type ='stores' AND child_key = 'isPartner' ");

                if ($insert_data === false) {
                    $wpdb->query("ROLLBACK");
                    return array(
                        "status" => "failed",
                        "message" => "An error occured while submitting data to server."
                    );
                }else{
                    $wpdb->query("COMMIT");
                    return array(
                        "status" => "success",
                        "message" => "Data has been successfully submitted."
                    );
                }
            }
        }
    }