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

            return $curl_user;
        }

        public static function listen_open($request){

            global $wpdb;
            // return wp_get_attachment_image_src( 1117 );

            // $data =  $wpdb->get_results("SELECT ID, banner FROM tp_v2_stores WHERE banner LIKE '%https://%' ");
            // $i = array();
            // $a = 0;



            // foreach ($data as $key => $value) {
            //     //$image = substr($value->banner,39);
            //     // $image  = substr($value->banner,47);
            //     // $a++;

            //     $i = $wpdb->get_row("SELECT meta_id FROM wp_postmeta WHERE meta_value LIKE '%$image%' ");
            //     // if (!empty($b)) {
            //     //     $wpdb->query("UPDATE tp_v2_stores SET banner = '$b->meta_id' WHERE ID = '$value->ID' ");
            //     // }
            // }
            // return $a;



            $tbl_store = TP_STORES_v2;
            $tbl_store_category = TP_STORES_CATEGORIES_v2;
            $tbl_address_view = DV_ADDRESS_VIEW;
            $tbl_operation = MP_OPERATIONS_v2;
            $tbl_store_category_groups = TP_STORES_CATEGORY_GROUPS_v2;
            $tbl_rate = TP_STORES_RATINGS_v2;

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
                id IN ( SELECT MAX( id ) FROM $tbl_store GROUP BY title ) ";

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
                            "data" => "Invalid value of type."
                        );
                    }
                // End

                $sql .= " AND scid IN (SELECT hsid FROM $tbl_store_category WHERE groups =  (SELECT hsid FROM $tbl_store_category_groups WHERE title LIKE  '%{$user["type"]}%' ) )";
            }

            $data = $wpdb->get_results($sql);
            $a = array();

            // Get other store information
                foreach ($data as $key => $value) {
                    // Store category
                        $get_category = $wpdb->get_row("SELECT title FROM $tbl_store_category WHERE hsid = '$value->scid' ");
                        if (empty($get_category)) {
                            $value->category_name = '';
                        }else{

                            $value->category_name = $get_category->title;
                        }
                    // End

                    // Get Store Data
                        $get_store_address = $wpdb->get_row("SELECT * FROM $tbl_address_view WHERE ID = $value->adid ");
                        $get_store_address_code = $wpdb->get_row("SELECT
                            ( SELECT child_val FROM dv_revisions WHERE ID = d.city ) AS city_code,
                            ( SELECT child_val FROM dv_revisions WHERE ID = d.province ) AS province_code,
                            ( SELECT child_val FROM dv_revisions WHERE ID = d.country ) AS country_code,
                            ( SELECT child_val FROM dv_revisions WHERE ID = d.brgy ) AS brgy_code
                        FROM
                            dv_address d WHERE ID = $value->adid ");

                        if (empty($get_store_address) || empty($get_store_address_code)) {

                            $value->street = "";
                            $value->brgy = "";
                            $value->brgy_code = "";
                            $value->city = "";
                            $value->city_code = "";
                            $value->province = "";
                            $value->province_code = "";
                            $value->country = "";
                            $value->country_code = "";

                        }else{
                            $value->street = $get_store_address->street;
                            $value->brgy = $get_store_address->brgy;
                            $value->brgy_code = $get_store_address_code->brgy_code;
                            $value->city = $get_store_address->city;
                            $value->city_code = $get_store_address_code->city_code;
                            $value->province = $get_store_address->province;
                            $value->province_code = $get_store_address_code->province_code;
                            $value->country = $get_store_address->country;
                            $value->country_code = $get_store_address_code->country_code;
                        }

                    // End

                    // Get store rates
                        $get_rates = $wpdb->get_row("SELECT hsid as ID, stid, IF(AVG(rates) is null OR AVG(rates) = 0 , 'No ratings yet', FORMAT(AVG(rates), 1))as rates FROM $tbl_rate WHERE stid = '$value->hsid' ");
                        if (!empty($get_rates)) {
                            $value->rates = $get_rates->rates ;
                        }
                    // End

                    // Get store schedule
                        $get_schedule = $wpdb->get_row("SELECT * FROM mp_v2_schedule WHERE stid = '$value->hsid'");
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

                    if (is_numeric($value->banner)) {

                        $image = wp_get_attachment_image_src( $value->banner, 'full', $icon =false );
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
                }
            // End

            return array(
                "status" => "success",
                "data" => $data
            );
        }
    }