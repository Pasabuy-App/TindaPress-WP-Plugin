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

        public static function listen(){
            return rest_ensure_response( 
                TP_Insert_Documents::insert_document()
            );
        }
                                                        
        public static function insert_document(){
            
            global $wpdb;
            
            // Step1 : Verify if datavicce plugin is active
			if (TP_Globals::verifiy_datavice_plugin() == false) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Plugin Missing!",
                );
			}
            
<<<<<<< Updated upstream
            // Step2 : Verify if User is in database
			if (DV_Verification::is_verified() == false) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Request Unknown!",
=======
            // Step2 : Check if wpid and snky is valid
            if (DV_Verification::is_verified() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Verification Issues!",
                    )
>>>>>>> Stashed changes
                );
            }

            // Step3 : Sanitize if all variables at POST
            if ( !isset($_POST['doc_type']) 
                || !isset($_POST['stid']) 
                || !isset($_POST['doc_prev']) ) {
				return array(
					"status" => "unknown",
					"message" => "Please contact your administrator. Request unknown!",
                );
                
            }

            // Step6 : Check if all variables is not empty 
            if ( empty($_POST['doc_type']) 
                || empty($_POST['stid']) 
                || empty($_POST['doc_prev']) ) {
                return array(
                    "status" => "unknown",
                    "message" => "Required fileds cannot be empty.",
                );
            }

            // Declare variables
            $tp_docs = TP_DOCU_TABLE;
            $doc_fields = DOCS_FIELDS;
            $revs_fields = REVS_FIELDS;
            $docs = DOCUMENTS;
            $prev = PREVIEW;
<<<<<<< Updated upstream
            $table_revs = TP_REVISIONS_TABLE;            
            
=======
            $table_revs = TP_REVISIONS_TABLE;           
>>>>>>> Stashed changes
            $doc_type = $_POST['doc_type'];
            $stid = $_POST['stid'];
            $doc_prev = $_POST['doc_prev'];

            // Step7 : Start Query
            $wpdb->query("START TRANSACTION");
                $insert1 = $wpdb->query("INSERT INTO $tp_docs ($doc_fields) VALUES ($stid, 0, '$doc_type')");
                    $last_id_doc = $wpdb->insert_id;
                $insert2 = $wpdb->query("INSERT INTO $table_revs ($revs_fields) VALUES ('$docs', $last_id_doc, '$prev', '$doc_prev' ) "); 
                    $last_id_rev = $wpdb->insert_id;
                $update = $wpdb->query("UPDATE $tp_docs SET $prev = $last_id_rev WHERE ID = $last_id_doc ");
           
            // Step8 : Check if query has result
            if ($insert2 < 1 || $insert1 < 1 || $update < 1) {
                $wpdb->query("ROLLBACK");
                // Step9 : return result
                return array(
                    "status" => "unknown",
                    "message" => "Please Contact your administrator. Submiting Document Failed!"
                );
            }else {
                //  Step10 : Return Success
                $wpdb->query("COMMIT");
                return array(
                    "status" => "success",
                    "message" => "Document Successfully Submited!"
                );
            }
        }

        
    }