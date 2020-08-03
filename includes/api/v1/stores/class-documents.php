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

	class TP_Documents {

        public static function add_single_docs(){
            
            global $wpdb;
            
            // Step1 : Verify if datavicce plugin is active
			if (TP_Globals::verifiy_datavice_plugin() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Plugin Missing!",
                    )
                );
			}
            
            // Step2 : Verify if User is in database
			if (TP_Globals::validate_user() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Request Unknown!",
                    )
                );
            }

            // Step3 : Sanitize if all variables at POST
            if (!isset($_POST["wpid"]) 
                || !isset($_POST["snky"]) 
                || !isset($_POST['doc_type']) 
                || !isset($_POST['stid']) 
                || !isset($_POST['doc_prev']) ) {
				return rest_ensure_response( 
					array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Request unknown!",
					)
                );
                
            }


            // Step 4: Check if ID is in valid format (integer)
			if (!is_numeric($_POST["wpid"])) {
				return rest_ensure_response( 
					array(
						"status" => "failed",
						"message" => "Please contact your administrator. ID not in valid format!",
					)
                );
                
			}

			// Step 5: Check if ID exists
			if (!get_user_by("ID", $_POST['wpid'])) {
				return rest_ensure_response( 
					array(
						"status" => "failed",
						"message" => "User not found!",
					)
                );
                
            }

            // Step6 : Check if all variables is not empty 
            if (empty($_POST["wpid"]) 
                || empty($_POST["snky"]) 
                || empty($_POST['doc_type']) 
                || empty($_POST['stid']) 
                || empty($_POST['doc_prev']) ) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Required fileds cannot be empty.",
                    )
                );
            }

            // Declare variables
            $tp_docs = TP_DOCU_TABLE;
            $doc_fields = DOCS_FIELDS;
            $revs_fields = REVS_FIELDS;
            $docs = DOCUMENTS;
            $prev = PREVIEW;
            $table_revs = TP_REVISION_TABLE;            
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
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please Contact your administrator. Submiting Document Failed!"
                    )
                );
            }else {
                //  Step10 : Return Success
                $wpdb->query("COMMIT");
                return rest_ensure_response( 
                    array(
                        "status" => "success",
                        "message" => "Document Successfully Submited!"
                    )
                );
            }
        }

        public static function delete_docs(){
            global $wpdb;
            
            // Step 1 : Verfy if Datavice Plugin is Activated
			if (TP_Globals::verifiy_datavice_plugin() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Plugin Missing!",
                    )
                );
			}
			//step 2: validate User
			if (TP_Globals::validate_user() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Request Unknown!",
                    )
                );
            }

            // Step3 : Sanitize all Request
			if (!isset($_POST["wpid"]) || !isset($_POST["snky"]) || !isset($_POST['stid']) || !isset($_POST['docid']) ) {
				return rest_ensure_response( 
					array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Request unknown!",
					)
                );
                
            }


            // Step 4: Check if ID is in valid format (integer)
			if (!is_numeric($_POST["wpid"]) ||  !is_numeric($_POST['stid']) || !is_numeric($_POST['docid'])) {
				return rest_ensure_response( 
					array(
						"status" => "failed",
						"message" => "Please contact your administrator. ID not in valid format!",
					)
                );
                
            }
            

			// Step 5: Check if ID exists
			if (!get_user_by("ID", $_POST['wpid'])) {
				return rest_ensure_response( 
					array(
						"status" => "failed",
						"message" => "User not found!",
					)
                );
                
            }

            // Step6 : Sanitize all Request if emply
			if (empty($_POST["wpid"]) || empty($_POST["snky"]) || empty($_POST['stid']) || empty($_POST['docid']) ) {
				return rest_ensure_response( 
					array(
						"status" => "unknown",
						"message" => "Required fields cannot be empyty.",
					)
                );
                
            }



            // Put all request to variable
            $stid = $_POST['stid'];

            $docid = $_POST['docid'];

            $tp_docs = TP_DOCU_TABLE;

            $result = $wpdb->query("UPDATE $tp_docs SET `status` = 'inactive' WHERE ID = $docid AND stid = $stid ");

            //  Step7 : Return Success
            if ($result < 1 ) {

                return rest_ensure_response( 
					array(
						"status" => "failed",
						"message" => "Please Contact your Administrator. Deletion failed!",
					)
                );

            }else {
                return rest_ensure_response( 
					array(
						"status" => "success",
						"message" => "Successfully Deleted",
					)
                );

            }

        }
    }