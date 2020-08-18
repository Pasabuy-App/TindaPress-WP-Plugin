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
    class TP_Select_Variants {

        public static function listen(){
            return rest_ensure_response( 
                TP_Select_Variants:: select_variants()
            );
        }

        public static function select_variants(){
            
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

            // Step3 : Sanitize request
			if (!isset($_POST['pdid']) ) {
				return array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Request unknown!",
                );
            }
            
            // Step4 : Sanitize if variable is empty
            if (empty($_POST["pdid"])) {
				return array(
						"status" => "failed",
						"message" => "Required fields cannot be empty.",
                );
            }

            $product_id = $_POST['pdid'];
            
            $get_parent = $wpdb->get_results("SELECT `ID` FROM $table_variants WHERE `pdid` = $product_id");

            $variants = array();
            $child_variants = array();
            $parent_variants = array();
            
            
            foreach ($get_parent as $row) {
                $result = array();
               
                $variants[] = $wpdb->get_row("SELECT `id`, `child_val` as name,
                    (SELECT `child_val` FROM $table_revs WHERE `revs_type` = 'variants' AND `parent_id` = $row->ID AND `child_key` = 'baseprice') as base_price,
                    (SELECT `parent_id` FROM $table_revs WHERE `revs_type` = 'variants' AND `parent_id` = $row->ID AND `child_key` = 'name') as var_id
                    FROM $table_revs
                    WHERE `revs_type` = 'variants'
                    AND `parent_id` = $row->ID
                    AND `child_key` = 'name'
                 ");
                 
                foreach ($variants as $child) {
                    
                    $result_variants = array();
                    
                    $child_variants[$child->name] = $wpdb->get_results("SELECT `id`, `child_key` as name, `child_val` as value FROM $table_revs WHERE `parent_id` = $child->id");
                    
                    foreach ($child_variants[$child->name] as $parent) {
                        
                        $parent_variants[$child->name][$parent->value] = $wpdb->get_results("SELECT `child_key`, `child_val` FROM $table_revs WHERE `parent_id` = $parent->id");
                        
                        foreach ($parent_variants[$child->name][$parent->value] as $key) {
                            
                            $result_variants[$parent->value][$key->child_key] = $key->child_val;
                           
                        }
                        
                    }
                    
                    $result['list'][] = array('name' => $child->name, 'id' => $child->var_id, 'base_price' => $child->base_price, 'variants' => $result_variants);
                    
                }
                 
            }
         
            return array(
                "status" => "success",
                "data" => $result
            );


            

        }   

        

        
    }
