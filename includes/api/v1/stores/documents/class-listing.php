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

    class TP_Store_Listing_Documents {
        public static function listen(){
            return rest_ensure_response( 
                TP_Store_Listing_Documents:: listen_open()
            );
        }

        public static function listen_open (){
            global $wpdb;

            // NOTE : POST 'type' is not required even if its not listen in client it will not show error
            $tp_docs = TP_DOCU_TABLE;
            $table_revs = TP_REVISIONS_TABLE;  
            // declaring table names to variable
         
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
                        "message" => "Please contact your administrator. Verification issues!",
                );
            }

            // Step 3: Start mysql query
            $sql = "SELECT
                doc.ID,
                (SELECT child_val FROM tp_revisions WHERE ID = str.title) AS store,
            IF
                ( (SELECT child_val FROM tp_revisions WHERE  ID =  doc.`status`) = 1, 'Active', 'Inactive' ) AS `status`,
                doc.doctype,
                doc.date_created 
            FROM
                tp_documents doc
            INNER JOIN 
                tp_stores str ON str.ID = doc.stid
            ";
            
            // Step 4: Filter Contact type (OPTIONAL)
            isset($_POST['stid']) ? $stid = $_POST['stid']: $stid = NULL; 
            isset($_POST['doc_type']) ? $doc_type = $_POST['doc_type']: $doc_type = NULL; 
            isset($_POST['doc_id']) ? $doc_id = $_POST['doc_id']: $doc_id = NULL; 
            isset($_POST['status']) ? $sts = $_POST['status'] : $sts = NULL  ;
            (int)$status = $sts == '0'? NULL:($sts == '2'? '0':'1')  ;

            // Step 5: Where clause if needs a filter store id in store query 
            if (isset($_POST['stid']) && $stid != NULL && $stid != '0') {
                $sql .= "WHERE doc.stid = '$stid' ";                    
            }
            
            // Step 6: Where clause if needs a filter type in documents query 
            if (isset($_POST['doc_type'])  && $doc_type != NULL ) {
                // Validate if Contact type is valid
                if ($doc_type != 'dti_registration' 
                    && $doc_type != 'barangay_clearance' 
                    && $doc_type != 'lease_contract' 
                    && $doc_type != 'community_tax' 
                    && $doc_type != 'occupancy_permit' 
                    && $doc_type != 'sanitary_permit' 
                    && $doc_type != 'fire_permit' 
                    && $doc_type != 'mayors_permit') {
                    return array(
                        "status" => "failed",
                        "message" => "Document type is not valid."
                    );
                }
                if (!isset($_POST['stid'])){
                    $sql .= " WHERE doc.doctype = '$doc_type' "; 
                }
                else{   
                    $sql .= " AND doc.doctype = '$doc_type' "; 
                }
            }

            // Step 7: Where clause if needs a filter status in contacts query 
            if (isset($_POST['status']) && $status != NULL ) {
                if (  $status != NULL && $status != '0' && $type != NULL || $type != '0'  ) {
                    if (!isset($_POST['stid'])){
                        $sql .= " WHERE (SELECT child_val FROM tp_revisions WHERE  ID =  doc.`status`) = '$status' "; 
                    }
                    else{   
                        $sql .= " AND (SELECT child_val FROM tp_revisions WHERE  ID =  doc.`status`) = '$status' "; 
                    }                   
                }
            }

            // Step 8: Where clause if needs a filter document id in documents query 
            if (isset($_POST['doc_id']) && $doc_id != NULL && $doc_id != '0'  ) {
                if (!isset($_POST['stid'])){
                    $sql .= " WHERE doc.`ID` = '$doc_id' "; 
                }
                else{   
                    $sql .= " AND  doc.`ID` = '$doc_id' "; 
                }  
            }
            
            // Step 9: return query
            $result = $wpdb->get_results($sql);

            // Step 10: Check if no rows found
            if (!$result) {
                return array(
                    "status" => "success",
                    "data" => [],
                );
            }else{
                return array(
                    "status" => "success",
                    "data" => $result
                );
            }
            
        }

    }