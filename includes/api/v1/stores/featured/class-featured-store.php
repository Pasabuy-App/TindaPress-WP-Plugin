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
?>
<?php

    class TP_Featured_Store {

        public static function listen(WP_REST_Request $request){
            return rest_ensure_response(
                self::list_open($request)
            );
        }

        public static function catch_post(){
            $curl_user = array();
            $curl_user['store_id'] = $_POST['stid'];
            return $curl_user;
        }

        public static function list_open($request){

            global $wpdb;

            $files = $request->get_file_params();

            $user = self::catch_post();

            // Step 5: Check if store exists
            $store_data = $wpdb->get_row("SELECT child_val as stats FROM tp_revisions WHERE ID = (SELECT `status` FROM tp_stores WHERE ID = '{$user["store_id"]}')");

            // Check if no rows found
            if (!$store_data) {
                return array(
                    "status" => "failed",
                    "message" => "This store does not exists.",
                );
            }

            //Fails if already activated
            if ($store_data->stats == 0) {
                return array(
                    "status" => "failed",
                    "message" => "This store is currently inactive.",
                );
            }

            if (isset($_POST)) {
                # code...
            }




            $wpdb->query("INSERT INTO tp_featured_store (`type`, `stid`, `logo`,`banner`, `created_by`) VALUES ('food', '2', 'None', 'Burce Banner', '1')");

            $result = DV_Globals::upload_image( $request, $files);
        }
    }