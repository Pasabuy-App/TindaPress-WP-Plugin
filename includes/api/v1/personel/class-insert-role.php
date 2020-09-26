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

    class TP_Personel_Create_Role {

        //REST API Call
        public static function listen(){

            return rest_ensure_response(
                self::listen_open()
            );
        }

        //QA Done 2020-08-12 4:10 pm
        public static function listen_open(){
            global $wpdb;

            $table_role = TP_ROLES_TABLE;
            $table_revisions = TP_REVISIONS_TABLE;
            $table_store = TP_STORES_TABLE;

            $date = TP_Globals::date_stamp();


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
                        "message" => "Please contact your administrator. Verification Issues!",
                );
            }

            if ( !isset($_POST['title']) || !isset($_POST['info']) ) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Request unknown"
                );
            }

            if ( empty($_POST['title']) || empty($_POST['info']) ) {
                return array(
                    "status" => "failed",
                    "message" => "Required fields cannot be empty."
                );
            }

            // Get post listener
            $user = self::catch_post();

            $wpdb->query("START TRANSACTION");


            // Insert data to tp_role
            $role = $wpdb->query(
                $wpdb->prepare("INSERT INTO $table_role ( created_by) VALUES ( %d ) ", $user['user_id'] )
            ); $role_id = $wpdb->insert_id;

            // role title
            $title = $wpdb->query(
                $wpdb->prepare("INSERT INTO $table_revisions (revs_type, parent_id, child_key , child_val, created_by, date_created ) VALUES ( '%s', %d, '%s', '%s' %d, '%s'  ) ",
                'roles', $role_id, 'title', $user['title'], $user['user_id'], $date )
            );

            // role info
            $info = $wpdb->query(
                $wpdb->prepare("INSERT INTO $table_revisions (revs_type, parent_id, child_key , child_val, created_by, date_created ) VALUES ( '%s', %d, '%s', '%s' %d, '%s'  ) ",
                'roles', $role_id, 'info', $user['info'], $user['user_id'], $date )
            );

            // role status
            $info = $wpdb->query(
                $wpdb->prepare("INSERT INTO $table_revisions (revs_type, parent_id, child_key , child_val, created_by, date_created ) VALUES ( '%s', %d, '%s', '%s' %d, '%s'  ) ",
                'roles', $role_id, 'status', '1', $user['user_id'], $date )
            );

            if ($role == false || $title == false || $info == false) {
                $wpdb->query("ROLLBACK");
                return array(
                    "status" => "failed",
                    "message" => "An error occured while submitting data to server."
                );
            }else{
                $wpdd->query("COMMIT");
                return array(
                    "status" => "success",
                    "message" => "Data has been added successfully."
                );
            }
        }

        public static function catch_post(){
            $curl_user = array();
            $curl_user['user_id'] = $_POST['wpid'];
            $curl_user['title'] = $_POST['title'];
            $curl_user['info'] = $_POST['info'];
            return $curl_user;
        }
    }