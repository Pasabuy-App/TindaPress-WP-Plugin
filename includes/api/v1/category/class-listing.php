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
    class TP_Category_Listing {

        public static function listen(){
            return rest_ensure_response( 
                TP_Category_Listing:: list_type()
            );
        }
        
        public static function list_type(){

            global $wpdb;
            $table_revs = TP_REVISIONS_TABLE;
            $table_categories = TP_CATEGORIES_TABLE;

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
                        "message" => "Please contact your administrator. verification issues!",
                );
                
            }
            
            if (!isset($_POST['stid']) || !isset($_POST['type']) || !isset($_POST['status'])  ) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your admininstrator. Missing paramiters!"
                );
            }

            $stores_count = "IF((  SELECT COUNT(ID) FROM tp_stores  WHERE ctid = cat.ID GROUP BY ctid) IS NULL, '0', (  SELECT COUNT(ID) FROM tp_stores  WHERE ctid = cat.ID GROUP BY ctid)  ) AS total, ";
            $products_count = "IF((  SELECT COUNT(ID) FROM tp_products  WHERE ctid = cat.ID GROUP BY ctid) IS NULL, '0', (  SELECT COUNT(ID) FROM tp_products  WHERE ctid = cat.ID GROUP BY ctid)  ) AS total, ";

            $counting = (isset($_POST['stid']) && (int)$_POST['stid'] > 0) == true ? $products_count : $stores_count;

            $sql = "SELECT   
                cat.ID,
                cat.types, ".$counting."
                ( SELECT rev.child_val FROM $table_revs rev WHERE ID = cat.title ) AS title,
                ( SELECT rev.child_val FROM $table_revs rev WHERE ID = cat.info ) AS info,
            IF( ( SELECT rev.child_val FROM $table_revs rev WHERE ID = cat.`status` ) = 1, 'Active','Inactive' ) AS `status` 
            FROM
                $table_categories cat ";
                
            $stid = $_POST['stid'];
            $status = $_POST["status"];

            if ( isset($_POST['stid']) && (int)$_POST['stid'] > 0  ) {
                $sql .= "WHERE cat.`stid` = '$stid' AND cat.types = 'product' ";
            }else{
                if ( isset($_POST['type']) && (int)$_POST['type'] > 0) {
                    $type = $_POST['type'] == '1'? 'store':'product'; 
                    $sql .= "WHERE cat.types = '$type' ";
                }
            }

            if (isset($_POST['status']) && $_POST['status'] > 0 ) {

                if(((isset($_POST['stid']) && $_POST['stid'] > 0) || (isset($_POST['type']) && $_POST['type'] > 0)) ) {
                    $sql .= "AND ";
                } else {
                    $sql .= "WHERE ";
                }

                $status = (int)$_POST['status'] >= 2 ? 0 : 1;
                $sql .= "( SELECT rev.child_val FROM tp_revisions rev WHERE ID = cat.`status` ) = $status  ";
            }
            
            $results =  $wpdb->get_results($sql);
            if (!$results) {
                return array(
                    "status" => "failed",
                    "message" => "No results found",
                );
            } else {
                return array(
                    "status" => "success",
                    "data" => $results,
                );
            }
        
        }

        public static function catch_post(){
            $cur_user = array();

            $cur_user["store_id"] = $_POST["stid"];
            $cur_user["type"]     = $_POST["type"];
            $cur_user["status"]   = $_POST["status"];

            return  $cur_user;
        }
    }