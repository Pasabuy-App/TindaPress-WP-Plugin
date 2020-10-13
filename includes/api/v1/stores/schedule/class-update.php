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
    class TP_Store_Schedule_Update {

        public static function listen(){
            return rest_ensure_response(
                self:: list_open()
            );
        }

        public static function catch_post(){
            $curl_user = array();
            $curl_user['stid'] = $_POST['stid'];
            $curl_user['scid'] = $_POST['scid'];
            $curl_user['type'] = $_POST['type'];
            $curl_user['open'] = $_POST['open'];
            $curl_user['close'] = $_POST['close'];
            $curl_user['wpid'] = $_POST['wpid'];
            return $curl_user;
        }

        public static function list_open(){

            // 2nd Initial QA 2020-08-24 10:57 PM - Miguel
            global $wpdb;

            $table_schedule = TP_SCHEDULE;
            $table_schedule_fields = TP_SCHEDULE_FILEDS;

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
                    "message" => "Please contact your administrator. Verification Issues!",
                );
            }

            if (!isset($_POST['stid'])
                || !isset($_POST['type'])
                || !isset($_POST['close'])
                || !isset($_POST['open'])) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Request unknown!"
                );
            }

            if ($_POST['type'] != "mon"
                && $_POST['type'] != "tue"
                && $_POST['type']  != "wed"
                && $_POST['type'] != "thu"
                && $_POST['type']  != "fri"
                && $_POST['type']  != "sat"
                && $_POST['type']  != "sun"  ) {
                return array(
                    "status" => "failed",
                    "message" => "Invalid value of type."
                );
            }

            $user = self::catch_post();

            $check_store = $wpdb->get_row("SELECT ID FROM $table_schedule WHERE stid = '{$user['stid']}' ");

            if (!empty($check_store)) {
                return array(
                    "status" => "failed",
                    "message" => "This store is already have an schedule."
                );
            }

            // Step 5: Check if store exists
                $store_data = $wpdb->get_row("SELECT child_val as stats FROM tp_revisions WHERE ID = (SELECT `status` FROM tp_stores WHERE ID = '{$user["stid"]}')");

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
                        "message" => "This store is currently inactive.",
                    );
                }
            $wpdb->query("START TRANSACTION");

            if (isset($_POST['type'])) {
                if (!empty($_POST['type'])) {
                    $type = $wpdb->query("UPDATE $table_schedule SET `type` = '{$user["type"]}' WHERE ID = '{$user["sid"]}' ");
                    if ($type == false) {
                        $wpdb->query("ROLLBACK");
                        return array(
                            "status" => "failed",
                            "message" => "An error occured while submitting data to server."
                        );

                    }
                }
            }


            if (isset($_POST['open'])) {
                if (!empty($_POST['open'])) {
                    $open = $wpdb->query("UPDATE $table_schedule SET `open` = '{$user["open"]}' WHERE ID = '{$user["sid"]}' ");
                    if ($open == false) {
                        $wpdb->query("ROLLBACK");
                        return array(
                            "status" => "failed",
                            "message" => "An error occured while submitting data to server."
                        );

                    }
                }
            }

            if (isset($_POST['close'])) {
                if (!empty($_POST['close'])) {
                    $close = $wpdb->query("UPDATE $table_schedule SET `close` = '{$user["open"]}' WHERE ID = '{$user["sid"]}' ");

                    if ($close == false) {
                        $wpdb->query("ROLLBACK");
                        return array(
                            "status" => "failed",
                            "message" => "An error occured while submitting data to server."
                        );

                    }
                }
            }
            $wpdb->query("COMMIT");
            return array(
                "status" => "success",
                "message" => "Data has been added successfully."
            );
        }
    }