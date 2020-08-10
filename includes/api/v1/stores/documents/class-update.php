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

	class TP_Update_Documents {

        public static function listen(){
            return rest_ensure_response( 
                TP_Update_Documents::insert_document()
            );
        }
                                                        
        public static function insert_document(){
            
            global $wpdb;

            // Step1 : Verify if datavicce plugin is active
			if (TP_Globals::verify_datavice_plugin() == false) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Plugin Missing!",
                );
			}
            
            // Step2 : Verify if User is in database
			if (DV_Verification::is_verified() == false) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Request Unknown!",
                );
            }

            // Step3 : Sanitize if all variables at POST
            if ( !isset($_POST['doc_type']) 
                || !isset($_POST['stid']) 
                || !isset($_POST['doc_prev']) 
                || !isset($_POST['doc_id']) ) {
				return array(
					"status" => "unknown",
					"message" => "Please contact your administrator. Request unknown!",
                );
                
            }

            // Step6 : Check if all variables is not empty 
            if ( empty($_POST['doc_type']) 
                || empty($_POST['stid']) 
                || empty($_POST['doc_prev']) 
                || empty($_POST['doc_id'])) {
                return array(
                    "status" => "unknown",
                    "message" => "Required fileds cannot be empty.",
                );
            }
            
           $user = TP_Update_Documents::catch_post();

            // Declare variables
            $tp_docs = TP_DOCU_TABLE;
            $doc_fields = DOCS_FIELDS;
            $revs_fields = TP_REVISION_FIELDS;
            $docs = DOCUMENTS;
            $prev = PREVIEW;
            $table_revs = TP_REVISIONS_TABLE;            
            
            $doc_type = $user['doc_type'];
            $stid = $user['store_id'];
            $doc_prev = $user['doc_prev'];
            $doc_id = $user['doc_id'];

             $check_doc =  $wpdb->get_row("SELECT * FROM $tp_docs WHERE ID = $doc_id  ");
            if ($check_doc->ID < 1 || $check_doc->doctype == $doc_type || $check_doc->stid == $stid ) {
                return array(
                    "status" => "failed",
                    "message" => "This document does not exist."
                );
            }

            // Step7 : Start Query
            $wpdb->query("START TRANSACTION");

                $results = $wpdb->query("UPDATE $table_revs SET `child_val` = '{$user['doc_prev']}' WHERE ID = $check_doc->preview   ");

            // Step8 : Check if query has result
            if ($results < 1) {
                $wpdb->query("ROLLBACK");
                // Step9 : return result
                return array(
                    "status" => "failed",
                    "message" => "An error occurred while submitting data to server."
                );

            }else {
                //  Step10 : Return Success
                $wpdb->query("COMMIT");
                return array(
                    "status" => "success",
                    "message" => "Document edited successfully."
                );

            }
        }

        // Catch Post 
        public static function catch_post()
        {
            $cur_user = array();
            $cur_user['created_by'] = $_POST['wpid'];
            $cur_user['store_id']   = $_POST['stid'];
            $cur_user['doc_id']   = $_POST['doc_id'];
            
            $cur_user['doc_type']   = $_POST['doc_type'];
            $cur_user['doc_prev']   = $_POST['doc_prev'];

            return  $cur_user;
        }
        
    }