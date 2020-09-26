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

    class TP_Verify_Store_Personel {

        //REST API Call
        public static function listen(){
            $verified = self::listen_open();
            return  $verified['status'] == 'success' ? $verified : $verified;
           // return  self::listen_open();
        }

        //QA Done 2020-08-12 4:10 pm
        public static function listen_open(){
            global $wpdb;

             // Step1 : Check if prerequisites plugin are missing
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
                    "message" => "Please contact your administrator. Verification issues!",
                );
            }

            $wpid = $_POST['wpid'];
            // Verify tp_role
            $personel = $wpdb->get_row("SELECT * FROM tp_personnels WHERE wpid = '$wpid'");

            if(empty($personel)){
                return array( "status" => "failed" );
            }else if ($personel->status == 'inactive') {
                return array( "status" => "failed" );
            }else{

                $get_logo = $wpdb->get_row("SELECT (SELECT child_val FROM tp_revisions WHERE ID = st.logo AND revs_type = 'stores' AND parent_id =  $personel->stid) as logo, ID FROM tp_stores st WHERE ID = $personel->stid");
                $get_banner = $wpdb->get_row("SELECT (SELECT child_val FROM tp_revisions WHERE ID = st.banner AND revs_type = 'stores' AND parent_id =  $personel->stid) as banner FROM tp_stores st WHERE ID = $personel->stid");
                // TP_PLUGIN_URL . "assets/images/default-store.png"
                return array(
                    "status" => "success",
                    "data" => array(
                        "stid" => $personel->stid,
                    "roid" => $personel->roid,
                    "logo" =>  $get_logo->logo == null? TP_PLUGIN_URL . "assets/images/default-store.png":($get_logo->logo == "None"? TP_PLUGIN_URL . "assets/images/default-store.png":$get_logo->logo),
                    "banner" => $get_banner->banner == null? TP_PLUGIN_URL . "assets/images/default-store.png":($get_banner->banner == "None"? TP_PLUGIN_URL . "assets/images/default-store.png":$get_banner->banner)
                    )
                );
            }
        }
    }