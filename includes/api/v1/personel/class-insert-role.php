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






            return $_POST['access'];    

            if (!isset($_POST['stid']) || !isset($_POST['acsid']) ) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Request unknown."
                );
            }
            $user = self::catch_post();


            // Validate Access

                $check_access = $wpdb->get_row(
                "SELECT hash_id FROM tp_access WHERE hash_id = '{$user["access_id"]}'"
                );

                if (!$check_access) {
                    return array(
                        "status" => "failed",
                        "message" => "This access does not exists."
                    );
                }

            // End validate


            $wpdb->query("START TRANSACTION");
                
            // insert table role
                $result_role = $wpdb->query(
                    $wpdb->prepare("INSERT INTO tp_roles (`wpid`, `stid`, `created_by` ) 
                    VALUES ('%s', '%s', '%s' )", $user['user_id'], $user['store_id'], $user['user_id'] )
                );
                $role_id = $wpdb->insert_id;

                $update_id = $wpdb->query("UPDATE tp_roles SET hash_id = sha2($role_id,256) WHERE ID = $role_id");

                $get_role_id = $wpdb->get_row("SELECT hash_id FROM tp_roles WHERE ID = $role_id");
            // Insert table role meta
                $result_role_meta = $wpdb->query(
                    $wpdb->prepare(
                        "INSERT INTO tp_roles_meta (roid, acsid) VALUES ('%s', '%s')", $get_role_id->hash_id, $user["access_id"]
                    )
                );
                $result_role_meta_id = $wpdb->insert_id;

                $wpdb->query("UPDATE tp_roles_meta SET hash_id = sha2($result_role_meta_id, 256) WHERE ID = $result_role_meta_id");


                if ($result_role < 1 || $update_id < 1 || $result_role_meta < 1) {
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




            return "HAHAHAHAHA";
        }

        public static function catch_post(){
            $curl_user = array();
            $curl_user['user_id'] = $_POST['wpid'];   
            $curl_user['store_id'] = $_POST['stid'];   
            $curl_user['access_id'] = $_POST['acsid'];   
            return $curl_user;
        }
    }