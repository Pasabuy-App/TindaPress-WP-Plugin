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
    class TP_Store_Schedule_Insert {

        public static function listen(){
            return rest_ensure_response(
                self:: list_open()
            );
        }

        public static function catch_post(){
            $curl_user = array();
            $curl_user['stid'] = $_POST['stid'];
            $curl_user['mon'] = $_POST['mon'];
            $curl_user['tues'] = $_POST['tues'];
            $curl_user['wed'] = $_POST['wed'];
            $curl_user['thur'] = $_POST['thur'];
            $curl_user['fri'] = $_POST['fri'];
            $curl_user['sat'] = $_POST['sat'];
            $curl_user['sun'] = $_POST['sun'];
            $curl_user['wpid'] = $_POST['wpid'];
            return $curl_user;
        }

        public static function list_open(){

            // 2nd Initial QA 2020-08-24 10:57 PM - Miguel
            global $wpdb;

            $table_schedule = TP_SCHEDULE;
            $table_schedule_fields = TP_SCHEDULE_FILEDS;

            if (!isset($_POST['stid'])
                || !isset($_POST['mon'])
                || !isset($_POST['tues'])
                || !isset($_POST['tues'])
                || !isset($_POST['wed'])
                || !isset($_POST['thur'])
                || !isset($_POST['fri'])
                || !isset($_POST['sat'])
                || !isset($_POST['sun']) ) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Request unknown!"
                );
            }

            if ($_POST['mon'] != "1" && $_POST['mon']   != "0"
            || $_POST['tues'] != "1" && $_POST['tues']  != "0"
            || $_POST['wed']  != "1" && $_POST['wed']    != "0"
            || $_POST['thur'] != "1" && $_POST['thur']  != "0"
            || $_POST['fri']  != "1" && $_POST['fri']    != "0"
            || $_POST['sat']  != "1" && $_POST['sat']    != "0"
            || $_POST['sun']  != "1" && $_POST['sun']    != "0" ) {
                return array(
                    "status" => "failed",
                    "message" => "Required fields value must be 1 or 0 only."
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

            $data = $wpdb->query("INSERT INTO
                $table_schedule
                    ($table_schedule_fields)
                VALUES
                    ('{$user['stid']}',
                    '{$user['mon']}',
                    '{$user['tues']}',
                    '{$user['wed']}',
                    '{$user['thur']}',
                    '{$user['fri']}',
                    '{$user['sat']}',
                    '{$user['sun']}',
                    '{$user['wpid']}'
                    ) ");

            if ($data == false) {
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