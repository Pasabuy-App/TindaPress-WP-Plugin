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
	class TP_Update_Documents {

        public static function listen($request){
            return rest_ensure_response( 
                TP_Update_Documents::insert_document($request)
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
            if ( !isset($_POST['doc_type']) 
                || !isset($_POST['stid'])  
                || !isset($_POST['doc_id']) ) {
				return array(
					"status" => "unknown",
					"message" => "Please contact your administrator. Request unknown!",
                );
                
            }

            // Step 4: Check if all variables is not empty 
            if ( empty($_POST['doc_type']) 
                || empty($_POST['stid']) 
                || empty($_POST['doc_id'])) {
                return array(
                    "status" => "failed",
                    "message" => "Required fileds cannot be empty.",
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
            $doc_id = $_POST['doc_id'];
            $date_created = TP_Globals::date_stamp();

            // Step 5: Check document if exist using document id, store id and document type
            $check_doc =  $wpdb->get_row("SELECT ID, (SELECT child_val FROM $table_revs WHERE ID = $tp_docs.status) AS status FROM $tp_docs WHERE ID = $doc_id  AND stid = '$stid' AND doctype = '$doc_type' ");
            if (!$check_doc || $check_doc->status === '0') {
                return array(
                    "status" => "failed",
                    "message" => "This document does not exist."
                );
            }

            // Step 6: Start Query
            $wpdb->query("START TRANSACTION");

            $result = DV_Globals::upload_image( $request); // upload image
            $doc_prev = substr($result['data'], 45); // get /year/month/filename to save in database

            $insert = $wpdb->query("INSERT INTO $table_revs $revs_fields VALUES ('documents', $doc_id, 'preview', '$doc_prev', '$wpid', '$date_created' ) ");
            $last_id_doc = $wpdb->insert_id;

            $update = $wpdb->query("UPDATE $tp_docs SET preview = $last_id_doc WHERE ID = $doc_id ");

            // Step 7: Check if query has result
            if ($result < 1 || $insert < 1 || $update < 1) {
                $wpdb->query("ROLLBACK");
                // Step 8: return result
                return array(
                    "status" => "failed",
                    "message" => "An error occurred while submitting data to server."
                );
            }else {
                //  Step 9: Return Success
                $wpdb->query("COMMIT");
                return array(
                    "status" => "success",
                    "message" => "Data has been updated successfully."
                );
            }
        }
        
    }