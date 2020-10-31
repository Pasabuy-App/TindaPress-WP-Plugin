<?php
	// Exit if accessed directly
	if ( ! defined( 'ABSPATH' ) )
	{
		exit;
	}

	/**
        * @package tindapress-wp-plugin
        * @version 0.2.0
	*/

  	class TP_Store_Lisitng_Docs_v2 {

        public static function listen($request){
            return rest_ensure_response(
                self::listen_open($request)
            );
        }

        public static function listen_open($request){

			// Initialize WP global variable
            global $wpdb;
            $tbl_product = TP_PRODUCT_v2;
            $tbl_stores = TP_STORES_v2;
            $tbl_store_categories = TP_STORES_CATEGORIES_v2;
            $tbl_product_categories = TP_PRODUCT_CATEGORY_v2;

            $files = file_get_contents("C:/Users/migue/OneDrive/Desktop/final_variant_view.json");
            $json_a = json_decode($files, true);
            $store = array();
            $product = array();
            #$cat = 0;
            #$parner_id = '9eiuytbl5wwq07i0exippkrd9b9rrm85vgpwr9hr69nz4rw6225Aupodutr7xArAbb';


            $smp = 0;

            $wpdb->query("START TRANSACTION");

            foreach ($json_a['RECORDS'] as $key => $value) {

                $get_product = $wpdb->get_row("SELECT hsid FROM $tbl_product WHERE ID = '{$value["pdid"]}' ");
                $smp ++;

                $value["status"] = lcfirst($value["preview"]);
                $name = esc_sql($value["name"]);

                $import_data = $wpdb->query("INSERT INTO
                tp_v2_product_variants
                    (`created_by`, `date_created`, `title`,`info`, `status`, `banner`, `price`, `discount`, `created_by`, `status`, `date_created`)
                VALUES
                    ('{$value["ID"]}', '$get_store_hsid->hsid', '$cat', '$product_name', '$short_info', '{$value["preview"]}', '', '{$value["price"]}', '{$value["discount"]}', '{$value["created_by"]}', '{$value["status"]}', '{$value["date_created"]}' ) ");
                $import_data_id = $wpdb->insert_id;




            }

            $wpdb->query("COMMIT");



/**
 * Product Script
*//*
            foreach ($json_a['RECORDS'] as $key => $value) {

                $cat_name = esc_sql($value["cat_name"]);

                $get_category_hsid = $wpdb->get_row("SELECT hsid FROM $tbl_product_categories WHERE title LIKE '%$cat_name%' ");
                if (empty($get_category_hsid)) {

                    if (empty($value)) {
                        return true;
                    }else{
                        $cat = $value['ID'];
                    }
                }else{
                    $cat= $get_category_hsid->hsid;
                }

                $get_store_hsid = $wpdb->get_row("SELECT hsid FROM tp_v2_stores WHERE ID = '{$value["stid"]}' ");

                if (empty($get_store_hsid)) {
                    return false;
                }

                if ($value["preview"] == null || $value["preview"] == "None") {
                    $value["preview"] = '';
                }
                // return $value;
                $smp ++;

                $value["status"] = lcfirst($value["preview"]);
                $product_name = esc_sql($value["product_name"]);
                $short_info = esc_sql($value["short_info"]);

                $import_data = $wpdb->query("INSERT INTO
                    tp_v2_products
                        (`ID`, `stid`, `pcid`, `title`,`info`, `avatar`, `banner`, `price`, `discount`, `created_by`, `status`, `date_created`)
                    VALUES
                        ('{$value["ID"]}', '$get_store_hsid->hsid', '$cat', '$product_name', '$short_info', '{$value["preview"]}', '', '{$value["price"]}', '{$value["discount"]}', '{$value["created_by"]}', '{$value["status"]}', '{$value["date_created"]}' ) ");
                $import_data_id = $wpdb->insert_id;

                $hsid = TP_Globals_v2::generating_pubkey($import_data_id, 'tp_v2_products', 'hsid', true, 64);


            }



            return $smp; */

/**
 * End Product Script
*/


/* Store script */
        /*
            foreach ($json_a['RECORDS'] as $key => $value) {

                $get_category_hsid = $wpdb->get_row("SELECT hsid FROM $tbl_store_categories WHERE title LIKE '%{$value["cat_name"]}%' ");
                if (empty($get_category_hsid)) {

                    if (empty($value)) {
                        return true;
                    }else{
                        $cat = $value['ID'];
                    }
                }else{
                    $cat= $get_category_hsid->hsid;
                }
                // return $value;
                $smp ++;


                $title = esc_sql($value["title"]);
                $info = esc_sql($value["short_info"]);
                $import_data = $wpdb->query("INSERT INTO
                    tp_v2_stores
                        (`avatar`, `banner`,   `ID`,`scid`, `adid`, `title`, `info`, `status`, `created_by`, `date_created`, `commision`)
                    VALUES
                        ('{$value["avatar"]}', '{$value["banner"]}', '{$value["ID"]}', '$cat', '{$value["address"]}', '$title', '$info', '{$value["status"]}', '{$value["created_by"]}' , '{$value["date_created"]}',  '{$value["comm"]}' ) ");
                $import_data_id = $wpdb->insert_id;

                $hsid = TP_Globals_v2::generating_pubkey($import_data_id, 'tp_v2_stores', 'hsid', true, 64);


                if ($value['partner'] == "true") {
                    $import_data_partner = $wpdb->query("INSERT INTO
                        tp_v2_featured_store
                            (`stid`, `groups`,   `avatar`, `banner`, `status`, `created_by`, `date_created`)
                        VALUES
                            ('$hsid', '$parner_id', '{$value["avatar"]}', '{$value["banner"]}', '{$value["status"]}', '{$value["created_by"]}' , '{$value["date_created"]}' ) ");
                    $import_data_partner_id = $wpdb->insert_id;

                    $hsid = TP_Globals_v2::generating_pubkey($import_data_partner_id, 'tp_v2_featured_store', 'hsid', false, 64);
                }

                // if($value['types'] == "store"){
                //     $store[] = $value;
                // }

                // if($value['types'] == "product"){
                //     $product[] = $value;
                // }
            } */
/* End */

/* Category insert script */



            /*  if($value['types'] == "store"){
                    $store[] = $value;
                }

                if($value['types'] == "product"){
                    $product[] = $value;
                } */



          /*   $wpdb->query("START TRANSACTION");
            foreach ($product as $key => $value) {
                #return $value;
                if ($value['groups'] == "robinson") {
                    return $value;
                }

                if ($value['avatar'] == "None") {
                    $value['avatar'] = null;
                }
                if ($value['info'] == "None") {
                    $value['info'] = null;
                }

                $smp ++;
                $title = esc_sql($value["title"]);
                $info = esc_sql($value["info"]);

                $get_store_hsid = $wpdb->get_row("SELECT hsid FROM tp_v2_stores WHERE ID = '{$value["stid"]}' ");

                if (empty($get_store_hsid)) {
                    return false;
                }

                $import_data = $wpdb->query("INSERT INTO tp_v2_products_categories (`title`, `info`, `status`, `stid`, `created_by`, `date_created`) VALUES ('$title', '$info', '{$value["status"]}', '$get_store_hsid->hsid',   '{$value["created_by"]}' , '{$value["date_created"]}' ) ");
                $import_data_id = $wpdb->insert_id;

                $hsid = TP_Globals_v2::generating_pubkey($import_data_id, 'tp_v2_products_categories', 'hsid', false, 64);

            }
            $wpdb->query("COMMIT"); */
/* END Category insert script */

            return $smp;
        }
    }
