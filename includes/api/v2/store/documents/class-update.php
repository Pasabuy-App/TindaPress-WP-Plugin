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

  	class TP_Store_Update_Docs_v2 {

        public static function listen(WP_REST_Request $request){
            return rest_ensure_response(
                self::listen_open($request)
            );
        }

        public static function catch_post(){
            $curl_user = array();

            $curl_user['docid'] = $_POST['docid'];
            $curl_user['wpid'] = $_POST['wpid'];

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

            $validate = MP_Globals_v2::check_listener($user);
            if ($validate !== true) {
                return array(
                    "status" => "failed",
                    "message" => "Required fileds cannot be empty "."'".ucfirst($validate)."'"."."
                );
            }

            // Check store docs is already exists
                $check_store_document = $wpdb->get_row("SELECT * FROM $tbl_store_document WHERE hsid = '{$user["docid"]}' AND `status` = 'active'  ");
                if (empty($check_store_document)) {
                    return array(
                        "status" => "failed",
                        "message" => "This store document does not exist.",
                    );
                }
            // End

            isset($_POST['types']) && !empty($_POST['types'])? $user['types'] =  $_POST['types'] :  $user['types'] = $check_store_document->types ;
            isset($_POST['comments']) && !empty($_POST['comments'])? $user['comments'] =  $_POST['comments'] :  $user['comments'] = $check_store_document->comments ;

            // Start MYSQL Transaction
            $wpdb->query("START TRANSACTION");

            // Optional Upload for avatar and banner
            if (isset($files['preview']) && !empty($files['preview']['name']) ) {

                $image = TP_Globals_v2::upload_image( $request, $files);
                if ($image['status'] != 'success') {
                    return array(
                        "status" => $image['status'],
                        "message" => $image['message']
                    );
                }

                $files['preview'] =  $image['data'][0]['preview_id'];

            }else{

                $files['preview'] = $check_store_document->preview;

            }

            // Insert Vehicles documents
            $docs = $wpdb->query("INSERT
                INTO
                    $tbl_store_document
                        (`hsid`, $tbl_store_document_fileds, `status`)
                    VALUES
                        ('$check_store_document->hsid', '$check_store_document->stid', '{$files["preview"]}', '{$user["types"]}', '{$user["comments"]}', '$check_store_document->status' ) ");
            $docs_id = $wpdb->insert_id;

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
                    "message" => "Data has been updated sucessfully."
                );
            }
        }
    }