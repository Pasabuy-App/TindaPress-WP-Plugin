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

    class TP_Update_Personnel {

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

			// Step 2: Validate user
			if (DV_Verification::is_verified() == false) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. verification issues!",
                );

            }

            if (!isset($_POST['user_id'])) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Request unknown!",
                );
            }

            $wpdb->query("START TRANSACTION");

                if (isset($_POST['pincode'])) {

                    if ($_POST['pincode'] !== null) {

                        if (strlen($_POST['pincode']) != 4) {
                            return array(
                                "status" => "failed",
                                "message" => "Pincode must have 4 characters only."
                            );
                        }


                        if (!is_numeric($_POST['pincode'])) {
                            return array(
                                "status" => "failed",
                                "message" => "Pincode must be numbers only."
                            );
                        }

                        $update_pincode =  $wpdb->query("UPDATE tp_personnels SET pincode = '{$_POST["pincode"]}' WHERE wpid = '{$_POST["user_id"]}'");

                        if ($update_pincode == false) {
                            $wpdb->query("ROLBACK");
                            return array(
                                "status" => "failed",
                                "message" => "An error occured while submitting data to server."
                            );
                        }

                    }
                }

               /*  if (isset($_POST['roid'])) {

                    if ($_POST['roid'] !== null) {

                        if (!is_numeric($_POST['roid'])) {
                            return array(
                                "status" => "failed",
                                "message" => "Invalid value of role id."
                            );
                        }

                        // Checking role
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
                        // End of checking role

                        $update_role =  $wpdb->query("UPDATE tp_personnels SET roid = '{$_POST["roid"]}' WHERE wpid = '{$_POST["user_id"]}'");

                        if ($update_pincode == false) {
                            $wpdb->query("ROLBACK");
                            return array(
                                "status" => "failed",
                                "message" => "An error occured while submitting data to server."
                            );
                        }
                    }


                }
 */
            $wpdb->query("COMMIT");
            return array(
                "status" => "success",
                "message" => "Data has been added successfully."
            );
        }
    }