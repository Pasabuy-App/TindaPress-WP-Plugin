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

    class TP_Assigned_Store_List {

        //REST API Call
        public static function listen(){

            return rest_ensure_response(
                self::listen_open()
            );
        }

        //QA Done 2020-08-12 4:10 pm
        public static function listen_open(){
            global $wpdb;

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

            $wpid = $_POST['wpid'];
            $status = $_POST['status'];

            // $sql = "SELECT
            //         hash_id as ID,
            //         (SELECT child_val FROM tp_revisions WHERE ID = (SELECT title FROM tp_stores WHERE ID = p.stid)) AS store_name,
            //         (SELECT child_val FROM tp_revisions WHERE ID = (SELECT logo FROM tp_stores WHERE ID = p.stid)) AS store_logo,
            //         (SELECT child_val FROM tp_revisions WHERE ID = (SELECT banner FROM tp_stores WHERE ID = p.stid)) AS store_banner,
            //         (SELECT hash_id FROM tp_roles WHERE ID = p.roid) as role_id,
            //         (SELECT
            //             (SELECT child_val FROM tp_revisions WHERE ID = r.title  AND revs_type = 'roles' )
            //         FROM
            //                 tp_roles r
            //         WHERE ID = p.roid) as role_title,
            //         p.status,
            //         p.pincode
            //     FROM
            //         tp_personnels p
            //     WHERE`
            //         wpid = '$wpid'
            //     ";
            $sql = "SELECT
                #p.ID,
                p.hash_id AS personnel_id,
                                    p.stid AS ID,
                                    p.roid,
                (SELECT child_val FROM tp_revisions WHERE ID = (SELECT title FROM tp_stores WHERE ID = p.stid)) AS title,
                (SELECT child_val FROM tp_revisions WHERE ID = (SELECT logo FROM tp_stores WHERE ID = p.stid)) AS avatar,
                (SELECT child_val FROM tp_revisions WHERE ID = (SELECT banner FROM tp_stores WHERE ID = p.stid)) AS banner,
                (SELECT child_val FROM tp_revisions WHERE ID = (SELECT MAX(ID) FROM tp_revisions WHERE parent_id = p.roid AND child_key = 'title')) as position,

                p.status,
                p.pincode
            FROM
                tp_personnels p
                                    INNER JOIN tp_roles AS r ON p.roid = r.ID
            WHERE
                p.wpid = '$wpid'";

            if (isset($_POST['status'])) {
                if ($_POST['status'] != null) {
                    if ($_POST['status'] != "active" && $_POST['status'] != "inactive" ) {
                        return array(
                            "status" => "failed",
                            "message" => "Invalid value of status.",
                        );
                    }

                    $status = $_POST['status'];
                    $sql .=" AND p.status = '$status' ";
                }
            }

            $get_data = $wpdb->get_results($sql);

            return array(
                "status" => "success",
                "data" => $get_data
            );
        }
    }