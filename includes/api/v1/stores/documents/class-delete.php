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

	class TP_Delte_Documents {

		public static function listen(){
            return rest_ensure_response( 
                TP_Delte_Documents::delete_document()
            );
        }

        public static function delete_document(){

            global $wpdb;

            // Step1 : Check if prerequisites plugin are missing
            $plugin = TP_Globals::verify_prerequisites();
            if ($plugin !== true) {
                return array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. ".$plugin." plugin missing!",
                );
            }
           
            // Step2 : Validate user
            if (DV_Verification::is_verified() == false) {
                return array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Verification Issues!",
                );
            }

            // Step3 : Sanitize all Request
            if (!isset($_POST['stid']) 
                || !isset($_POST['doc_id']) ) {
				return array(
					"status" => "unknown",
					"message" => "Please contact your administrator. Request unknown!",
                );
            }
            
            // Step5 : Sanitize all Request if emply
            if ( empty($_POST['stid']) 
                || empty($_POST['doc_id']) ) {
				return array(
						"status" => "failed",
						"message" => "Required fields cannot be empty.",
                );
            }

            // Put all request to variable
            $wpid = $_POST['wpid'];
            $stid = $_POST['stid'];
            $doc_id = $_POST['doc_id'];
            $date_created = TP_Globals::date_stamp();
            $tp_docs = TP_DOCU_TABLE;
            $doc_fields = DOCS_FIELDS;
            $table_revs = TP_REVISIONS_TABLE;  
            $revs_fields = TP_REVISION_FIELDS; 

            // Step5 : Check document if exist using document id, store id and document type
            $check_doc =  $wpdb->get_row("SELECT ID, (SELECT child_val FROM $table_revs WHERE ID = $tp_docs.status) AS status FROM $tp_docs WHERE ID = $doc_id  AND stid = '$stid' ");
            if (!$check_doc || $check_doc->status === '0') {
                return array(
                    "status" => "failed",
                    "message" => "This document does not exist."
                );
            } 

            $insert = $wpdb->query("INSERT INTO $table_revs $revs_fields VALUES ('documents', $doc_id, 'status', '0', '$wpid', '$date_created' ) ");
            $last_id_doc = $wpdb->insert_id;

            $update = $wpdb->query("UPDATE $tp_docs SET status = $last_id_doc WHERE ID = $doc_id ");

            //$result = $wpdb->query("UPDATE $tp_docs SET `status` = 'inactive' WHERE ID = $docid AND stid = $stid ");

            //  Step6 : Return Success
            if ($insert < 1 || $update < 1 ) {
                return array(
					"status" => "failed",
                    "message" => "An error occurred while submitting data to server."
                );
            }else {
                return array(
					"status" => "success",
                    "message" => "Data has been deleted successfully."
                );
            }

        }

    }