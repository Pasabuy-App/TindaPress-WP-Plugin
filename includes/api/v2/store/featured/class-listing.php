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

    class TP_Featured_Store_Listing_v2 {

        //REST API Call
        public static function listen($request){

            return rest_ensure_response(
                self::listen_open($request)
            );
        }

        public static function catch_post(){
            $curl_user = array();

            isset($_POST['type']) && !empty($_POST['type'])? $curl_user['type'] =  $_POST['type'] :  $curl_user['type'] = null ;
            isset($_POST['ID']) && !empty($_POST['ID'])? $curl_user['ID'] =  $_POST['ID'] :  $curl_user['ID'] = null ;
            isset($_POST['stid']) && !empty($_POST['stid'])? $curl_user['stid'] =  $_POST['stid'] :  $curl_user['stid'] = null ;
            isset($_POST['status']) && !empty($_POST['status'])? $curl_user['status'] =  $_POST['status'] :  $curl_user['status'] = null ;
            $curl_user['wpid'] = $_POST['wpid'];
            return $curl_user;
        }

        public static function listen_open($request){
            global $wpdb;

            $tbl_store = TP_STORES_v2;
            $tbl_featured_store = TP_FEATURED_STORES_v2;
            $tbl_featured_store_groups = TP_FEATURED_STORES_GROUPS_v2;
            $tbl_store_category_groups = TP_STORES_CATEGORY_GROUPS_v2;
            $tbl_rate = TP_STORES_RATINGS_v2;
            $tbl_schedule = MP_SCHEDULES_v2;
            $tbl_store_category = TP_STORES_CATEGORIES_v2;
            $tbl_address_view = DV_ADDRESS_VIEW;
            $tbl_operation = MP_OPERATIONS_v2;
            $tbl_featured_store_seen = TP_FEATURED_STORES_SEEN_v2;
            $time = time();
            $date = date("Y:m:d");
            $day = lcfirst(date('D', $time));
            // Step 1: Check if prerequisites plugin are missing
            $plugin = TP_Globals_v2::verify_prerequisites();
            if ($plugin !== true) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. ".$plugin." plugin missing!",
                );
            }

            // // Step 2: Validate user
            if (DV_Verification::is_verified() == false) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Verification issues!",
                );
            }

            $user = self::catch_post();

            $sql = " SELECT
                    hsid as ID,
                    stid as hsid,
                    (SELECT title FROM $tbl_store s WHERE hsid = stid AND id IN ( SELECT MAX( id ) FROM $tbl_store  WHERE hsid = s.hsid GROUP BY hsid  ) ) as title,
                    groups,
                    avatar,
                    banner,
                    `status`,
                    date_created
                FROM
                    $tbl_featured_store fs
                WHERE
                    id IN ( SELECT MAX( id ) FROM $tbl_featured_store WHERE  hsid = fs.hsid GROUP BY stid ) " ;

            if ($user["ID"] != null ) {
                $sql .= " AND hsid = '{$user["ID"]}' ";
            }

            if ($user["status"] != null ) {
                $sql .= " AND `status` = '{$user["status"]}' ";
            }

            if ($user["stid"] != null ) {
                $sql .= " AND `stid` = '{$user["stid"]}' ";
            }

            if ($user['type'] != null) {

                // Check filter type
                    $get = $wpdb->get_results("SELECT title FROM $tbl_featured_store_groups ");
                    $check = TP_Globals_v2::check_type($user['type'], $get);
                    if ($check == false) {
                        return array(
                            "status" => "failed",
                            "message" => "Invalid value of type."
                        );
                    }
                // End

                $sql .= " AND  groups IN (SELECT hsid FROM $tbl_featured_store_groups WHERE title LIKE  '%{$user["type"]}%' )  ";

            }

            $sql .= " LIMIT 5";
            $data = $wpdb->get_results($sql);


            $a = array();
            $ops = array();

            foreach ($data as $key => $value) {
                $store = $wpdb->get_row("SELECT
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
                        hsid = '$value->stid'
                    AND
                        id IN ( SELECT MAX( id ) FROM $tbl_store s WHERE s.hsid = hsid  GROUP BY hsid )");


                // Store category
                    $get_category = $wpdb->get_row("SELECT title FROM $tbl_store_category WHERE hsid = '$store->scid'  AND id IN ( SELECT MAX( id ) FROM $tbl_store_category sc WHERE sc.hsid = hsid  GROUP BY hsid ) ");
                    if (empty($get_category)) {
                        $value->category_name = '';
                    }else{
                        $value->category_name = $get_category->title;
                    }
                // End

                // Get Store Data
                    $get_store_address = $wpdb->get_row("SELECT * FROM $tbl_address_view WHERE ID = '$store->adid' ");
                    $get_store_address_code = $wpdb->get_row("SELECT
                        ( SELECT child_val FROM dv_revisions WHERE ID = d.city ) AS city_code,
                        ( SELECT child_val FROM dv_revisions WHERE ID = d.province ) AS province_code,
                        ( SELECT child_val FROM dv_revisions WHERE ID = d.country ) AS country_code,
                        ( SELECT child_val FROM dv_revisions WHERE ID = d.brgy ) AS brgy_code
                    FROM
                        dv_address d WHERE ID = '$store->adid' ");

                    if (empty($get_store_address) || empty($get_store_address_code)) {
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
                    }else{
                        $value->street        = $get_store_address->street;
                        $value->brgy          = $get_store_address->brgy;
                        $value->brgy_code     = $get_store_address_code->brgy_code;
                        $value->city          = $get_store_address->city;
                        $value->city_code     = $get_store_address_code->city_code;
                        $value->province      = $get_store_address->province;
                        $value->province_code = $get_store_address_code->province_code;
                        $value->country       = $get_store_address->country;
                        $value->country_code  = $get_store_address_code->country_code;
                        if (isset($get_store_address->latitude)) {
                            $value->latitude = $get_store_address->latitude;
                        }else{
                            $value->latitude = "";
                        }
                        if (isset($get_store_address_code->longitude)) {
                            $value->longitude = $get_store_address_code->longitude;
                        }else{
                            $value->longitude = "";
                        }
                    }
                // End

                // Get store rates
                    $get_rates = $wpdb->get_row("SELECT hsid as ID, stid, IF(AVG(rates) is null OR AVG(rates) = 0 , 'No ratings yet', FORMAT(AVG(rates), 1))as rates FROM $tbl_rate WHERE stid = '$store->hsid' ");
                    if (!empty($get_rates)) {
                        $value->rates = $get_rates->rates ;
                    }
                // End

                // Get store schedule
                    $get_schedule = $wpdb->get_row("SELECT * FROM $tbl_schedule WHERE stid = '$store->hsid'");
                    $get_schedule_today = $wpdb->get_row("SELECT stid, hsid FROM $tbl_schedule WHERE stid = '$store->hsid' AND types = '$day' ");
                    if (!empty($get_schedule)) {
                        $value->opening = $get_schedule->started;
                        $value->closing = $get_schedule->ended;
                    }
                // End



                if (is_numeric($value->avatar)) {
                    $image = wp_get_attachment_image_src( $value->avatar, 'full', $icon = false );

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

                if (is_numeric($store->banner)) {

                    $image = wp_get_attachment_image_src( $store->banner, 'full', $icon =false );
                    if ($image != false) {
                        $value->banner = $image[0];
                    }else{
                        $get_image = $wpdb->get_row("SELECT meta_value FROM wp_postmeta WHERE meta_id = $store->banner ");
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

                        $operation = $wpdb->get_row("SELECT hsid FROM $tbl_operation WHERE stid = '$store->hsid' AND sdid = '$get_schedule_today->hsid'  ");
                        if (!empty($operation)) {
                            $value->operation_id = $operation->hsid;
                        }else{
                            $value->operation_id = "";
                        }

                    }else{
                        $value->operation_id = "";
                    }
                // End

                $seen = TP_Globals_v2::seen($tbl_featured_store_seen, $user['wpid'], "ftid", $value->ID);
                if ($seen == false) {
                    return array(
                        "status" => "failed",
                        "message" => "Please contact your administrator. Featured store seen failed."
                    );
                }

                // Add store seen count
                $get_Fstore_seen_count = $wpdb->get_row("SELECT count(wpid) as `count` FROM $tbl_featured_store_seen WHERE ftid = '$value->ID' ");
                if (!empty($get_Fstore_seen_count)) {
                    $value->store_seen = $get_Fstore_seen_count->count;
                }
                // End
            }


            return array(
                "status" => "success",
                "data" => $data
            );
        }
    }