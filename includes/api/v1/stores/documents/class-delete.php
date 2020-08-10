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

            // Put all request to variable
            $stid = $_POST['stid'];
            $docid = $_POST['docid'];
            $tp_docs = TP_DOCU_TABLE;
            
            // Step 1 : Verfy if Datavice Plugin is Activated
			if (TP_Globals::verifiy_datavice_plugin() == false) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Plugin Missing!",
                );
			}
            
            // Step2 : Check if wpid and snky is valid
            if (DV_Verification::is_verified() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Verification Issues!",
                    )
                );
            }

            // Step3 : Sanitize all Request
            if (!isset($_POST['stid']) 
                || !isset($_POST['docid']) ) {
				return array(
					"status" => "unknown",
					"message" => "Please contact your administrator. Request unknown!",
                );
            }

            // Step4: Check if ID is in valid format (integer)
			if ( !is_numeric($_POST['docid'])) {
				return array(
					"status" => "failed",
					"message" => "Please contact your administrator. ID not in valid format.",
                );
            }
            
            // Step5 : Sanitize all Request if emply
            if ( empty($_POST['stid']) 
                || empty($_POST['docid']) ) {
				return array(
						"status" => "unknown",
						"message" => "Required fields cannot be empty.",
                );
            }

            $result = $wpdb->query("UPDATE $tp_docs SET `status` = 'inactive' WHERE ID = $docid AND stid = $stid ");

            //  Step6 : Return Success
            if ($result < 1 ) {
                return array(
					"status" => "failed",
					"message" => "Please Contact your administrator. Deletion failed.",
                );
            }else {
                return array(
					"status" => "success",
					"message" => "Successfully deleted.",
                );
            }

        }

    }