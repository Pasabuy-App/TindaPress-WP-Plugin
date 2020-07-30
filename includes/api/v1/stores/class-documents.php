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
        public static function add_documents(){
            global $wpdb;


			if (TP_Globals::verifiy_datavice_plugin() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Plugin Missing!",
                    )
                );
			}
			
			if (TP_Globals::validate_user() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Request Unknown!",
                    )
                );
            }

            // Step1 : Sanitize all Request
            if (!isset($_POST["wpid"]) 
                || !isset($_POST["snky"])
                || !isset($_POST['dti_reg'])
                || !isset($_POST['brgy_clr'])
                || !isset($_POST['lease_contr'])
                || !isset($_POST['comnty_tax'])
                || !isset($_POST['occt_permit'])
                || !isset($_POST['santy_permit'])
                || !isset($_POST['fire_permit'])
                || !isset($_POST['myrs_permit'])
                // ==============================
                || !isset($_POST['dti_reg_val'])
                || !isset($_POST['brgy_clr_val'])
                || !isset($_POST['lease_contr_val'])
                || !isset($_POST['comnty_tax_val'])
                || !isset($_POST['occt_permit_val'])
                || !isset($_POST['santy_permit_val'])
                || !isset($_POST['fire_permit_val'])
                || !isset($_POST['myrs_permit_val'])
                || !isset($_POST['stid'])
                
                ) {
				return rest_ensure_response( 
					array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Request unknown!",
					)
                );
                
            }


            // Step 2: Check if ID is in valid format (integer)
			if (!is_numeric($_POST["wpid"])) {
				return rest_ensure_response( 
					array(
						"status" => "failed",
						"message" => "Please contact your administrator. ID not in valid format!",
					)
                );
                
			}

			// Step 3: Check if ID exists
			if (!get_user_by("ID", $_POST['wpid'])) {
				return rest_ensure_response( 
					array(
						"status" => "failed",
						"message" => "User not found!",
					)
                );
                
            }


            // Docs type
            $doctype_DTI = $_POST['dti_reg'];
            $doctype_BRGY = $_POST['brgy_clr'];
            $doctype_LEASE = $_POST['lease_contr'];
            $doctype_COMNTY = $_POST['comnty_tax'];
            $doctype_OCCT = $_POST['occt_permit'];
            $doctype_SANTY = $_POST['santy_permit'];
            $doctype_FIRE = $_POST['fire_permit'];
            $doctype_MYRS = $_POST['myrs_permit'];
            // Docs value
            $doctype_DTI_val = $_POST['dti_reg_val'];
            $doctype_BRGY_val = $_POST['brgy_clr_val'];
            $doctype_LEASE_val = $_POST['lease_contr_val'];
            $doctype_COMNTY_val = $_POST['comnty_tax_val'];
            $doctype_OCCT_val = $_POST['occt_permit_val'];
            $doctype_SANTY_val = $_POST['santy_permit_val'];
            $doctype_FIRE_val = $_POST['fire_permit_val'];
            $doctype_MYRS_val = $_POST['myrs_permit_val'];

            $tp_revs = TP_REVISION_TABLE;
            $tp_doc = TP_DOCU_TABLE;
            $stid = $_POST['stid'];


            $doc_fields = DOCS_FIELDS;
            $revs_fields = REVS_FIELDS;

            $child_key = PREVIEW;
            $rev_types = DOCUMENTS;

    

            $wpdb->query("START TRANSACTION");
            // ========== INSERT tp_documents                                                                                  ====================================
                $insert_docs_dti = $wpdb->query("INSERT $tp_doc ($doc_fields) VALUES ($stid, 0, '$doctype_DTI' )");
                    $last_id_docs_dti =$wpdb->insert_id;
                
                $insert_docs_brgy = $wpdb->query("INSERT $tp_doc ($doc_fields) VALUES ($stid, 0, '$doctype_BRGY' )");
                    $last_id_docs_brgy =$wpdb->insert_id;
                
                $insert_docs_lease = $wpdb->query("INSERT $tp_doc ($doc_fields) VALUES ($stid, 0, '$doctype_LEASE' )");
                    $last_id_docs_lease =$wpdb->insert_id;

                $insert_docs_comnty = $wpdb->query("INSERT $tp_doc ($doc_fields) VALUES ($stid, 0, '$doctype_COMNTY' )");
                    $last_id_docs_comnty =$wpdb->insert_id;

                $insert_docs_occ = $wpdb->query("INSERT $tp_doc ($doc_fields) VALUES ($stid, 0, '$doctype_OCCT' )");
                    $last_id_docs_occ =$wpdb->insert_id;

                $insert_docs_santy = $wpdb->query("INSERT $tp_doc ($doc_fields) VALUES ($stid, 0, '$doctype_SANTY' )");
                    $last_id_docs_santy =$wpdb->insert_id;

                $insert_docs_fire = $wpdb->query("INSERT $tp_doc ($doc_fields) VALUES ($stid, 0, '$doctype_FIRE' )");
                    $last_id_docs_fire =$wpdb->insert_id;

                $insert_docs_mayor = $wpdb->query("INSERT $tp_doc ($doc_fields) VALUES ($stid, 0, '$doctype_MYRS' )");
                    $last_id_docs_mayor = $wpdb->insert_id;

            // ========== INSERT tp_revisions                                                                                   ====================================
                $insert_dti = $wpdb->query("INSERT INTO $tp_revs ($revs_fields) VALUES ('$rev_types', $last_id_docs_dti, '$child_key','$doctype_DTI_val' )");
                    $last_id_dti =$wpdb->insert_id;

                $insert_brgy = $wpdb->query("INSERT INTO $tp_revs ($revs_fields) VALUES ('$rev_types', $last_id_docs_brgy, '$child_key','$doctype_BRGY_val' )");
                    $last_id_brgy =$wpdb->insert_id;

                $insert_lease = $wpdb->query("INSERT INTO $tp_revs ($revs_fields) VALUES ('$rev_types', $last_id_docs_lease, '$child_key','$doctype_LEASE_val' )");
                    $last_id_lease =$wpdb->insert_id;

                $insert_comnty = $wpdb->query("INSERT INTO $tp_revs ($revs_fields) VALUES ('$rev_types', $last_id_docs_comnty, '$child_key','$doctype_COMNTY_val' )");
                    $last_id_comnty =$wpdb->insert_id;

                $insert_occpny  = $wpdb->query("INSERT INTO $tp_revs ($revs_fields) VALUES ('$rev_types', $last_id_docs_occ, '$child_key','$doctype_OCCT_val' )");
                    $last_id_occpny =$wpdb->insert_id;

                $insert_sanitry  = $wpdb->query("INSERT INTO $tp_revs ($revs_fields) VALUES ('$rev_types', $last_id_docs_santy, '$child_key','$doctype_SANTY_val' )");
                    $last_id_santy =$wpdb->insert_id;
                    
                $insert_fire      = $wpdb->query("INSERT INTO $tp_revs ($revs_fields) VALUES ('$rev_types', $last_id_docs_fire, '$child_key','$doctype_FIRE_val' )");
                    $last_id_fire =$wpdb->insert_id;

                $insert_mayors     = $wpdb->query("INSERT INTO $tp_revs ($revs_fields) VALUES ('$rev_types', $last_id_docs_mayor, '$child_key','$doctype_MYRS_val' )");
                    $last_id_mayor =$wpdb->insert_id;

            //============== UPDATE                                                                                            ==================================== 
                $update_docs_dti = $wpdb->query("UPDATE $tp_doc SET  $child_key = $last_id_dti WHERE ID = $last_id_docs_dti");

                $update_docs_brgy = $wpdb->query("UPDATE $tp_doc SET  $child_key = $last_id_brgy WHERE ID = $last_id_docs_brgy");

                $update_docs_lease = $wpdb->query("UPDATE $tp_doc SET  $child_key = $last_id_lease WHERE ID = $last_id_docs_lease");

                $update_docs_comnty = $wpdb->query("UPDATE $tp_doc SET  $child_key = $last_id_comnty WHERE ID = $last_id_docs_comnty");

                $update_docs_occ = $wpdb->query("UPDATE $tp_doc SET  $child_key = $last_id_occpny WHERE ID = $last_id_docs_occ");

                $update_docs_santy = $wpdb->query("UPDATE $tp_doc SET  $child_key = $last_id_santy WHERE ID = $last_id_docs_santy");

                $update_docs_fire = $wpdb->query("UPDATE $tp_doc SET  $child_key = $last_id_fire WHERE ID = $last_id_docs_fire");

                $update_docs_mayor = $wpdb->query("UPDATE $tp_doc SET  $child_key = $last_id_mayor WHERE ID = $last_id_docs_mayor");
                
    
            if ($last_id_docs_dti < 1 
                || $last_id_docs_brgy   < 1 
                || $last_id_docs_lease  < 1 
                || $last_id_docs_comnty < 1 
                || $last_id_docs_occ    < 1 
                || $last_id_docs_santy  < 1
                || $last_id_docs_fire   < 1 
                || $last_id_docs_mayor  < 1 
                || $last_id_dti         < 1 
                || $last_id_brgy        < 1 
                || $last_id_lease       < 1 
                || $last_id_comnty      < 1 
                || $last_id_occpny      < 1 
                || $last_id_santy       < 1 
                || $last_id_fire        < 1 
                || $last_id_mayor       < 1
                || $update_docs_dti     < 1
                || $update_docs_brgy    < 1
                || $update_docs_lease   < 1
                || $update_docs_comnty  < 1
                || $update_docs_occ     < 1
                || $update_docs_santy   < 1
                || $update_docs_fire    < 1
                || $update_docs_mayor   < 1
                ) {
                $wpdb->query("ROLLBACK");
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please Contact your Administrator. Contact Deletion Failed!"
                    )
                );
            } else {
                $wpdb->query("COMMIT");
                return rest_ensure_response( 
                    
                    array(
                        "status" => "success",
                        "message" => "Contact updated successfully"
                    )
                );

            }


            

        }

        public static function add_single_docs(){
            
            global $wpdb;
            
			if (TP_Globals::verifiy_datavice_plugin() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Plugin Missing!",
                    )
                );
			}
			
			if (TP_Globals::validate_user() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Request Unknown!",
                    )
                );
            }

            // Step1 : Sanitize all Request
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


            // Step 2: Check if ID is in valid format (integer)
			if (!is_numeric($_POST["wpid"])) {
				return rest_ensure_response( 
					array(
						"status" => "failed",
						"message" => "Please contact your administrator. ID not in valid format!",
					)
                );
                
			}

			// Step 3: Check if ID exists
			if (!get_user_by("ID", $_POST['wpid'])) {
				return rest_ensure_response( 
					array(
						"status" => "failed",
						"message" => "User not found!",
					)
                );
                
            }



            $tp_docs = TP_DOCU_TABLE;
            $doc_fields = DOCS_FIELDS;
            $revs_fields = REVS_FIELDS;
            $docs = DOCUMENTS;
            $prev = PREVIEW;
            $table_revs = TP_REVISION_TABLE;

            
            $doc_type = $_POST['doc_type'];
            $stid = $_POST['stid'];
            $doc_prev = $_POST['doc_prev'];

            $wpdb->query("START TRANSACTION");
                $insert1 = $wpdb->query("INSERT INTO $tp_docs ($doc_fields) VALUES ($stid, 0, '$doc_type')");
                    $last_id_doc = $wpdb->insert_id;
                $insert2 = $wpdb->query("INSERT INTO $table_revs ($revs_fields) VALUES ('$docs', $last_id_doc, '$prev', '$doc_prev' ) "); 
                    $last_id_rev = $wpdb->insert_id;
                $update = $wpdb->query("UPDATE $tp_docs SET $prev = $last_id_rev WHERE ID = $last_id_doc ");
           
            
            if ($insert2 < 1 || $insert1 < 1 || $update < 1) {
                $wpdb->query("ROLLBACK");
                //Step 6: return result
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please Contact your administrator. Submiting Document Failed!"
                    )
                );
            }else {
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
            
            
			if (TP_Globals::verifiy_datavice_plugin() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Plugin Missing!",
                    )
                );
			}
			
			if (TP_Globals::validate_user() == false) {
                return rest_ensure_response( 
                    array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Request Unknown!",
                    )
                );
            }

            // Step1 : Sanitize all Request
			if (!isset($_POST["wpid"]) || !isset($_POST["snky"]) || !isset($_POST['stid']) || !isset($_POST['docid']) ) {
				return rest_ensure_response( 
					array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Request unknown!",
					)
                );
                
            }


            // Step 2: Check if ID is in valid format (integer)
			if (!is_numeric($_POST["wpid"]) ||  !is_numeric($_POST['stid']) || !is_numeric($_POST['docid'])) {
				return rest_ensure_response( 
					array(
						"status" => "failed",
						"message" => "Please contact your administrator. ID not in valid format!",
					)
                );
                
			}

			// Step 3: Check if ID exists
			if (!get_user_by("ID", $_POST['wpid'])) {
				return rest_ensure_response( 
					array(
						"status" => "failed",
						"message" => "User not found!",
					)
                );
                
            }

            $stid = $_POST['stid'];

            $docid = $_POST['docid'];

            $tp_docs = TP_DOCU_TABLE;

            $result = $wpdb->query("UPDATE $tp_docs SET `status` = 'inactive' WHERE ID = $docid AND stid = $stid ");

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