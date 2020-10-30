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

    class TP_Listing_Personnel {

        //REST API Call
        public static function listen(){

            return rest_ensure_response(
                self::listen_open()
            );
        }

        //QA Done 2020-08-12 4:10 pm
        public static function listen_open(){
            global $wpdb;


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
                    "message" => "Please contact your administrator. Verification Issues!",
                );
            }

            /**
             * @param status = active, inactive
             * @param user_id = wpid of user
             * @param stid = personels of store
             * @param plid = personel id
             */

            isset($_POST['status'])? $status = $_POST['status'] : ((isset($_POST['status']) && $_POST['status'] != null)? $status = $_POST['status'] : $status = null);

            isset($_POST['user_id'])?  $user_id = $_POST['user_id'] : ((isset($_POST['user_id']) && $_POST['user_id'] != null)? $user_id = $_POST['user_id'] : $user_id = null);

            isset($_POST['stid'])? $stid = $_POST['stid'] : ((isset($_POST['stid']) && $_POST['stid'] != null)? $stid = $_POST['stid'] : $stid = null);

            isset($_POST['plid'])?  $plid = $_POST['plid'] : ((isset($_POST['plid']) && $_POST['plid'] != null)? $plid = $_POST['plid'] : $plid = null);

            $sql = "SELECT
                `hash_id` as ID,
                `stid`,
                `wpid`,
                null as avatar,
                null as `dname`,
                `status`,
                `date_created`
            FROM
                tp_personnels
            ";

            if ($status != null) {
                if ($status != "active" && $status != "inactive") {
                    return array(
                        "status" => "failed",
                        "message" => "Invalid value of status."
                    );
                }
                $sql .= " WHERE `status` = '$status' ";
            }

            if ($user_id != null) {
                if ($status != null) {
                    $sql .= " AND wpid = '$user_id' ";
                }else{
                    $sql .= " WHERE wpid = '$user_id' ";
                }
            }

            if ($stid != null) {
                if ($status != null || $user_id != null) {
                    $sql .= " AND stid = '$stid' ";
                }else{
                    $sql .= " WHERE stid = '$stid' ";
                }
            }

            if ($plid != null) {

                if ($status != null || $user_id != null || $stid != null) {
                    $sql .= " AND hash_id = '$plid' ";
                }else{
                    $sql .= " WHERE hash_id = '$plid' ";
                }
            }

            $get_data = $wpdb->get_results($sql);

            foreach ($get_data as $key => $value) {
                $wp_user = get_user_by("ID", $value->wpid);

                $value->avatar = $wp_user->avatar != null? $wp_user->avatar: $wp_user->avatar= TP_PLUGIN_URL. "assets/images/default-avatar.png" ;

                $value->dname = $wp_user->display_name;
            }

            return array(
                "status" => "success",
                "data" => $get_data
            );
        }
    }