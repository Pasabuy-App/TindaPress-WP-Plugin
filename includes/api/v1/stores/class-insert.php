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
?>
<?php

    class TP_Insert_Store {
        public static function listen(){
            global $wpdb;



            $later = TP_Globals::date_stamp();
            
            $user = TP_Insert_Store::catch_post();

            // variables for query
            $table_store = TP_STORES_TABLE;
            $table_store_fields = TP_STORES_FIELDS;

            $table_revs = TP_REVISION_TABLE;
            $table_revs_fields = TP_REVISION_FIELDS;

            $revs_type = "stores";


            $wpdb->query("START TRANSACTION");

                $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('$revs_type', '0', 'ctid', '{$user["ctid"]}', '{$user["created_by"]}', '$later')");
                $ctid = $wpdb->insert_id;

                $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('$revs_type', '0', 'title', '{$user["title"]}', '{$user["created_by"]}', '$later')");
                $title = $wpdb->insert_id;

                $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('$revs_type', '0', 'short_info', '{$user["title"]}', '{$user["created_by"]}', '$later')");
                $short_info = $wpdb->insert_id;

                $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('$revs_type', '0', 'long_info', '{$user["long_info"]}', '{$user["created_by"]}', '$later')");
                $long_info = $wpdb->insert_id;

                $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('$revs_type', '0', 'logo', '{$user["logo"]}', '{$user["created_by"]}', '$later')");
                $logo = $wpdb->insert_id;

                $wpdb->query("INSERT INTO $table_revs $table_revs_fields  VALUES ('$revs_type', '0', 'banner', '{$user["banner"]}', '{$user["created_by"]}', '$later')");
                $banner = $wpdb->insert_id;
                

            if ($ctid < 1 || $title < 1 || $short_info < 1 || $long_info < 1 || $logo < 1 || $banner < 1 ) {
            $wpdb->query("ROLLBACK");
                return array(
                    "status" => "failed",
                    "message" => "An error occured while submitting data to database.",
                );
            }

            //                                                                 (ctid, title, short_info, long_info, logo, banner, address, created_by, date_created )
            $wpdb->query("INSERT INTO $table_store $table_store_fields VALUES ($ctid, $title, $short_info, $long_info, $logo, $banner, '{$user["address"]}', '{$user["created_by"]}'   )  ");


            $wpdb->query("COMMIT");

            return "HAHAHAHAHA";
            
        }

        // Catch Post 
        public static function catch_post()
        {
              $cur_user = array();
               
                $cur_user['created_by'] = $_POST["wpid"];
                $cur_user['created_by'] = $_POST["snky"];
                $cur_user['ctid']       = $_POST["ctid"];

                $cur_user['title']      = $_POST["title"];
                $cur_user['short_info'] = $_POST["short_info"];
                $cur_user['long_info']  = $_POST["long_info"];
                $cur_user['logo']        = $_POST["logo"];
                $cur_user['banner']      = $_POST["banner"];
            
  
              return  $cur_user;
        }
    }