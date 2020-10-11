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

    class TP_Featured_Store_Insert {

        public static function listen(WP_REST_Request $request){
            return rest_ensure_response(
                self::list_open($request)
            );
        }

        public static function catch_post(){
            $curl_user = array();
            $curl_user['store_id'] = $_POST['stid'];
            $curl_user['type'] = $_POST['type'];
            return $curl_user;
        }

        public static function list_open($request){
            global $wpdb;

            $files = $request->get_file_params();

            // $plugin = TP_Globals::verify_prerequisites();
            // if ($plugin !== true) {
            //     return array(
            //         "status" => "unknown",
            //         "message" => "Please contact your administrator. ".$plugin." plugin missing!",
            //     );
            // }

            // if (DV_Verification::is_verified() == false) {
            //     return array(
            //         "status" => "unknown",
            //         "message" => "Please contact your administrator. Verification Issues!",
            //     );
            // }

            // if (!isset($_POST['stid']) || !isset($_POST['type'])) {
            //     return array(
            //         "status" => "unknown",
            //         "message" => "Please contact your administrator. Request unknown!"
            //     );
            // }


            // if (empty($_POST['stid']) || empty($_POST['type'])) {
            //     return array(
            //         "status" => "failed",
            //         "message" => "Required fields cannot be empty."
            //     );
            // }

            // if($_POST['type'] != "food" && $_POST['type'] != "store" && $_POST['type'] != "market"){
            //     return array(
            //         "status" => "failed",
            //         "message" => "Invalid value of type."
            //     );
            // }


            // Check featured store
            $status = 'inactive';

            $check_table = $wpdb->get_results("SELECT `status` FROM tp_featured_store ");


            if (!empty($check_table)) {
                $var = array();
                foreach ($check_table as $key => $value) {
                    $value->status == "active"? $smp = COUNT($value->status):$smp;

                }
            }
            return $smp;


return;
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
            $wpdb->query("START TRANSACTION");

            $result = $wpdb->query($wpdb->prepare("INSERT INTO tp_featured_store (`type`, `stid`, `created_by`) VALUES ('%s', %d, %d)", $user['type'], $user['store_id'], $_POST['wpid'] ));


            $featured_id = $wpdb->insert_id;

            if (isset($files['logo'])) {

                if (!empty($files['logo'])) {
                    $smp1 = array(
                        "img" => $files['logo']
                    );

                    $logo = DV_Globals::upload_image( $request, $smp1);

                    if ($logo['status'] != 'success') {
                        return array(
                            "status" => $logo['status'],
                            "message" => $logo['message']
                        );
                    }

                    $update_logo = $wpdb->query("UPDATE tp_featured_store SET logo = '{$logo["data"]}' WHERE ID = '$featured_id'");

                    if ($update_logo == false) {
                        $wpdb->query("ROLLBACK");
                        return array(
                            "status" => "failed",
                            "message" => "An error occured while submiting data to server."
                        );
                    }
                }
            }


            if (isset($files['banner'])) {

                if (!empty($files['banner'])) {

                    $smp2 = array(
                        "img" => $files['banner']
                    );
                    $banner = DV_Globals::upload_image( $request, $smp2);

                    if ($banner['status'] != 'success') {
                        return array(
                            "status" => $banner['status'],
                            "message" => $banner['message']
                        );
                    }

                    $update_banner = $wpdb->query("UPDATE tp_featured_store SET banner = '{$banner["data"]}' WHERE ID = '$featured_id'");

                    if ($update_banner == false) {
                        $wpdb->query("ROLLBACK");

                        return array(
                            "status" => "failed",
                            "message" => "An error occured while submiting data to server."
                        );
                    }
                }
            }

            if ($result == false) {
                $wpdb->query("ROLLBACK");
                return array(
                    "status" => "failed",
                    "message" => "An error occured while submiting data to server."
                );

            }else{
                $wpdb->query("COMMIT");

                return array(
                    "status" => "success",
                    "message" => "Data has been added successfully."
                );
            }
        }
    }