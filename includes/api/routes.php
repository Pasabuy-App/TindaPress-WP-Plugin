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
        require plugin_dir_path(__FILE__) . '/v1/stores/class-categories.php';
	
	// Init check if USocketNet successfully request from wapi.
    function tindapress_route()
    {
        register_rest_route( 'tindapress/v1/products', 'init', array(
            'methods' => 'POST',
            'callback' => array('TP_Initialization','initialize'),
        ));
       
         // retrieve
         register_rest_route( 'tindapress/v1/globals', 'retrieve', array(
            'methods' => 'GET',
            'callback' => array('TP_Initialization','retrieve_product'),
        ));
        // create
        register_rest_route( 'tindapress/v1/products', 'add_products', array(
            'methods' => 'POST',
            'callback' => array('TP_Initialization','add_products'),
        ));
        // update
        register_rest_route( 'tindapress/v1/globals', 'update', array(
            'methods' => 'POST',
            'callback' => array('TP_Initialization','update_product'),
        ));
        // update
        register_rest_route( 'tindapress/v1/globals', 'delete', array(
            'methods' => 'POST',
            'callback' => array('TP_Initialization','delete_product'),
        ));
         // sample
         register_rest_route( 'tindapress/v1/globals', 'retriveById', array(
            'methods' => 'POST',
            'callback' => array('TP_Initialization','retrieveById_product'),
        ));
        // category
        register_rest_route( 'tindapress/v1/stores', 'category', array(
            'methods' => 'GET',
            'callback' => array('TP_Categories','category'),
        ));

    }
    add_action( 'rest_api_init', 'tindapress_route' );
?>