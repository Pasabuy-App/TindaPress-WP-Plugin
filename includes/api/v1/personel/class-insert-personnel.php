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

    class TP_Insert_Personnel {

        //REST API Call
        public static function listen(){

            return rest_ensure_response(
                self::listen_open()
            );
        }

        public static function catch_post(){
            $curl_user = array();
            $curl_user['user_id'] = $_POST['user_id'];
            $curl_user['created_by'] = $_POST['wpid'];
            $curl_user['store_id'] = $_POST['stid'];
            $curl_user['pincode'] = $_POST['pincode'];
            $curl_user['role_id'] = $_POST['roid'];
            return $curl_user;
        }

        //QA Done 2020-08-12 4:10 pm
        public static function listen_open(){
            global $wpdb;

            $table_role = TP_ROLES_TABLE;
            $table_revisions = TP_REVISIONS_TABLE;
            $table_store = TP_STORES_TABLE;
            $date = TP_Globals::date_stamp();

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
                    "message" => "Please contact your administrator. verification issues!",
                );

            }

            if (!isset($_POST['roid']) || !isset($_POST['stid']) || !isset($_POST['pincode']) ||  !isset($_POST['user_id'])) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Request unknown!"
                );
            }

            if (empty($_POST['roid']) || empty($_POST['stid']) || empty($_POST['pincode']) ||  empty($_POST['user_id']) ) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Request unknown!"
                );
            }

            $user = self::catch_post();

            if (strlen($user['pincode']) != 4) {
                return array(
                    "status" => "failed",
                    "message" => "Pincode must have 4 characters only."
                );
            }


            if (!is_numeric($user['pincode'])) {
                return array(
                    "status" => "failed",
                    "message" => "Pincode must be numbers only."
                );
            }
               // Verifying Store
            $get_store = $wpdb->get_row("SELECT
               tp_str.ID,
               ( SELECT tp_rev.child_val FROM $table_revisions tp_rev WHERE ID = tp_str.status ) AS `status`
               FROM
                   $table_store tp_str
               INNER JOIN
                   $table_revisions tp_rev ON tp_rev.ID = tp_str.`status`
               WHERE tp_str.ID = '{$user["store_id"]}'
           ");

            // Check if no rows found
                if ( !$get_store ) {
                    return rest_ensure_response(
                        array(
                            "status" => "failed",
                            "message" => "This store does not exists.",
                        )
                    );
                }

                // Check if status = 0
                if ( $get_store->status == 0 ) {
                    return array(
                        "status" => "failed",
                        "message" => "This store is currently deactivated.",
                    );
                }
            // End verifying Store

            $check_role = $wpdb->get_row("SELECT
                (SELECT
                    child_val
                FROM
                    tp_revisions rev
                WHERE
                    parent_id = r.ID
                    AND revs_type = 'roles'
                    AND child_key = 'status'
                    AND ID = (SELECT MAX(ID) FROM tp_revisions WHERE ID = rev.ID AND revs_type = 'roles' AND child_key = 'status' ))as `status`  FROM tp_roles r WHERE ID = '{$user["role_id"]}' ");

            if (!$check_role) {
                return array(
                    "status" => "failed",
                    "message" => "This role does not exists."
                );
            }


            if ($check_role->status == '0') {
                return array(
                    "status" => "failed",
                    "message" => "This role is currently inactive."
                );
            }


            $check_personnel = $wpdb->get_results("SELECT * FROM tp_personnels WHERE wpid = '{$user["user_id"]}'");

            if ($check_personnel) {
                return array(
                    "status" => "failed",
                    "message" => "This personnel is already existed."
                );
            }

            $wpdb->query("START TRANSACTION");
                $insert_personnel = $wpdb->query($wpdb->prepare("INSERT INTO tp_personnels (`stid`, `wpid`, `roid`, `pincode`, `status`, `created_by`) VALUES (%d, %d, %d, %d, '%s', %d)", $user['store_id'], $user['user_id'], $user['role_id'], $user['pincode'], 'active', $user['created_by']  ));
                $insert_personnel_id = $wpdb->insert_id;

                $wpdb->query("UPDATE tp_personnels SET hash_id = sha2($insert_personnel_id, 256) WHERE ID = $insert_personnel_id ");

            if ($insert_personnel == false) {
                $wpdb->query("ROLLBACK");
                return array(
                    "status" => "failed",
                    "message" => "An error occured while submitting data to server."
                );
            }else{
                $wpdb->query("COMMIT");
                return array(
                    "status" => "success",
                    "message" => "Data has been added successfully."
                );
            }
        }
    }