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
    class TP_Var_Opt_List {

        public static function listen(){
            return rest_ensure_response(
                TP_Var_Opt_List:: list_variants()
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

            if (!isset($_POST['pdid']) || !isset($_POST['pid'])) {
                return array(
					"status" => "unknown",
					"message" => "Please contact your administrator. Request unknown!",
                );
            }

            if (!is_numeric($_POST['pdid']) || !is_numeric($_POST['pid'])) {
                return array(
					"status" => "failed",
					"message" => "Please contact your administrator. Invalid input of product id or parent id.",
                );
            }

            $product_id = $_POST['pdid'];
            $parent_id = $_POST['pid'];

            isset($_POST['status']) ? $sts = $_POST['status'] : $sts = NULL;
            isset($_POST['vrid']) ? $vrid = $_POST['vrid'] : $vrid = NULL;

            $status = $sts  == '0' || $sts == NULL ? NULL : ($sts == '2' && $sts !== '0'? '0':'1');
            $variants_id = $vrid  == '0' || $vrid == NULL ? NULL: $variants_id = $vrid;

            // TODO : check product id and parent id
            // TODO : if variant id is set, check if a variant or an options

            $sql = "SELECT
                var.ID,
                var.pdid,
                (SELECT child_val FROM tp_revisions WHERE ID = (SELECT MAX(ID) FROM tp_revisions WHERE revs_type = 'variants' AND child_key = 'name' AND parent_id = var.ID)) as name,
                IF ( (SELECT child_val FROM tp_revisions WHERE ID = (SELECT MAX(ID) FROM tp_revisions WHERE revs_type = 'variants' AND child_key = 'price' AND parent_id = var.ID)) IS NULL, '0',
                (SELECT child_val FROM tp_revisions WHERE ID = (SELECT MAX(ID) FROM tp_revisions WHERE revs_type = 'variants' AND child_key = 'price' AND parent_id = var.ID)) ) as price,
                IF ((SELECT child_val FROM tp_revisions WHERE ID = (SELECT MAX(ID) FROM tp_revisions WHERE revs_type = 'variants' AND child_key = 'info' AND parent_id = var.ID)) is null, 'None', (SELECT child_val FROM tp_revisions WHERE ID = (SELECT MAX(ID) FROM tp_revisions WHERE revs_type = 'variants' AND child_key = 'info' AND parent_id = var.ID)) ) as info,
                IF ((SELECT child_val FROM tp_revisions WHERE ID = (SELECT MAX(ID) FROM tp_revisions WHERE revs_type = 'variants' AND child_key = 'baseprice' AND parent_id = var.ID)) = 1, 'Yes', 'No') as baseprice,
                IF ((SELECT child_val FROM tp_revisions WHERE ID = (SELECT MAX(ID) FROM tp_revisions WHERE revs_type = 'variants' AND child_key = 'status' AND parent_id = var.ID)) = 1, 'Active', 'Inactive' ) as status,
                (SELECT date_created FROM tp_revisions WHERE ID = (SELECT MAX(ID) FROM tp_revisions WHERE revs_type = 'variants' AND child_key = 'name' AND parent_id = var.ID)) as date_created
            FROM
                tp_variants AS var 
            WHERE var.pdid = $product_id AND var.parent_id = $parent_id 
            ";

            if (isset($_POST['vrid'])) { // option or variant details
                if ($variants_id != NULL) {
                    $sql .= " AND var.ID = '$variants_id' ";
                }
            }

            if (isset($_POST['status'])) {
                if ($status != NULL) {
                    $sql .= " AND ( SELECT child_val FROM $table_revs rev WHERE  rev.parent_id = var.ID AND rev.child_key = 'status' AND rev.revs_type = 'variants'  AND rev.ID = ( SELECT MAX(ID) FROM $table_revs WHERE  parent_id = rev.parent_id AND revs_type ='variants' AND child_key = 'status'   ) ) = '$status' ";
                }
            }
            
            $sql .= " ORDER BY var.ID DESC ";
            $result = $wpdb->get_results($sql);

            return array(
                "status" => "success",
                "data" => $result
            );
        }
    }
