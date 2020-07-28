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
        require plugin_dir_path(__FILE__) . '/v1/products/filter/class-categories.php';
        require plugin_dir_path(__FILE__) . '/v1/globals/class-globals.php';
        require plugin_dir_path(__FILE__) . '/v1/stores/class-categories.php';
        require plugin_dir_path(__FILE__) . '/v1/category/class-stores.php';
        require plugin_dir_path(__FILE__) . '/v1/stores/class-stores.php';
        require plugin_dir_path(__FILE__) . '/v1/stores/class-newest.php';
        require plugin_dir_path(__FILE__) . '/v1/products/class-newest.php';
        require plugin_dir_path(__FILE__) . '/v1/products/class-product.php';
	
	// Init check if USocketNet successfully request from wapi.
    function tindapress_route()
    {
        register_rest_route( 'tindapress/v1/products', 'init', array(
            'methods' => 'POST',
            'callback' => array('TP_Initialization','initialize'),
        ));
        register_rest_route( 'tindapress/v1/products', 'retrieve', array(
            'methods' => 'POST',
            'callback' => array('TP_Products','sample'),
        ));
         // retrieve
         register_rest_route( 'tindapress/v1/products', 'retrieve', array(
            'methods' => 'POST',
            'callback' => array('TP_Products','get_product'),
        ));
        // create
        register_rest_route( 'tindapress/v1/products', 'add_products', array(
            'methods' => 'POST',
            'callback' => array('TP_Products','add_product'),
        ));
        // update
        register_rest_route( 'tindapress/v1/globals/products', 'update', array(
            'methods' => 'POST',
            'callback' => array('TP_Products','update_product'),
        ));
        // update
        register_rest_route( 'tindapress/v1/globals', 'delete', array(
            'methods' => 'POST',
            'callback' => array('TP_Initialization','delete_product'),
        ));
         // sample
         register_rest_route( 'tindapress/v1/products', 'retriveById', array(
            'methods' => 'POST',
            'callback' => array('TP_Products','retrieveById_product'),
        ));
        // store folder
        register_rest_route( 'tindapress/v1/stores', 'categories', array(
            'methods' => 'POST',
            'callback' => array('TP_Store','category'),
        ));
        register_rest_route( 'tindapress/v1/stores', 'category', array(
            'methods' => 'POST',
            'callback' => array('TP_StorebyCategory','initialize'),
        ));
        register_rest_route( 'tindapress/v1/stores', 'newest', array(
            'methods' => 'POST',
            'callback' => array('TP_Newest','initialize'),
        ));


        // product folder
        register_rest_route( 'tindapress/v1/products/filter', 'category', array(
            'methods' => 'POST',
            'callback' => array('TP_Product','initialize'),
        ));

        register_rest_route( 'tindapress/v1/products', 'newest', array(
            'methods' => 'POST',
            'callback' => array('TP_Product_Newest','initialize'),
        ));

        // store
        register_rest_route( 'tindapress/v1/category', 'stores', array(
            'methods' => 'POST',
            'callback' => array('TP_Storelist','initialize'),
        ));



    }
    add_action( 'rest_api_init', 'tindapress_route' );
?>