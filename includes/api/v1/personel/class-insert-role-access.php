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

    class TP_Personel_Create_Role_Meta {

        //REST API Call
        public static function listen(){

            return rest_ensure_response(
                self::listen_open()
            );
        }

        //QA Done 2020-08-12 4:10 pm
        public static function listen_open(){
            global $wpdb;


            // Validating  access
            $access = $wpdb->get_results("SELECT hash_id FROM tp_access");

            $var = array();
            $role_id = $_POST['roid'];

            for ($count=0; $count < count($access) ; $count++) {
                $var[] = $access[$count]->hash_id;
            }

            for ($i=0; $i < count((array)$_POST['data']['access']) ; $i++) {

                if( !in_array( $_POST['data']['access'][$i], $var)){
                    return array(
                        "status" => "failed",
                        "message" => "This access does not exists.",
                    );
                }

                $access =  $_POST['data']['access'][$i];

                $role_meta = $wpdb->query("INSERT INTO tp_roles_meta (roid, acsid, `status` ) VALUES ($role_id, '$access', '1' )");
                $role_meta_id = $wpdb->insert_id;

                $wpdb->query("UPDATE tp_roles_meta SET hash_id = sha2($role_meta_id, 256) WHERE ID = $role_meta_id");
            }


            if ($role_meta == false) {
                return array(
                    "status" => "failed",
                    "message" => "An erro ocurred while submiting data to server."
                );
            }else{
                return array(
                    "status" => "success",
                    "message" => "Data has been added successfully."
                );
            }



        }

        public static function catch_post(){
            $curl_user = array();

            $curl_user['role_id'] = $_POST['roid'];
            $curl_user['access'] = $_POST['acid'];

            return $curl_user;
        }
    }