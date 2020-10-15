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
    class TP_Store_Navigation {

        public static function listen(){
            return rest_ensure_response(
                self:: list_open()
            );
        }

        public static function list_open(){

            // 2nd Initial QA 2020-08-24 10:57 PM - Miguel
            global $wpdb;

            $data = $wpdb->get_results("SELECT * FROM tp_stores_view ");
            $results = array();
            for ($i=0; $i < count($data) ; $i++) {
                $results[] =
                array(
                    "geometry" => array(
                    "type" => "Point",
                    "coordinates"=> [$data[$i]->lat,$data[$i]->long]
                ),

                "type" => "Feature",
                "properties" => array(
                    "avatar" => $data[$i]->avatar,
                    "banner" => $data[$i]->banner,
                    "category"=> $data[$i]->cat_name,
                    "hours"=> "10am - 6pm",
                    "description"=> $data[$i]->long_info,
                    "name"=> $data[$i]->title,
                    "phone"=> $data[$i]->phone,
                    "storeid"=> $data[$i]->ID
                ));
            }
            return $results;
        }
    }