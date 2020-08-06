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

        require plugin_dir_path(__FILE__) . '/v1/stores/class-stores.php';
        require plugin_dir_path(__FILE__) . '/v1/stores/class-newest.php';
        require plugin_dir_path(__FILE__) . '/v1/stores/class-documents.php';
        require plugin_dir_path(__FILE__) . '/v1/stores/class-categories.php';
        require plugin_dir_path(__FILE__) . '/v1/stores/class-popular.php';

        require plugin_dir_path(__FILE__) . '/v1/products/class-newest.php';
        require plugin_dir_path(__FILE__) . '/v1/products/class-product.php';
        require plugin_dir_path(__FILE__) . '/v1/products/class-popular.php';
        require plugin_dir_path(__FILE__) . '/v1/products/class-init.php';
        require plugin_dir_path(__FILE__) . '/v1/products/filter/class-categories.php';

        require plugin_dir_path(__FILE__) . '/v1/category/class-stores.php';

        require plugin_dir_path(__FILE__) . '/v1/globals/class-globals.php';

	
	// Init check if USocketNet successfully request from wapi.
    function tindapress_route()
    {
        /*
         * PRODUCT RESTAPI
        */
            register_rest_route( 'tindapress/v1/products', 'init', array(
                'methods' => 'POST',
                'callback' => array('TP_Initialization','initialize'),
            ));
        
            register_rest_route( 'tindapress/api/v1/products', 'popular', array(
                'methods' => 'POST',
                'callback' => array('TP_Product_popular','initialize'),
            ));

            register_rest_route( 'tindapress/api/v1/products', 'get_product_store', array(
                'methods' => 'POST',
                'callback' => array('TP_Products','initialize'),
            ));
        
            register_rest_route( 'tindapress/api/v1/products', 'retrieve', array(
                'methods' => 'POST',
                'callback' => array('TP_Products','get_product'),
            ));

            register_rest_route( 'tindapress/api/v1/products', 'add_products', array(
                'methods' => 'POST',
                'callback' => array('TP_Products','add_product'),
             ));

            register_rest_route( 'tindapress/api/v1/products', 'delete_product', array(
                'methods' => 'POST',
                'callback' => array('TP_Products','delete_product'),
            ));
        
            register_rest_route( 'tindapress/api/v1/products', 'get_all_product', array(
                'methods' => 'POST',
                'callback' => array('TP_Products','get_product_by_storeid'),
            ));

            register_rest_route( 'tindapress/api/v1/products', 'get_product_search', array(
                'methods' => 'POST',
                'callback' => array('TP_Products','get_product_search'),
            ));

            register_rest_route( 'tindapress/api/v1/products', 'update', array(
                'methods' => 'POST',
                'callback' => array('TP_Products','update_product'),
            ));

            register_rest_route( 'tindapress/v1/products', 'retriveById', array(
                'methods' => 'POST',
                'callback' => array('TP_Products','retrieveById_product'),
            ));

            register_rest_route( 'tindapress/v1/products/filter', 'category', array(
                'methods' => 'POST',
                'callback' => array('TP_Product','initialize'),
            ));
    
            register_rest_route( 'tindapress/v1/products', 'newest', array(
                'methods' => 'POST',
                'callback' => array('TP_Product_Newest','initialize'),
            ));

        register_rest_route( 'tindapress/v1/globals', 'delete', array(
            'methods' => 'POST',
            'callback' => array('TP_Initialization','delete_product'),
        ));
         // sample
        
        // store folder

        // add documents
        register_rest_route( 'tindapress/v1/stores', 'document', array(
            'methods' => 'POST',
            'callback' => array('TP_Documents','add_documents'),
        ));
        // add single documents
        register_rest_route( 'tindapress/v1/stores', 'add_single_docs', array(
            'methods' => 'POST',
            'callback' => array('TP_Documents','add_single_docs'),
        ));
         // delete Docs 
        register_rest_route( 'tindapress/v1/stores', 'delete_docs', array(
            'methods' => 'POST',
            'callback' => array('TP_Documents','delete_docs'),
        ));

        register_rest_route( 'tindapress/api/v1/stores', 'categories', array(
            'methods' => 'POST',
            'callback' => array('TP_Store','category'),
        ));
        register_rest_route( 'tindapress/v1/stores', 'category', array(
            'methods' => 'POST',
            'callback' => array('TP_StorebyCategory','initialize'),
        ));

        register_rest_route( 'tindapress/api/v1/stores', 'store_search', array(
            'methods' => 'POST',
            'callback' => array('TP_StorebyCategory','search_store'),
        ));

        register_rest_route( 'tindapress/v1/stores', 'newest', array(
            'methods' => 'POST',
            'callback' => array('TP_Newest','initialize'),
        ));


        // product folder
       

        // store
        register_rest_route( 'tindapress/v1/category', 'stores', array(
            'methods' => 'POST',
            'callback' => array('TP_Storelist','initialize'),
        ));
        // new popular
        register_rest_route( 'tindapress/api/v1/stores', 'popular_store', array(
            'methods' => 'POST',
            'callback' => array('TP_Popular_Store','popular_store'),
        ));
        



    }
    add_action( 'rest_api_init', 'tindapress_route' );
?>