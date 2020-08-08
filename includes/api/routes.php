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

    //Require the USocketNet class which have the core function of this plguin. 

        require plugin_dir_path(__FILE__) . '/v1/stores/class-stores.php';
        require plugin_dir_path(__FILE__) . '/v1/stores/class-newest.php';
        // require plugin_dir_path(__FILE__) . '/v1/stores/class-documents.php';
        require plugin_dir_path(__FILE__) . '/v1/stores/class-categories.php';
        require plugin_dir_path(__FILE__) . '/v1/stores/class-popular.php';

        require plugin_dir_path(__FILE__) . '/v1/products/class-insert.php';
        require plugin_dir_path(__FILE__) . '/v1/products/class-select.php';
        require plugin_dir_path(__FILE__) . '/v1/products/class-update.php';
        require plugin_dir_path(__FILE__) . '/v1/products/class-select-byid.php';
        require plugin_dir_path(__FILE__) . '/v1/products/class-delete.php';
        require plugin_dir_path(__FILE__) . '/v1/products/class-select-by-storeid.php';
        require plugin_dir_path(__FILE__) . '/v1/products/class-search.php';
        require plugin_dir_path(__FILE__) . '/v1/products/class-newest.php';
        require plugin_dir_path(__FILE__) . '/v1/products/class-popular.php';
        require plugin_dir_path(__FILE__) . '/v1/products/class-product.php';
        // require plugin_dir_path(__FILE__) . '/v1/products/filter/class-categories.php';

        // require plugin_dir_path(__FILE__) . '/v1/category/class-stores.php';
        require plugin_dir_path(__FILE__) . '/v1/stores/class-insert.php';
        require plugin_dir_path(__FILE__) . '/v1/stores/class-update.php';
        require plugin_dir_path(__FILE__) . '/v1/stores/class-delete.php';
        require plugin_dir_path(__FILE__) . '/v1/stores/class-select.php';
        require plugin_dir_path(__FILE__) . '/v1/stores/class-listing.php';
        require plugin_dir_path(__FILE__) . '/v1/stores/class-listing-active.php';
        require plugin_dir_path(__FILE__) . '/v1/stores/class-listing-inactive.php';

        require plugin_dir_path(__FILE__) . '/v1/globals/class-globals.php';

        //Category Classes
        require plugin_dir_path(__FILE__) . '/v1/category/class-delete.php';
        require plugin_dir_path(__FILE__) . '/v1/category/class-insert.php';
        require plugin_dir_path(__FILE__) . '/v1/category/class-listing.php';
        require plugin_dir_path(__FILE__) . '/v1/category/class-select.php';
        require plugin_dir_path(__FILE__) . '/v1/category/class-update.php';

        require plugin_dir_path(__FILE__) . '/v1/category/store/class-store-category.php';

	
	// Init check if USocketNet successfully request from wapi.
    function tindapress_route()
    {
        /*
         * PRODUCT RESTAPI
        */
            
            register_rest_route( 'tindapress/v1/products', 'insert', array(
                'methods' => 'POST',
                'callback' => array('TP_Insert_Product','listen'),
            ));

            register_rest_route( 'tindapress/v1/products', 'select', array(
                'methods' => 'POST',
                'callback' => array('TP_Select_Product','listen'),
            ));

            register_rest_route( 'tindapress/v1/products', 'update', array(
                'methods' => 'POST',
                'callback' => array('TP_Update_Products','listen'),
            ));

            register_rest_route( 'tindapress/v1/products', 'retriveById', array(
                'methods' => 'POST',
                'callback' => array('TP_Select_Byid_Product','listen'),
            ));
            
            register_rest_route( 'tindapress/v1/products', 'delete', array(
                'methods' => 'POST',
                'callback' => array('TP_Delete_Product','listen'),
            ));

            register_rest_route( 'tindapress/v1/products', 'get_all_product_bystid', array(
                'methods' => 'POST',
                'callback' => array('TP_Select_By_StoreId_Products','listen'),
            ));

            register_rest_route( 'tindapress/v1/products', 'search', array(
                'methods' => 'POST',
                'callback' => array('TP_Search_Products','listen'),
            ));

            register_rest_route( 'tindapress/v1/products', 'get_product_store', array(
                'methods' => 'POST',
                'callback' => array('TP_Products','listen'),
            ));
            
            register_rest_route( 'tindapress/v1/products', 'newest', array(
                'methods' => 'POST',
                'callback' => array('TP_Product_Newest','listen'),
            ));

            register_rest_route( 'tindapress/v1/products', 'popular', array(
                'methods' => 'POST',
                'callback' => array('TP_Product_popular','listen'),
            ));

            register_rest_route( 'tindapress/v1/products', 'filter/category', array(
                'methods' => 'POST',
                'callback' => array('TP_Select_Store_Category_Product','listen'),
            ));


        /*
         * DOCUMENTS RESTAPI
        */
            register_rest_route( 'tindapress/v1/stores/documents', 'insert', array(
                'methods' => 'POST',
                'callback' => array('TP_Insert_Documents','listen'),
            ));
            
        
        /*
         * Category RESTAPI
        */
            register_rest_route( 'tindapress/v1/category/store', 'list/active', array(
                'methods' => 'POST',
                'callback' => array('TP_Store_Category','listen'),
            ));
        
        /*
         * STORE RESTAPI
        */
            register_rest_route( 'tindapress/v1/stores', 'insert', array(
                'methods' => 'POST',
                'callback' => array('TP_Insert_Store','listen'),
            ));

            register_rest_route( 'tindapress/v1/stores', 'update', array(
                'methods' => 'POST',
                'callback' => array('TP_Update_Store','listen'),
            ));

            register_rest_route( 'tindapress/v1/stores', 'delete', array(
                'methods' => 'POST',
                'callback' => array('TP_Delete_Store','listen'),
            ));

            register_rest_route( 'tindapress/v1/stores', 'select', array(
                'methods' => 'POST',
                'callback' => array('TP_Select_Store','listen'),
            ));

            register_rest_route( 'tindapress/v1/stores', 'list/all', array(
                'methods' => 'POST',
                'callback' => array('TP_Listing_Store','listen'),
            ));

            register_rest_route( 'tindapress/v1/stores', 'list/store/active', array(
                'methods' => 'POST',
                'callback' => array('TP_Listing_Active_Store','listen'),
            ));

            register_rest_route( 'tindapress/v1/stores', 'list/store/inactive', array(
                'methods' => 'POST',
                'callback' => array('TP_Listing_Inactive_Store','listen'),
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

        /*
         * CATEGORIES RESTAPI
        */

        register_rest_route( 'tindapress/v1/category', 'insert', array(
            'methods' => 'POST',
            'callback' => array('TP_Category_Insert','listen'),
        ));

        register_rest_route( 'tindapress/v1/category', 'delete', array(
            'methods' => 'POST',
            'callback' => array('TP_Category_Delete','listen'),
        ));

        register_rest_route( 'tindapress/v1/category', 'update', array(
            'methods' => 'POST',
            'callback' => array('TP_Category_Update','listen'),
        ));

        register_rest_route( 'tindapress/v1/category', 'list', array(
            'methods' => 'POST',
            'callback' => array('TP_Category_List','listen'),
        ));
        
        register_rest_route( 'tindapress/v1/category', 'select', array(
            'methods' => 'POST',
            'callback' => array('TP_Category_Select','listen'),
        ));



    }
    add_action( 'rest_api_init', 'tindapress_route' );