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

    class TP_Product_Listing_v2 {

        //REST API Call
        public static function listen($request){

            return rest_ensure_response(
                self::listen_open($request)
            );
        }

        public static function catch_post(){
            $curl_user = array();

            isset($_POST['stid']) && !empty($_POST['stid'])? $curl_user['stid'] =  $_POST['stid'] :  $curl_user['stid'] = null ;
            isset($_POST['ctid']) && !empty($_POST['ctid'])? $curl_user['ctid'] =  $_POST['ctid'] :  $curl_user['ctid'] = null ;
            isset($_POST['title']) && !empty($_POST['title'])? $curl_user['title'] =  $_POST['title'] :  $curl_user['title'] = null ;
            isset($_POST['ID']) && !empty($_POST['ID'])? $curl_user['ID'] =  $_POST['ID'] :  $curl_user['ID'] = null ;
            isset($_POST['inventory']) && !empty($_POST['inventory'])? $curl_user['inventory'] =  $_POST['inventory'] :  $curl_user['inventory'] = null ;
            isset($_POST['status']) && !empty($_POST['status'])? $curl_user['status'] =  $_POST['status'] :  $curl_user['status'] = null ;

            return $curl_user;
        }

        public static function listen_open($request){

            global $wpdb;
            $tbl_product = TP_PRODUCT_v2;
            $tbl_stores = TP_STORES_v2;
            $tbl_product_rates = TP_PRODUCT_RATING_v2;
            $tbl_product_category = TP_PRODUCT_CATEGORY_v2;

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
            $_default_avatar = TP_PLUGIN_URL . "assets/images/default-product.png";
            $_default_banner = TP_PLUGIN_URL . "assets/images/default-banner.png";

            $sql = "SELECT
                hsid as ID,
                (SELECT title FROM $tbl_stores WHERE hsid = stid AND id IN ( SELECT MAX( id ) FROM $tbl_stores s WHERE s.hsid = hsid  GROUP BY hsid )  ) as store_name,
                (SELECT title FROM $tbl_product_category WHERE hsid = pcid AND id IN ( SELECT MAX( id ) FROM $tbl_product_category ct WHERE ct.hsid = hsid  GROUP BY hsid ) ) as category_name,
                pcid,
                title,
                info,
                avatar,
                banner,
                price,
                IF( ( SELECT  AVG(rates) as rates FROM $tbl_product_rates WHERE pdid = p.hsid )is null, 0, ( SELECT  AVG(rates) as rates FROM $tbl_product_rates WHERE pdid = p.hsid ) ) as rates,
                IF( discount is null OR discount = '',  0, discount ) as discount,
                `status`,
                inventory,
                date_created
            FROM
                $tbl_product p
            WHERE
                ID IN ( SELECT MAX( pdd.ID ) FROM $tbl_product  pdd WHERE pdd.hsid = p.hsid GROUP BY pdd.hsid )  ";

            if($user["ctid"] != null){
                $sql .= " AND pcid = '{$user["ctid"]}' ";
            }

            if ($user["title"] != null) {
                $sql .= " AND title LIKE '%{$user["title"]}%' ";
            }

            if ($user["ID"] != null) {
                $sql .= " AND hsid = '{$user["ID"]}' ";
            }

            if ($user["stid"] != null) {
                $sql .= " AND stid = '{$user["stid"]}' ";
            }

            if ($user["inventory"] != null) {
                if ($user["inventory"] != "true" && $user["inventory"] != "false") {
                    return array(
                        "status" => "success",
                        "message" => "Invalid value of inventory."
                    );
                }

                $sql .= " AND inventory = '{$user["inventory"]}' ";
            }

            if ($user["status"] != null) {
                if ($user["status"] != "active" && $user["status"] != "inactive") {
                    return array(
                        "status" => "success",
                        "message" => "Invalid value of status."
                    );
                }

                $sql .= " AND `status` = '{$user["status"]}' ";
            }

            $sql .= " ORDER BY ID DESC ";

            $data = $wpdb->get_results($sql);
            foreach ($data as $key => $value) {
                if (is_numeric($value->avatar)) {
                    $image = wp_get_attachment_image_src( $value->avatar, 'medium', $icon = false );
                    if ($image != false) {
                        $value->avatar = $image[0];
                    }
                }
            }

            return array(
                "status" => "success",
                "data" => $data
            );
        }
    }