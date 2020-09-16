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
    class TP_Update_Variants {
        public static function listen(){
            return rest_ensure_response( 
                TP_Update_Variants:: update_variants()
            );
        }
        public static function update_variants(){
            
            // 2nd Initial QA 2020-08-24 11:20 PM - Miguel
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
            // if (DV_Verification::is_verified() == false) {
            //     return array(
            //         "status" => "unknown",
            //         "message" => "Please contact your administrator. Verification Issues!",
            //     );
            // }
            // Step 3: Sanitize request
            if ( !isset($_POST['pdid']) || !isset($_POST['vid']) || !isset($_POST['name']) ) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Request unknown!",
                );
            }
            if ( isset($_POST['base'])  && isset($_POST['price']) ){
                return array(
                    "status" => "failed",
                    "message" => "Please select base or price.",
                );
            }


            if ($_POST['base'] !== '1' && $_POST['base'] !== '0' ) {
                return array(
                    "status" => "failed",
                    "message" => "Invalid value of base price.",
                );
            }
            
            // Step 4: Sanitize if variable is empty
            if ( empty($_POST['pdid']) || empty($_POST['vid']) || empty($_POST['name']) ) {
                return array(
                    "status" => "failed",
                    "message" => "Required fields cannot be empty.",
                );
            }
            
            if(isset($_POST['base']) && $_POST['base'] !== '1' && $_POST['base'] !== '0' ){
                return array(
                    "status" => "failed",
                    "message" => "Invalid value of type.",
                );
            }
            // Step 6: Store post to variable
            $product_id = $_POST['pdid'];
            $variants_id = $_POST['vid'];
            $variant_name = $_POST['name'];
            $wpid = $_POST['wpid'];
            $date = TP_Globals:: date_stamp();
            isset($_POST['info']) ? $info = $_POST['info'] : $info = NULL;
            
            // Step 7: Validate if exists and if status is 0 or 1 using variant id and product id
            $get_parent = $wpdb->get_row("SELECT var.ID, var.parent_id,
                (SELECT child_val FROM tp_revisions WHERE ID = MAX(rev.ID)) as status
            FROM
                $table_variants var
            INNER JOIN $table_revs rev ON rev.parent_id = var.ID 
            WHERE var.ID = '$variants_id'  AND var.pdid = '$product_id'
            AND rev.revs_type = 'variants' 
            AND child_key = 'status' 
            ");
            if (!$get_parent){ // Check if null
                return array(
                    "status" => "failed",
                    "message" => "This variant does not exists." 
                );
            }
            
            if ($get_parent->status === '0') { // Check the status
                return array(
                    "status" => "failed",
                    "message" => "This variant is already inactive." 
                );
            }
            if ( isset($_POST['base']) ){ // If the post base is set
                if ( !($get_parent->parent_id === '0') ) { 
                    return array(
                        "status" => "failed",
                        "message" => "The variant id is not a variant." 
                    );
                }
                $ckbp = 'baseprice';// Name of Variant and base price
                $cvbp = $_POST['base'];
            }
            if ( isset($_POST['price']) ){// If the post price is set
                if ( ($get_parent->parent_id === '0') ) { 
                    return array(
                        "status" => "failed",
                        "message" => "This is a variant not an option." 
                    );
                }
                $ckbp = 'price';// Name of Option and price
                $cvbp = $_POST['price'];
            }
            $child_key = array( // Store into array
                $ckbp => $cvbp ,
                "status" => "1",
                "name" => $variant_name
            );
            
            // Step 8: Query
            $wpdb->query("START TRANSACTION");
            foreach ($child_key as $key => $val){ // loop the array and insert into tp revisions
                $rev_insert = $wpdb->query("INSERT INTO `$table_revs` $rev_fields VALUES ('variants', '$variants_id', '$key', '$val', $wpid, '$date')");
            }
            if ( isset($_POST['info']) ){ // if  post info is set, insert into tp revisions
                $rev_insert_info = $wpdb->query("INSERT INTO `$table_revs` $rev_fields VALUES ('variants', '$variants_id', 'info', '$info', $wpid, '$date')");
            } else { // if not set, get the value of last info using revision id and insert into tp revisions
                $get_info = $wpdb->get_row("SELECT (SELECT child_val FROM tp_revisions WHERE ID = MAX(rev.ID)) as info
            FROM
                $table_variants var
            INNER JOIN 
                $table_revs rev ON rev.parent_id = var.ID 
            WHERE 
                var.ID = '$variants_id'  AND var.pdid = '$product_id'
            AND 
                rev.revs_type = 'variants' 
            AND 
                child_key = 'info' 
            ");
                $rev_insert_info =  $wpdb->query("INSERT INTO `$table_revs` $rev_fields VALUES ('variants', '$variants_id', 'info', '$get_info->info', $wpid, '$date')");
            }
            // Step 9: Check result
            if ($rev_insert < 1 || $rev_insert_info < 1) {
                $wpdb->query("ROLLBACK");
                return array(
                    "status" => "error",
                    "message" => "An error occured while submitting data to the server.",
                );
            }else{
                $wpdb->query("COMMIT");
                
                return array(
                    "status" => "success",
                    "message" => "Data has been updated successfully.",
                );
            }
        } 
    }
