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
    class TP_Insert_Documents {

        public static function listen($request){
            return rest_ensure_response(
                self::insert_document($request)
            );
        }

        public static function insert_document($request){

            global $wpdb;

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

            // Step 3: Sanitize if all variables at POST
            if ( !isset($_POST['doc_type']) || !isset($_POST['stid']) ) {
				return array(
					"status" => "unknown",
					"message" => "Please contact your administrator. Request unknown!",
                );

            }

            // Step 4: Check if all variables is not empty
            if ( empty($_POST['doc_type']) || empty($_POST['stid']) ) {
                return array(
                    "status" => "failed",
                    "message" => "Required fileds cannot be empty.",
                );
            }

            if ($_POST['doc_type'] != 'dti_registration'
            && $_POST['doc_type'] != 'barangay_clearance'
            && $_POST['doc_type'] != 'lease_contract'
            && $_POST['doc_type'] != 'community_tax'
            && $_POST['doc_type'] != 'occupancy_permit'
            && $_POST['doc_type'] != 'sanitary_permit'
            && $_POST['doc_type'] != 'fire_permit'
            && $_POST['doc_type'] != 'mayors_permit') {
            return array(
                "status" => "failed",
                "message" => "Document type is not valid."
            );
        }

            // Declare variables
            $tp_docs = TP_DOCU_TABLE;
            $doc_fields = DOCS_FIELDS;
            $table_revs = TP_REVISIONS_TABLE;
            $revs_fields = TP_REVISION_FIELDS;
            $wpid = $_POST['wpid'];
            $doc_type = $_POST['doc_type'];
            $stid = $_POST['stid'];
            $date_created = TP_Globals::date_stamp();

            // Step 5: Check document if exist using store id and document type
            $check_doc =  $wpdb->get_row("SELECT doctype, (SELECT child_val FROM $table_revs WHERE ID = $tp_docs.status) AS status  FROM $tp_docs WHERE stid = '$stid' AND doctype = '$doc_type'  ");
            if (!empty($check_doc)) {
                if ($check_doc->doctype === $doc_type || $check_doc->status === '1' ) {
                    return array(
                        "status" => "failed",
                        "message" => "This document has already exist."
                    );
                }
            }

            // Step 6: Start Query
            $wpdb->query("START TRANSACTION");


            $files = $request->get_file_params();

            if ( !isset($files['img'])) {
				return  array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Request Unknown!",
                );
            }

            $result = DV_Globals::upload_image( $request,$files ); // upload image
            $doc_prev = $result['data']; // get /year/month/filename to save in database
            $child_key = array( //stored in array
                'preview'   =>$doc_prev,
                'status'    =>'1'
            );

            $insert1 = $wpdb->query("INSERT INTO $tp_docs ($doc_fields) VALUES ($stid, 0, '$doc_type')");
                $last_id_doc = $wpdb->insert_id;

            $id = array();
            foreach ( $child_key as $key => $child_val) {
                $insert2 = $wpdb->query("INSERT INTO $table_revs $revs_fields VALUES ('documents', $last_id_doc, '$key', '$child_val', '$wpid', '$date_created' ) ");
                $id[] = $wpdb->insert_id;
            }

            $update = $wpdb->query("UPDATE $tp_docs SET preview = $id[0], status = '$id[1]', date_created = '$date_created' WHERE ID = $last_id_doc ");

            // Step 7: Check if query has result
            if ($insert2 < 1 || $insert1 < 1 || $update < 1 || !$result) {
                $wpdb->query("ROLLBACK");
                return array(
                    "status" => "failed",
                    "message" => "Please contact your administrator. Submitting document failed."
                );
            }else {
                $wpdb->query("COMMIT");
                return array(
                    "status" => "success",
                    "message" => "Data has been added successfully."
                );
            }
        }
    }