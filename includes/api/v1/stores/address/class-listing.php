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

    class TP_Store_Listing_Address {
        public static function listen(){
            return rest_ensure_response( 
                TP_Store_Listing_Address:: listen_open()
            );
        }

        public static function listen_open (){
            global $wpdb;

            // NOTE : POST 'type' is not required even if its not listen in client it will not show error

            // declaring table names to variable
            $table_store = TP_STORES_TABLE;
            $table_revisions = TP_REVISIONS_TABLE;
            $table_contacts = DV_CONTACTS_TABLE;
            $table_brgy = DV_BRGY_TABLE;
            $table_city = DV_CITY_TABLE;
            $table_province = DV_PROVINCE_TABLE;
            $table_country = DV_COUNTRY_TABLE;
            $table_dv_revisions = DV_REVS_TABLE;
            $table_add = DV_ADDRESS_TABLE;

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

            // Step 6: Start mysql query
            $sql = "SELECT
                `add`.ID,
                `add`.stid,
                IF(`add`.types = 'business', 'Business', 'Office' )as `type`,
                ( SELECT `child_val` FROM $table_revisions WHERE ID = ( SELECT `title` FROM tp_stores WHERE ID = `add`.stid ) ) as store_name,
                ( SELECT child_val FROM $table_dv_revisions WHERE id = `add`.street ) AS street,
                ( SELECT brgy_name FROM $table_brgy WHERE ID = ( SELECT child_val FROM $table_dv_revisions WHERE id = `add`.brgy ) ) AS brgy,
                ( SELECT city_name FROM $table_city WHERE city_code = ( SELECT child_val FROM $table_dv_revisions WHERE id = `add`.city ) ) AS city,
                ( SELECT prov_name FROM $table_province WHERE prov_code = ( SELECT child_val FROM $table_dv_revisions WHERE id = `add`.province ) ) AS province,
                ( SELECT country_name FROM $table_country WHERE id = ( SELECT child_val FROM $table_dv_revisions WHERE id = `add`.country ) ) AS country,
                IF (( select child_val from $table_dv_revisions where id = `add`.`status` ) = 1, 'Active' , 'Inactive' ) AS `status`,
                `add`.date_created
            FROM
                $table_add `add`
            ";
            
            // Filter Address type (OPTIONAL)
            isset($_POST['type']) ? $type = $_POST['type']: $type = NULL; 
            isset($_POST['addr']) ? $address_id = $_POST['addr']: $address_id = NULL; 
            isset($_POST['stid']) ? $store_id = $_POST['stid']: $store_id = NULL; 
            isset($_POST['status']) ? $sts = $_POST['status'] : $sts = NULL  ;
            (int)$status = $sts == '0'? NULL:($sts == '2'? '0':'1')  ;

            if (isset($_POST['addr'])) {
                if($address_id != NULL && $address_id != '0'){
                    if ( !is_numeric($address_id) ) {
                        return array(
                            "status" => "failed",
                            "message" => "ID is not in valid format."
                        );
                    }

                    $sql .=" WHERE `add`.ID = '$address_id'";
                }
            }
            
            if (isset($_POST['type'])) {

                if ($type != NULL) {
            
                    if ($type != 'business' && $type != 'office' ) {
                        return array(
                            "status" => "failed",
                            "message" => "Invalid type of address."
                        );

                    }else{

                        if ($address_id !== NULL && $address_id != '0' ) {
                            $sql .= " AND `add`.types = '$type' ";
                            
                        }else{
                            $sql .= " WHERE `add`.types = '$type' ";
                            
                        }

                    }
                }
            }

            if (isset($_POST['stid'])) {
                
                if ($store_id !== NULL) {
                    
                    if ( $store_id != '0' && $type != '0' && isset($type) || $address_id !== NULL && $address_id !='0'  ) {
                        
                        
                        if ( $address_id !== NULL || $address_id != '0' ) {
                            // validate stid
                            if($store_id != '0' && $type !== NULL && $address_id !== NULL ){
                                // Check if Store id is existed
                                $check_store = $wpdb->get_row("SELECT ID FROM tp_stores  WHERE ID = '$store_id'  AND  (SELECT `child_val` FROM tp_revisions WHERE ID = tp_stores.`status`  ) = 1");
                                if (!$check_store) {
                                    return array(
                                        "status" => "failed",
                                        "message" => "This store does not exists."
                                    );
                                }

                                $sql .= " AND `add`.stid = '$store_id' ";

                            }
                            $sql .= " AND `add`.stid = '$store_id' ";

                        }
                        
                    }else{

                        if ($store_id != '0' ) {

                            $sql .= " WHERE `add`.stid = '$store_id' ";

                        }

                    }
                }
            }

            if (isset($_POST['status']) && $_POST['status'] != '0' ) {
                if ($status != NULL && $store_id != NULL && $store_id != '0' || $type != NULL || $address_id != NULL && $address_id != '0' ) {
                    $sql .= " AND ( select child_val from $table_dv_revisions where id = `add`.`status` ) = '$status'";
                    
                }else{
                    $sql .= " WHERE ( select child_val from $table_dv_revisions where id = `add`.`status` ) = '$status'";
                }
            }

            // return $sql;
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