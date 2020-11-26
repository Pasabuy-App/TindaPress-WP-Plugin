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

    class TP_Store_Insert_address {

        public static function listen($request){
            return rest_ensure_response(
                self:: listen_open($request)
            );
        }

        public static function catch_post()
        {
            $cur_user = array();

            $cur_user['created_by']  = $_POST["wpid"];
            $cur_user['store_id']  = $_POST["stid"];

            // Address Listen
            $cur_user['street']     = $_POST["st"];
            $cur_user['country']    = $_POST["co"];
            $cur_user['province']   = $_POST["pv"];
            $cur_user['city']       = $_POST["ct"];
            $cur_user['brgy']    = $_POST["bg"];
            $cur_user['type']       = $_POST["type"];
            isset($_POST['lat']) && !empty($_POST['lat']) ? $cur_user['latitude'] =  $_POST['lat'] :  $cur_user['latitude'] = null ;
            isset($_POST['long']) && !empty($_POST['long']) ? $cur_user['longitude'] =  $_POST['long'] :  $cur_user['longitude'] = null ;

            return  $cur_user;
        }

        public static function listen_open($request){
            global $wpdb;
            $stats = '';

            if ( DV_Verification::is_verified() == false ) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Verification Issue!",
                );
            }

            $user = self::catch_post();

            $address_data = DV_Address_Config::get_address( null, $user["store_id"], 'active', null, null, true);

            $data = array(
                "st" => empty($address_data) ? $user["street"] : $address_data->street,
                "bg" => empty($address_data) ? $user["brgy"] : $address_data->brgy_code,
                "ct" => empty($address_data) ? $user["city"] : $address_data->city_code,
                "pv" => empty($address_data) ? $user["province"] : $address_data->province_code,
                "co" => empty($address_data) ? $user["country"] : $address_data->country_code,
                "type" => empty($address_data) ? $user["type"] : $address_data->types,
            );

            $wpdb->query("START TRANSACTION");

            if (empty($address_data)) {
                /* Create address if address_data is empty */
                $import = DV_Address_Config::add_address( $data, 0, $user["store_id"], $user['latitude'], $user['longitude']);

                // Update store address ID
                    $_POST['adid'] = $import['data'];
                    $update_store =  TP_Store_Update_v2::listen_open($request);

                    if ($update_store['status'] == "failed") {
                        $wpdb->query("ROLLBACK");
                        return array(
                            "status" => "failed",
                            "message" => $update_store['message']
                        );
                    }
                // End
                $stats = 'added';
            }else{
                $user['latitude'] = empty($address_data) ? $user['latitude'] : $address_data->latitude;
                $user['longitude'] = empty($address_data) ? $user['longitude'] : $address_data->longitude;

                /* Update current store address */
                $import = DV_Address_Config::add_address( $data,  0, $user["store_id"], $user['latitude'], $user['longitude'],  null, $status = 'active', $address_data->hash_id );
                $stats = 'updated';

            }

            if ($import['status'] == "failed") {
                $wpdb->query("ROLLBACK");
                return array(
                    "status" => "failed",
                    "message" => $import['message']
                );
            }else{
                $wpdb->query("COMMIT");
                return array(
                    "status" => "success",
                    "message" => "Data has been $stats successfully."
                );
            }
        }
    }