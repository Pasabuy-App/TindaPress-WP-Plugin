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

  	class TP_Store_Insert_Docs_v2 {

        public static function listen(WP_REST_Request $request){
            return rest_ensure_response(
                self::listen_open($request)
            );
        }

        public static function catch_post(){
            $curl_user = array();

            $curl_user['stid'] = $_POST['stid'];
            $curl_user['types'] = $_POST['types'];
            $curl_user['comments'] = $_POST['comments'] == null? 'N/A': $_POST['comments'] ;

            return $curl_user;
        }

        public static function listen_open($request){

			// Initialize WP global variable
            global $wpdb;

            $tbl_vehicle = HP_VEHICLES_v2;
            $tbl_vehicle_docs = HP_VEHICLES_DOCS_v2;
            $tbl_vehicle_types = HP_VEHICLE_TYPES_v2;
            $tbl_vehicle_docs_fields = HP_VEHICLES_DOCS_FIELDS_v2;
            $tbl_vehicle_docs_types = HP_VEHICLES_DOCS_TYPES_v2;
            $files = $request->get_file_params();

            // Step 1: Check if prerequisites plugin are missing
            $plugin = HP_Globals_v2::verify_prerequisites();
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
                    "message" => "Please contact your administrator. Verification issues!",
                );
            }

            if(!isset($_POST['stid']) || !isset($_POST['comments']) || !isset($_POST['types'])  ){
                return  array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Request Unknown!",
                );
            }

            $user = self::catch_post();

            $validate = HP_Globals_v2::check_listener($user);
            if ($validate !== true) {
                return array(
                    "status" => "failed",
                    "message" => "Required fileds cannot be empty "."'".ucfirst($validate)."'"."."
                );
            }

            // Check if Vehicle exists
            $check_vehicle = $wpdb->get_row("SELECT ID FROM $tbl_vehicle WHERE hsid = '{$user["vhid"]}' AND `status` = 'active' ");
            if (empty($check_vehicle)) {
                return array(
                    "status" => "failed",
                    "message" => "This vehicle does not exists."
                );
            }

            // Check Vehicle docs is already exists
            $check_vehicle_document = $wpdb->get_row("SELECT * FROM $tbl_vehicle_docs WHERE types = '{$user["types"]}' AND vhid = '{$user["vhid"]}' AND `status` = 'active'   ");

            if(!empty($check_vehicle_document)){
                // Retuern  if document is already exists
                if( $check_vehicle_document->executed_by != null && $check_vehicle_document->activated == "true"  ){
                    return array(
                        "status" => "failed",
                        "message" => "This documents is already exists."
                    );
                }

                // Reutrn if ducment is already been pending
                if( $check_vehicle_document->executed_by == null && $check_vehicle_document->activated == "false"  ){
                    return array(
                        "status" => "failed",
                        "message" => "This documents is already been pending."
                    );
                }
            }

            // Start MYSQL Transaction
            $wpdb->query("START TRANSACTION");

            // Check if document types is exists
            $types = $wpdb->get_row("SELECT * FROM $tbl_vehicle_docs_types WHERE hsid = '{$user["types"]}' AND `status` = 'active' ");
            if (empty($types)) {
                return array(
                    "status" => "failed",
                    "message" => "Type of document does not exists"
                );
            }

            // Call upload image script
            $image = HP_Globals_v2::upload_image( $request, $files);

            if ($image['status'] != 'success') {
                return array(
                    "status" => $image['status'],
                    "message" => $image['message']
                );
            }

            // Insert Vehicles documents
            $docs = $wpdb->query("INSERT
                INTO
                    $tbl_vehicle_docs
                        ($tbl_vehicle_docs_fields)
                    VALUES
                        ('{$user["vhid"]}', '{$image["data"]}', '{$user["types"]}', '{$user["comments"]}') ");
            $docs_id = $wpdb->insert_id;

            // Update hash_id if mover using sha256 algorithm
            $wpdb->query("UPDATE $tbl_vehicle_docs SET hsid = sha2($docs_id, 256) WHERE ID = '$docs_id' ");

            if ($docs < 1 ) {
                $wpdb->query("ROLLBACK");
                return array(
                    "status" => "failed",
                    "message" => "An error occured while submitting data to server."
                );
            }else{
                $wpdb->query("COMMIT");
                return array(
                    "status" => "success",
                    "message" => "Data has been addded sucessfully."
                );
            }
        }
    }