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
    class TP_List_Variants {

        public static function listen(){
            return rest_ensure_response( 
                TP_List_Variants:: list_variants()
            );
        }

        public static function list_variants(){
            
            global $wpdb;
            $table_variants = TP_VARIANTS_TABLE;
            $table_revs = TP_REVISIONS_TABLE;
            $rev_fields = TP_REVISION_FIELDS;
            $variants_fields = TP_VARIANTS_FIELDS;
            
            //Step1 : Check if prerequisites plugin are missing
            $plugin = TP_Globals::verify_prerequisites();
            if ($plugin !== true) {
                return array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. ".$plugin." plugin missing!",
                );
            }

            // Step2 : Check if wpid and snky is valid
            if (DV_Verification::is_verified() == false) {
                return array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Verification Issues!",
                );
            }

            isset($_POST['pdid']) ? $product_id = $_POST['pdid'] : $product_id = NULL;

            if ($product_id == NULL || $product_id == 0) {
                $where = '';
                
            } else {
                $where = "AND `pdid` = $product_id";
            }
            
            $get_parent = $wpdb->get_results("SELECT `ID` FROM $table_variants WHERE `parent_id` = 0 $where");
            


            foreach ($get_parent as $parent_row => $value) {
                $variance_id = $value->ID;
                
                $parents[] = $wpdb->get_row("SELECT `child_val` as name,
                    (SELECT `child_val` FROM $table_revs WHERE `revs_type` = 'variants' AND `parent_id` = $variance_id AND `child_key` = 'baseprice' AND id = (SELECT max(ID) FROM $table_revs WHERE `parent_id` = $variance_id AND `child_key` = 'baseprice' AND `revs_type` = 'variants')) as base_price,
                    (SELECT `parent_id` FROM $table_revs WHERE `revs_type` = 'variants' AND `parent_id` = $variance_id AND `child_key` = 'name' AND id = (SELECT max(ID) FROM $table_revs WHERE `parent_id` = $variance_id AND `child_key` = 'name' AND `revs_type` = 'variants')) as var_id,
                    (SELECT `child_val` FROM $table_revs WHERE `revs_type` = 'variants' AND `parent_id` = $variance_id AND `child_key` = 'status' AND id IN (SELECT max(ID) FROM $table_revs WHERE `parent_id` = $variance_id AND `child_key` = 'status' AND `revs_type` = 'variants')) as status,
                    null as options
                    FROM $table_revs
                    WHERE `revs_type` = 'variants'
                    AND `parent_id` = $variance_id
                ");

     
                $child[] = $wpdb->get_row("SELECT `ID` FROM $table_variants WHERE `parent_id` = $variance_id");
                foreach ($child as $key => $value) {
                    $child_id = $value->ID;
                    return $child_id;
                }
            }

            foreach ($parents as $key => $value) {
                $value->options = $child;
            }

            

            return $child;

            return array(
                "status" => "success",
                "data" => $result
            );


            

        }   

        

        
    }
