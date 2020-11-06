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

            $tbl_store = TP_STORES_v2;
            $tbl_store_document = TP_STORES_DOCS_v2;
            $tbl_store_document_fileds = TP_STORES_DOCS_FIELDS_v2;
            $tbl_store_type = TP_STORES_DOCS_TYPES_v2;
            $files = $request->get_file_params();

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

            if (!isset($_POST['stid']) || !isset($_POST['types']) || !isset($_POST['comments']))  {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Request unknown!"
                );
            }

            $user = self::catch_post();

            $validate = TP_Globals_v2::check_listener($user);
            if ($validate !== true) {
                return array(
                    "status" => "failed",
                    "message" => "Required fileds cannot be empty "."'".ucfirst($validate)."'"."."
                );
            }

            // Check if store exists
                $check_store = $wpdb->get_row("SELECT ID FROM $tbl_store WHERE hsid = '{$user["stid"]}' AND `status` = 'active' ");
                if (empty($check_store)) {
                    return array(
                        "status" => "failed",
                        "message" => "This store does not exists."
                    );
                }
            // End

            // Check if document types is exists
                $types = $wpdb->get_row("SELECT ID FROM $tbl_store_type WHERE hsid = '{$user["types"]}' AND `status` = 'active' ");
                if (empty($types)) {
                    return array(
                        "status" => "failed",
                        "message" => "Type of document does not exists"
                    );
                }
            // End

            // Check store docs is already exists
                $check_store_document = $wpdb->get_row("SELECT executed_by, activated  FROM $tbl_store_document WHERE types = '{$user["types"]}' AND stid = '{$user["stid"]}' AND `status` = 'active'  AND ID IN ( SELECT MAX( pdd.ID ) FROM $tbl_store_document  pdd WHERE pdd.hsid = hsid GROUP BY hsid )  ");
                if(!empty($check_store_document)){
                    // Retuern  if document is already exists
                    if( $check_store_document->executed_by != null && $check_store_document->activated == "true"  ){
                        return array(
                            "status" => "failed",
                            "message" => "This documents is already exists."
                        );
                    }

                    // Reutrn if ducment is already been pending
                    if( $check_store_document->executed_by == null && $check_store_document->activated == "false"  ){
                        return array(
                            "status" => "failed",
                            "message" => "This documents is already been pending."
                        );
                    }
                }
            // End

            // Start MYSQL Transaction
            $wpdb->query("START TRANSACTION");

            // Call upload image script
            $image = TP_Globals_v2::upload_image( $request, $files);

            if ($image['status'] != 'success') {
                return array(
                    "status" => $image['status'],
                    "message" => $image['message']
                );
            }

            // Insert Vehicles documents
            $docs = $wpdb->query("INSERT
                INTO
                    $tbl_store_document
                        ($tbl_store_document_fileds)
                    VALUES
                        ('{$user["stid"]}', '{$image["data"][0]["preview_id"]}', '{$user["types"]}', '{$user["comments"]}') ");
            $docs_id = $wpdb->insert_id;

            // Update hash_id if mover using sha256 algorithm
            $hsid = TP_Globals_v2::generating_pubkey($docs_id, $tbl_store_document, 'hsid', false, 64);

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
