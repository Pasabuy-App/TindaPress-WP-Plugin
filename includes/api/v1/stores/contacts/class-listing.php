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

    class TP_Store_Listing_Contacts {
        public static function listen(){
            return rest_ensure_response( 
                TP_Store_Listing_Contacts:: listen_open()
            );
        }

        public static function listen_open (){

            // 2nd Initial QA 2020-08-24 8:05 PM - Miguel
            global $wpdb;

            // NOTE : POST 'type' is not required even if its not listen in client it will not show error
            $dv_contacts  = DV_CONTACTS_TABLE;
            $dv_revisions  = DV_REVS_TABLE;
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

            if (!isset($_POST['stid'])) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Missing paramiters!",
                );
            }

            $stid = $_POST['stid'];

            // Step 6: Start mysql query
            $sql = "SELECT
                dc.ID,
                dc.stid,
                IF ( dc.`status` = 1, 'Active', 'Inactive' ) as `status`,
                dc.types,
                dr.child_val AS `value`,
                dc.date_created 
            FROM
                dv_contacts dc
                INNER JOIN dv_revisions dr ON dr.ID = dc.revs 
            WHERE
                dc.`stid` = '$stid'
            ";
            
            // Filter Contact type (OPTIONAL)
            isset($_POST['type']) ? $type = $_POST['type']: $type = NULL; 
            isset($_POST['cid']) ? $contact_id = $_POST['cid']: $contact_id = NULL; 
            isset($_POST['status']) ? $sts = $_POST['status'] : $sts = NULL  ;
            (int)$status = $sts == '0'? NULL:($sts == '2'? '0':'1')  ;
            
            // Where clause if needs a filter type in contacts query 
            if (isset($_POST['type'])  ) {

                if ($type != NULL) {
                    // Validate if Contact type is valid
                    if ($type != 'phone' && $type != 'email') {
                        return array(
                            "status" => "failed",
                            "message" => "Contact type is not valid."
                        );

                    }else{
                        $sql .= " AND dc.`types` = '$type' ";
                        
                    }
                }
            }

            // Where clause if needs a filter status in contacts query 
            if (isset($_POST['status']) && $status != NULL ) {
                
                if (  $status != NULL && $status != '0' && $type != NULL || $type != '0'  ) {

                    $sql .= " AND dc.`status` = '$status' ";                    
                }
            }

            // Where clause if needs a filter contact id in contacts query 
            if (isset($_POST['cid']) && $contact_id != NULL && $contact_id != '0'  ) {
                
                $sql .= " AND dc.`ID` = '$contact_id' ";                    
                
            }
            
            // return query
            $result = $wpdb->get_results($sql);

            // Step 7: Check if no rows found
            if (!$result) {
                return array(
                    "status" => "success",
                    "message" => "No results found."
                );

            }else{

                return array(
                    "status" => "success",
                    "data" => $result
                );
            }
        }
    }