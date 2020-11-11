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

    class TP_Store_Lisitng_Docs_v2 {

        //REST API Call
        public static function listen($request){

            return rest_ensure_response(
                self::listen_open($request)
            );
        }

        public static function catch_post(){
            $curl_user = array();

            isset($_POST['ID']) && !empty($_POST['ID'])? $curl_user['ID'] =  $_POST['ID'] :  $curl_user['ID'] = "" ;
            isset($_POST['type']) && !empty($_POST['type'])? $curl_user['type'] =  $_POST['type'] :  $curl_user['type'] = "" ;
            isset($_POST['status']) && !empty($_POST['status'])? $curl_user['status'] =  $_POST['status'] :  $curl_user['status'] = "" ;

            return $curl_user;
        }

        public static function listen_open($request){

            global $wpdb;
            $tbl_docu = TP_STORES_DOCS_v2;
            $tbl_docu_type = TP_STORES_DOCS_TYPES_v2;


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
                    hsid as ID,
                    stid,
                    types as `type_id`,
                    (SELECT title FROM $tbl_docu_type WHERE hsid = c.types ) as doctype,
                    preview,
                    comments,
                    IF ( executed_by is null AND activated = 'false', 'Pending',
                        IF ( executed_by != '' AND activated = 'false', 'Inactive', 'Active' )  ) as `status`,
                    date_created
                FROM
                    $tbl_docu c
                WHERE
                    ID IN ( SELECT MAX( pdd.ID ) FROM $tbl_docu  pdd WHERE pdd.hsid = hsid GROUP BY hsid ) ";

            if ($user["ID"]) {
                $sql .= " AND hsid = '{$user["ID"]}'  ";
            }

            if ($user["status"]) {
                $sql .= " AND `status` = '{$user["status"]}'  ";
            }

            if ($user["type"]) {
                $sql .= " AND `types` = '{$user["type"]}'  ";
            }

            $data = $wpdb->get_results($sql);

            foreach ($data as $key => $value) {
                if (is_numeric($value->preview)) {

                    $image = wp_get_attachment_image_src( $value->preview, 'medium', $icon =false );
                    if ($image != false) {
                        $value->preview = $image[0];
                    }else{
                        $get_image = $wpdb->get_row("SELECT meta_value FROM wp_postmeta WHERE meta_id = $value->preview ");
                        if(!empty($get_image)){
                            // $value->preview = 'https://pasabuy.app/wp-content/uploads/'.$get_image->meta_value;
                            $value->preview = 'None';
                        }else{
                            $value->preview = 'None';
                        }
                    }

                }else{
                    $value->preview = 'None';
                }
            }

            return array(
                "status" => "success",
                "data" => $data
            );

        }
    }