<?php
	// Exit if accessed directly
	if ( ! defined( 'ABSPATH' ) )
	{
		exit;
	}

	/**
        * @package tindapress-wp-plugin
        * @version 0.2.0
	*/

    class TP_Store_Listing_v2 {

        //REST API Call
        public static function listen($request){

            return rest_ensure_response(
                self::listen_open($request)
            );
        }

        public static function catch_post(){
            $curl_user = array();

            isset($_POST['status']) && !empty($_POST['status'])? $curl_user['status'] =  $_POST['status'] :  $curl_user['status'] = null ;
            isset($_POST['stid']) && !empty($_POST['stid'])? $curl_user['stid'] =  $_POST['stid'] :  $curl_user['stid'] = null ;
            isset($_POST['title']) && !empty($_POST['title'])? $curl_user['title'] =  $_POST['title'] :  $curl_user['title'] = null ;
            isset($_POST['scid']) && !empty($_POST['scid'])? $curl_user['scid'] =  $_POST['scid'] :  $curl_user['scid'] = null ;
            isset($_POST['type']) && !empty($_POST['type'])? $curl_user['type'] =  $_POST['type'] :  $curl_user['type'] = null ;

            $curl_user['wpid'] = $_POST['wpid'];

            return $curl_user;
        }

        public static function listen_open($request){

            global $wpdb;

            $tbl_store = TP_STORES_v2;
            $tbl_store_category = TP_STORES_CATEGORIES_v2;
            $tbl_address_view = DV_ADDRESS_VIEW;
            $tbl_operation = MP_OPERATIONS_v2;
            $tbl_store_category_groups = TP_STORES_CATEGORY_GROUPS_v2;
            $tbl_rate = TP_STORES_RATINGS_v2;
            $tbl_schedule = MP_SCHEDULES_v2;
            $tbl_store_seen = TP_STORES_SEEN_v2;
            $time = time();
            $date = date("Y:m:d");
            $day = lcfirst(date('D', $time));
            $last_id = '';

             // Step 1: Check if prerequisites plugin are missing
            $plugin = TP_Globals_v2::verify_prerequisites();
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

            $user = self::catch_post();

            $sql = "SELECT
                ID,
                hsid,
                title,
                scid,
                info,
                avatar,
                banner,
                adid,
                `status`,
                created_by,
                date_created
            FROM
                $tbl_store
            WHERE
                id IN ( SELECT MAX( id ) FROM $tbl_store s WHERE s.hsid = hsid  GROUP BY hsid ) ";

            if($user["stid"] != null){
                $sql .= " AND hsid = '{$user["stid"]}' ";
            }

            if($user["title"] != null){
                $sql .= " AND title LIKE '%{$user["title"]}%' ";
            }

            if ($user['scid'] != null) {
                $sql .= " AND scid = '{$user["scid"]}' ";
            }

            if ($user['status'] != null) {
                if ($user['status'] != "active" && $user['status'] != "inactive") {
                    return array(
                        "status" => "failed",
                        "message" => "Invalid type value of status.",
                    );
                }
                $sql .= " AND `status` = '{$user["status"]}' ";
            }

            if ($user['type'] != null) {

                // Check filter type
                    $get = $wpdb->get_results("SELECT title FROM $tbl_store_category_groups ");
                    $check = TP_Globals_v2::check_type($user['type'], $get);
                    if ($check == false) {
                        return array(
                            "status" => "failed",
                            "message" => "Invalid value of type."
                        );
                    }
                // End
                $sql .= " AND scid IN (SELECT hsid FROM $tbl_store_category WHERE groups =  (SELECT hsid FROM $tbl_store_category_groups WHERE title LIKE  '%{$user["type"]}%' ) )";
            }

            if (isset($_POST['lid'])) {
                $last_id = " LIMIT 12 ";
            }

            if (!empty($_POST['lid'])) {
                $last_id = " LIMIT 7 OFFSET {$_POST["lid"]} ";
            }

            $sql .= " ORDER BY ID DESC  $last_id ";

            $data = $wpdb->get_results($sql);
            $a = array();
            $ops = array();
            // Get other store information
                foreach ($data as $key => $value) {

                    $value->title = str_replace("\\", "",  $value->title );
                    // Store category
                        $get_category = $wpdb->get_row("SELECT title FROM $tbl_store_category WHERE hsid = '$value->scid' ");
                        if (empty($get_category)) {
                            $value->category_name = '';
                        }else{
                            $value->category_name = $get_category->title;
                        }
                    // End

                    // Get Store Data
                        $address_data = DV_Address_Config::get_address( null, $value->hsid, 'active', null, null, true);

                        if (!empty($address_data)) {

                            $value->street        = $address_data->street;
                            $value->brgy          = $address_data->brgy;
                            $value->city          = $address_data->city;
                            $value->province      = $address_data->province;
                            $value->country       = $address_data->country;

                            $value->brgy_code     = DV_Address_Config::get_geo_location( DV_BRGY_TABLE, 'brgy_name', $address_data->brgy )['data'][0]->ID;
                            $value->city_code     = DV_Address_Config::get_geo_location( DV_CITY_TABLE, 'city_name', $address_data->city )['data'][0]->city_code;
                            $value->province_code = DV_Address_Config::get_geo_location( DV_PROVINCE_TABLE, 'prov_name', $address_data->province )['data'][0]->prov_code;
                            $value->country_code  = DV_Address_Config::get_geo_location( DV_COUNTRY_TABLE, 'country_name', $address_data->country )['data'][0]->country_code;

                            $value->latitude = $address_data->latitude;
                            $value->longitude = $address_data->longitude;

                        }else{

                            $value->street        = "";
                            $value->brgy          = "";
                            $value->brgy_code     = "";
                            $value->city          = "";
                            $value->city_code     = "";
                            $value->province      = "";
                            $value->province_code = "";
                            $value->country       = "";
                            $value->country_code  = "";
                            $value->latitude  = "";
                            $value->longitude  = "";
                        }
                    // End

                    // Get store rates
                        $get_rates = $wpdb->get_row("SELECT hsid as ID, stid, IF(AVG(rates) is null OR AVG(rates) = 0 , 'No ratings yet', FORMAT(AVG(rates), 1))as rates FROM $tbl_rate WHERE stid = '$value->hsid' ");
                        if (!empty($get_rates)) {
                            $value->rates = $get_rates->rates ;
                        }
                    // End

                    // Get store schedule
                        $get_schedule = $wpdb->get_row("SELECT * FROM $tbl_schedule WHERE stid = '$value->hsid'");
                        $get_schedule_today = $wpdb->get_row("SELECT stid, hsid FROM $tbl_schedule WHERE stid = '$value->hsid' AND types = '$day' ");
                        if (!empty($get_schedule)) {
                            $value->opening = $get_schedule->started;
                            $value->closing = $get_schedule->ended;
                        }
                    // End

                    if (is_numeric($value->avatar)) {
                        $image = wp_get_attachment_image_src( $value->avatar, 'medium', $icon = false );

                        if ($image != false) {
                            $value->avatar = $image[0];
                        }else{
                            $get_image = $wpdb->get_row("SELECT meta_value FROM wp_postmeta WHERE meta_id = $value->avatar ");
                            if(!empty($get_image)){
                                // $value->avatar = 'https://pasabuy.app/wp-content/uploads/'.$get_image->meta_value;
                                $value->avatar = 'None';
                            }else{
                                $value->avatar = 'None';
                            }
                        }

                    }else{
                        $value->avatar = 'None';
                    }

                    if (is_numeric($value->banner)) {

                        $image = wp_get_attachment_image_src( $value->banner, 'medium', $icon =false );
                        if ($image != false) {
                            $value->banner = $image[0];
                        }else{
                            $get_image = $wpdb->get_row("SELECT meta_value FROM wp_postmeta WHERE meta_id = $value->banner ");
                            if(!empty($get_image)){
                                // $value->banner = 'https://pasabuy.app/wp-content/uploads/'.$get_image->meta_value;
                                $value->banner = 'None';
                            }else{
                                $value->banner = 'None';
                            }
                        }

                    }else{
                        $value->banner = 'None';
                    }

                    // Get store operation
                        if (!empty($get_schedule_today)) {

                            $operation = $wpdb->get_row("SELECT hsid FROM $tbl_operation WHERE stid = '$value->hsid' AND sdid = '$get_schedule_today->hsid'  ");
                            if (!empty($operation)) {
                                $value->operation_id = $operation->hsid;
                            }else{
                                $value->operation_id = "";
                            }

                        }else{
                            $value->operation_id = "";
                        }
                    // End

                    $seen = TP_Globals_v2::seen($tbl_store_seen, $user['wpid'], "stid", $value->hsid);
                    if ($seen == false) {
                        return array(
                            "status" => "failed",
                            "message" => "Please contact your administrator. Featured store seen failed."
                        );
                    }

                    // Add store seen count
                    $get_store_seen_count = $wpdb->get_row("SELECT count(wpid) as `count` FROM $tbl_store_seen WHERE stid = '$value->hsid' ");
                    if (!empty($get_store_seen_count)) {
                        $value->store_seen = $get_store_seen_count->count;
                    }
                    // End
                }
            // End of main loop

            return array(
                "status" => "success",
                "data" => $data
            );
        }
    }