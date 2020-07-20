<?php
	// Exit if accessed directly
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	/** 
        * @package tindapress-wp-plugin
		* @version 0.1.0
		* This is the primary gateway of all the rest api request.
	*/
?>

<?php

    //Require the USocketNet class which have the core function of this plguin. 
    require plugin_dir_path(__FILE__) . '/v1/products/class-init.php';
        require plugin_dir_path(__FILE__) . '/v1/globals/class-globals.php';
	
	// Init check if USocketNet successfully request from wapi.
    function tindapress_route()
    {
        register_rest_route( 'tindapress/v1/products', 'init', array(
            'methods' => 'POST',
            'callback' => array('TP_Initialization','initialize'),
        ));
        // sample
        register_rest_route( 'tindapress/v1/products', 'first', array(
            'methods' => 'GET',
            'callback' => array('TP_Initialization','sample'),
        ));
        
        // create
        register_rest_route( 'tindapress/v1/products', 'add_products', array(
            'methods' => 'POST',
            'callback' => array('TP_Initialization','add_products'),
        ));
        // update
        register_rest_route( 'tindapress/v1/globals', 'update', array(
            'methods' => 'POST',
            'callback' => array('TP_Globals','update'),
        ));
        // update
        register_rest_route( 'tindapress/v1/globals', 'delete', array(
            'methods' => 'GET',
            'callback' => array('TP_Globals','delete'),
        ));
        


    }
    add_action( 'rest_api_init', 'tindapress_route' );

?>