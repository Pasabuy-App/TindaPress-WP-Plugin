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
    class TP_List_Variants_With_Options {

        public static function listen(){
            return rest_ensure_response(
                self:: list_variants()
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
            // if (DV_Verification::is_verified() == false) {
            //     return array(
            //         "status" => "unknown",
            //         "message" => "Please contact your administrator. Verification Issues!",
            //     );
            // }

            if (!isset($_POST['pdid'])) {
                return array(
                    "status" => "unknown",
                    "message" => "Please contact your administrator. Request unknown!",
                );
            }

            if (empty($_POST['pdid'])) {
                return array(
                    "status" => "failed",
                    "message" => "Required fields cannot be empty.",
                );
            }

            $product_id = $_POST['pdid'];


            $_parent = $wpdb->get_results("SELECT
            var.ID,
            var.pdid,
            ( SELECT child_val FROM $table_revs rev WHERE  rev.parent_id = var.ID AND rev.child_key = 'name' AND rev.revs_type = 'variants' AND rev.ID = ( SELECT MAX(ID) FROM $table_revs WHERE  parent_id = rev.parent_id AND revs_type ='variants' AND child_key = 'name'  ) ) as `name`,
            IF ( ( SELECT child_val FROM $table_revs rev WHERE  rev.parent_id = var.ID AND rev.child_key = 'info' AND rev.revs_type = 'variants' AND rev.ID = ( SELECT MAX(ID) FROM $table_revs WHERE  parent_id = rev.parent_id AND revs_type ='variants' AND child_key = 'info'  ) ) is null, 'NONE', ( SELECT child_val FROM $table_revs rev WHERE  rev.parent_id = var.ID AND rev.child_key = 'info' AND rev.revs_type = 'variants' AND rev.ID = ( SELECT MAX(ID) FROM $table_revs WHERE  parent_id = rev.parent_id AND revs_type ='variants' AND child_key = 'info'  ) ) )as `info`,
            IF( ( SELECT child_val FROM $table_revs rev WHERE  rev.parent_id = var.ID AND rev.child_key = 'baseprice' AND rev.revs_type = 'variants'  AND rev.ID = ( SELECT MAX(ID) FROM $table_revs WHERE  parent_id = rev.parent_id AND revs_type ='variants' AND child_key = 'baseprice'   ) ) = 1, 'Yes', 'No') as `base`,
                IF( ( SELECT child_val FROM $table_revs rev WHERE  rev.parent_id = var.ID AND rev.child_key = 'status' AND rev.revs_type = 'variants'  AND rev.ID = ( SELECT MAX(ID) FROM $table_revs WHERE  parent_id = rev.parent_id AND revs_type ='variants' AND child_key = 'status'   ) ) = 1 , 'Active','Inactive') AS `status`,
                null as options
            FROM
                $table_variants var
                INNER JOIN $table_revs rev ON rev.parent_id = var.ID
            WHERE
                var.parent_id = 0
                AND rev.revs_type = 'variants'
                AND child_key = 'baseprice'
                AND rev.ID = ( SELECT MAX(ID) FROM $table_revs WHERE  parent_id = rev.parent_id AND revs_type ='variants' AND child_key = 'baseprice'   )
                AND var.pdid = '$product_id'");

           foreach ($_parent as $key => $value) {

                $parent = $value->ID;
                $value->options = $wpdb->get_results("SELECT
                        var.ID,
                        -- rev.child_val as 'name',

                        (SELECT child_val FROM tp_revisions rev 
                        WHERE 
                            parent_id = var.ID
                        AND child_key = 'name' 
                        AND revs_type ='variants'  
                        AND ID = (SELECT MAX(ID) FROM tp_revisions WHERE  parent_id = var.ID AND revs_type ='variants' AND child_key = 'name' ) ) as `name`,
                        
                        (SELECT child_val FROM tp_revisions rev 
                        WHERE 
                            parent_id = var.ID
                        AND child_key = 'price' 
                        AND revs_type ='variants'  
                        AND ID = (SELECT MAX(ID) FROM tp_revisions WHERE  parent_id = var.ID AND revs_type ='variants' AND child_key = 'price' ) ) as `price`,

                        IF ( (SELECT child_val FROM $table_revs rev 
                        WHERE 
                            parent_id = var.ID 
                        AND child_key = 'info' 
                        AND revs_type ='variants'  
                        AND ID = ( SELECT MAX(ID) FROM $table_revs WHERE  parent_id = rev.parent_id AND revs_type ='variants' AND child_key = 'info'  ) ) is null,
                            'None', 
                            (SELECT child_val FROM $table_revs rev WHERE parent_id = var.ID AND child_key = 'info' AND revs_type ='variants'  AND ID = ( SELECT MAX(ID) FROM $table_revs WHERE  parent_id = rev.parent_id AND revs_type ='variants' AND child_key = 'info'  ) ) ) as `info`,
                        IF( ( SELECT child_val FROM $table_revs rev WHERE parent_id = var.ID AND child_key = 'status' AND revs_type ='variants' AND ID = ( SELECT MAX(ID) FROM $table_revs WHERE child_key ='status' AND revs_type ='variants' AND parent_id = rev.parent_id  ) ) = 1, 'Active', 'Inactive' ) as `status`
                    FROM
                        $table_revs rev
                        INNER JOIN $table_variants var ON var.parent_id = '$parent'
                    WHERE rev.revs_type = 'variants' AND rev.parent_id = var.ID
                        GROUP BY var.ID
                ");
            }

            return array(
                "status" => "success",
                "data" => $_parent,
            );
            //return $_parent;
        }
    }