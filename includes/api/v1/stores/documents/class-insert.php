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

	class TP_Insert_Documents {

        public static function listen($request){
            return rest_ensure_response( 
                TP_Insert_Documents::insert_document($request)
            );
        }
                                                        
        public static function insert_document($request){
            
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

            // Step3 : Sanitize if all variables at POST
            if ( !isset($_POST['doc_type']) 
                || !isset($_POST['stid']) ) {
				return array(
					"status" => "unknown",
					"message" => "Please contact your administrator. Request unknown!",
                );
                
            }
                
            // Step4 : Check if all variables is not empty 
            if ( empty($_POST['doc_type']) 
                || empty($_POST['stid']) ) {
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
            //$doc_prev = $_POST['doc_prev'];
            $date_created = TP_Globals::date_stamp();

            // Step5 : Start Query
            $wpdb->query("START TRANSACTION");
            
            $result = DV_Globals::upload_image( $request); // upload image
            $doc_prev = substr($result['data'], 45); // get /year/month/filename to save in database
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
           
            // Step6 : Check if query has result
            if ($insert2 < 1 || $insert1 < 1 || $update < 1 || !$result) {
                $wpdb->query("ROLLBACK");
                // Step7 : return result
                return array(
                    "status" => "failed",
                    "message" => "Please contact your administrator. Submitting document failed."
                );
            }else {
                //  Step8 : Return Success
                $wpdb->query("COMMIT");
                return array(
                    "status" => "success",
                    "message" => "Document Successfully Submited."
                );
            }
        }

        
    }