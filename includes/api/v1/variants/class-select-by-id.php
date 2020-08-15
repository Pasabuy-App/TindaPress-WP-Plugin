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
    class TP_Select_Variants_Id {

        public static function listen(){
            return rest_ensure_response( 
                TP_Select_Variants_Id:: select_variants_id()
            );
        }

        public static function select_variants_id(){
            
            global $wpdb;
            $table_variants = TP_VARIANTS_TABLE;
            $table_revs = TP_REVISIONS_TABLE;
            $rev_fields = TP_REVISION_FIELDS;
            $variants_fields = TP_VARIANTS_FIELDS;
            

            //Step 1: Check if prerequisites plugin are missing
            $plugin = TP_Globals::verify_prerequisites();
            if ($plugin !== true) {
                return array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. ".$plugin." plugin missing!",
                );
            }

            // Step 2: Check if wpid and snky is valid
            if (DV_Verification::is_verified() == false) {
                return array(
                        "status" => "unknown",
                        "message" => "Please contact your administrator. Verification Issues!",
                );
            }

            // Step 3: Check if params are passed
			if (!isset($_POST['vid']) ) {
				return array(
						"status" => "unknown",
						"message" => "Please contact your administrator. Request unknown!",
                );
            }
            
            // Step 4: Check if params are not empty
            if (empty($_POST["vid"])) {
				return array(
						"status" => "failed",
						"message" => "Required fields cannot be empty.",
                );
            }

            $variants_id = $_POST['vid'];
           
            $get_parent = array();
            
            //Check if this exists
            $get_parent = $wpdb->get_row("SELECT `ID`,
            (SELECT `child_val` FROM $table_revs WHERE `ID` = $table_variants.status) as status
            FROM $table_variants 
            WHERE `ID` = $variants_id");
            

            if (!$get_parent) {
                return array(
                    "status" => "failed",
                    "message" => "This variant does not exists" 
                );
            }
            
            if ($get_parent->status == 0) {
                return array(
                    "status" => "failed",
                    "message" => "This variant is currently inactive" 
                );
            }

            $variants[] = $wpdb->get_row("SELECT `id`, `child_val` as name
                    FROM $table_revs
                    WHERE `revs_type` = 'variants'
                    AND `parent_id` = $variants_id
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

                    $result[$variants_id] = array('name' => $child->name, 'variants' => $result_variants);
                
                }
            
            }

            return array(
                "status" => "success",
                "data" => $result
            );

        }   

        

        
    }
