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
        require plugin_dir_path(__FILE__) . '/v1/products/class-listing.php';
        require plugin_dir_path(__FILE__) . '/v1/products/class-update.php';
        require plugin_dir_path(__FILE__) . '/v1/products/class-select-byid.php';
        require plugin_dir_path(__FILE__) . '/v1/products/class-delete.php';
        require plugin_dir_path(__FILE__) . '/v1/products/class-select-by-storeid.php';
        require plugin_dir_path(__FILE__) . '/v1/products/class-search.php';
        require plugin_dir_path(__FILE__) . '/v1/products/class-newest.php';
        require plugin_dir_path(__FILE__) . '/v1/products/class-popular.php';
        require plugin_dir_path(__FILE__) . '/v1/products/class-product.php';
        require plugin_dir_path(__FILE__) . '/v1/products/class-listing-price.php';
        require plugin_dir_path(__FILE__) . '/v1/products/class-listing-price-storeid.php';
        require plugin_dir_path(__FILE__) . '/v1/products/class-select-by-category.php';
        // require plugin_dir_path(__FILE__) . '/v1/products/filter/class-categories.php';

        // require plugin_dir_path(__FILE__) . '/v1/category/class-stores.php';
        // require plugin_dir_path(__FILE__) . '/v1/stores/class-insert.php';
        require plugin_dir_path(__FILE__) . '/v1/stores/class-update.php';
        require plugin_dir_path(__FILE__) . '/v1/stores/class-delete.php';
        require plugin_dir_path(__FILE__) . '/v1/stores/class-select.php';
        require plugin_dir_path(__FILE__) . '/v1/stores/class-listing.php';
        require plugin_dir_path(__FILE__) . '/v1/stores/class-listing-active.php';
        require plugin_dir_path(__FILE__) . '/v1/stores/class-listing-inactive.php';
        require plugin_dir_path(__FILE__) . '/v1/stores/class-search.php';
        
        require plugin_dir_path(__FILE__) . '/v1/settings/class-banner.php';

        require plugin_dir_path(__FILE__) . '/v1/class-globals.php';
        
         //Category Classes
         require plugin_dir_path(__FILE__) . '/v1/category/class-delete.php';
         require plugin_dir_path(__FILE__) . '/v1/category/class-insert.php';
         require plugin_dir_path(__FILE__) . '/v1/category/class-listing.php';
         require plugin_dir_path(__FILE__) . '/v1/category/class-select.php';
         require plugin_dir_path(__FILE__) . '/v1/category/class-select-type.php';
         require plugin_dir_path(__FILE__) . '/v1/category/class-select-by-store.php';
         require plugin_dir_path(__FILE__) . '/v1/category/class-store-insert.php';
         require plugin_dir_path(__FILE__) . '/v1/category/class-update.php';

    
        require plugin_dir_path(__FILE__) . '/v1/order/class-total-sales.php';
        require plugin_dir_path(__FILE__) . '/v1/order/class-total-sales-date.php';
         

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

            register_rest_route( 'tindapress/v1/products', 'list/price', array(
                'methods' => 'POST',
                'callback' => array('TP_Listing_Product_by_Price','listen'),
            ));

            register_rest_route( 'tindapress/v1/products', 'list/price/store', array(
                'methods' => 'POST',
                'callback' => array('TP_Listing_Product_Price_by_storeId','listen'),
            ));
            
            register_rest_route( 'tindapress/v1/products', 'select/category', array(
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

            register_rest_route( 'tindapress/v1/stores', 'listbycat', array(
                'methods' => 'POST',
                'callback' => array('TP_StorebyCategory','listen'),
            ));
            register_rest_route( 'tindapress/v1/stores', 'list/store/active', array(
                'methods' => 'POST',
                'callback' => array('TP_Listing_Active_Store','listen'),
            ));

            register_rest_route( 'tindapress/v1/stores', 'list/store/inactive', array(
                'methods' => 'POST',
                'callback' => array('TP_Listing_Inactive_Store','listen'),
            ));

        /*
         * ORDER RESTAPI
        */
            
            register_rest_route( 'tindapress/v1/order', 'total/sales', array(
                'methods' => 'POST',
                'callback' => array('TP_Total_sales','listen'),
            ));

            register_rest_route( 'tindapress/v1/order', 'total/sales/date', array(
                'methods' => 'POST',
                'callback' => array('TP_Total_sales_date','listen'),
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

        register_rest_route( 'tindapress/v1/stores', 'search', array(
            'methods' => 'POST',
            'callback' => array('TP_SearchStore','listen'),
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

            register_rest_route( 'tindapress/v1/category', 'list/all', array(
                'methods' => 'POST',
                'callback' => array('TP_Category_All','listen'),
            ));

            register_rest_route( 'tindapress/v1/category', 'list/type', array(
                'methods' => 'POST',
                'callback' => array('TP_Category_List','listen'),
            ));
            
            register_rest_route( 'tindapress/v1/category', 'select', array(
                'methods' => 'POST',
                'callback' => array('TP_Category_Select','listen'),
            ));

            register_rest_route( 'tindapress/v1/category', 'select/store', array(
                'methods' => 'POST',
                'callback' => array('TP_Category_Select_Store','listen'),
            ));

            register_rest_route( 'tindapress/v1/category', 'store/insert', array(
                'methods' => 'POST',
                'callback' => array('TP_Category_Store_Insert','listen'),
            ));
            
        

        /*
         * SETTINGS RESTAPI
        */

            register_rest_route( 'tindapress/v1/settings', 'banner', array(
                'methods' => 'POST',
                'callback' => array('TP_Banner_update','listen'),
            ));



    }
    add_action( 'rest_api_init', 'tindapress_route' );