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

            isset($_POST['pdid']) ? $pdid = $_POST['pdid'] : $pdid = NULL;
            isset($_POST['pid']) ? $pid = $_POST['pid'] : $pid = NULL;
            isset($_POST['status']) ? $sts = $_POST['status'] : $sts = NULL;
            isset($_POST['vrid']) ? $vrid = $_POST['vrid'] : $vrid = NULL;


            $status = $sts  == '0' || $sts == NULL ? NULL : ($sts == '2' && $sts !== '0'? '0':'1');
            $product_id = $pdid  == '0'  || $pdid == NULL ? NULL: $product_id = $pdid;
            $parent_id = $pid  == '0' || $pid == NULL ? NULL: $parent_id = $pid;
            $variants_id = $vrid  == '0' || $vrid == NULL ? NULL: $variants_id = $vrid;
            
            $test[] = array('name' => 'small', 'price' => 130 );
            
            $sql = "SELECT
            var.ID,
            var.pdid,
            ( SELECT child_val FROM tp_revisions rev WHERE  rev.parent_id = var.ID AND rev.child_key = 'name' AND rev.revs_type = 'variants' ) as `name`,
            ( SELECT child_val FROM tp_revisions rev WHERE  rev.parent_id = var.ID AND rev.child_key = 'info' AND rev.revs_type = 'variants' ) as `info`,
            IF( ( SELECT child_val FROM tp_revisions rev WHERE  rev.parent_id = var.ID AND rev.child_key = 'baseprice' AND rev.revs_type = 'variants' ) = 1, 'Yes', 'No') as `base`,

                IF( rev.child_val = 1 , 'Active','Inactive') AS `status`,
                null as options
            FROM
                tp_variants var
                INNER JOIN tp_revisions rev ON rev.parent_id = var.ID 
            WHERE
                rev.revs_type = 'variants' 
                AND child_key = 'status' 
                AND rev.date_created = ( SELECT MAX( date_created ) FROM tp_revisions WHERE ID = rev.ID AND child_key = rev.child_key )
            ";

            if (isset($_POST['pdid'])) {
                if ($product_id != NULL) {
                    $sql .= " AND var.pdid = $product_id ";
                }
            }

            if (isset($_POST['status'])) {
                if ($status != NULL) {
                    $sql .= " AND rev.child_val = '$status' ";
                }
            }

            if (isset($_POST['vrid'])) {
                if ($variants_id != NULL) {
                    $sql .= " AND var.ID = '$variants_id' ";
                }
            }


            $result = $wpdb->get_results($sql);

            foreach ($result as $key => $value) {
                $parent = $value->ID;

                $option = $wpdb->get_results("SELECT
                rev.ID,
                rev.child_val as 'name',
                (SELECT child_val FROM tp_revisions rev WHERE parent_id = var.ID AND child_key = 'price' AND revs_type ='variants'  AND date_created = ( SELECT MAX(date_created) FROM tp_revisions WHERE ID = rev.ID  ) ) as `price`,
                (SELECT child_val FROM tp_revisions rev WHERE parent_id = var.ID AND child_key = 'info' AND revs_type ='variants'  AND date_created = ( SELECT MAX(date_created) FROM tp_revisions WHERE ID = rev.ID  ) ) as `info`,
                IF((SELECT child_val FROM tp_revisions rev WHERE parent_id = var.ID AND child_key = 'status' AND revs_type ='variants'  AND date_created = ( SELECT MAX(date_created) FROM tp_revisions WHERE ID = rev.ID  ) ) = 1, 'Active', IF((SELECT child_val FROM tp_revisions rev WHERE parent_id = var.ID AND child_key = 'status' AND revs_type ='variants'  AND date_created = ( SELECT MAX(date_created) FROM tp_revisions WHERE ID = rev.ID  ) ) is NULL , '0', 'Inactive' )) as `status`
            FROM
                tp_revisions rev
                INNER JOIN tp_variants var ON var.parent_id = '$parent' 
            WHERE   rev.revs_type = 'variants' AND child_key = 'name' AND rev.parent_id = var.ID
                ");

                $value->options = $option;  

                if (isset($_POST['vrid'])  ) {
                    if ($variants_id !== NULL  ) {

                        if (isset($_POST['pdid'])) {
                            if ($product_id !== NULL) {
                               
                                $value->options = $option;
                            }else{
                            $result = $option;

                            }
                        }else{

                            $result = $option;
                        }
                        

                    }
                }

                
            }

            return array(
                "status" => "success",
                "data" => $result
            );

        }   
    }
