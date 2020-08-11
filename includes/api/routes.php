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
        
        //Products Classes
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
        require plugin_dir_path(__FILE__) . '/v1/products/class-best-seller.php';
        require plugin_dir_path(__FILE__) . '/v1/products/class-best-seller-local.php';
        // require plugin_dir_path(__FILE__) . '/v1/products/filter/class-categories.php';

        //Stores Classes
        require plugin_dir_path(__FILE__) . '/v1/stores/class-insert.php';
        require plugin_dir_path(__FILE__) . '/v1/stores/class-update.php';
        require plugin_dir_path(__FILE__) . '/v1/stores/class-delete.php';
        require plugin_dir_path(__FILE__) . '/v1/stores/class-select.php';
        require plugin_dir_path(__FILE__) . '/v1/stores/class-listing.php';
        require plugin_dir_path(__FILE__) . '/v1/stores/class-listing-active.php';
        require plugin_dir_path(__FILE__) . '/v1/stores/class-listing-inactive.php';
        require plugin_dir_path(__FILE__) . '/v1/stores/class-search.php';
        require plugin_dir_path(__FILE__) . '/v1/stores/class-stores.php';
        require plugin_dir_path(__FILE__) . '/v1/stores/class-newest.php';
        require plugin_dir_path(__FILE__) . '/v1/stores/class-popular.php';
        require plugin_dir_path(__FILE__) . '/v1/stores/class-best-seller.php';
        
        // Document Classes
        require plugin_dir_path(__FILE__) . '/v1/stores/documents/class-delete.php';
        require plugin_dir_path(__FILE__) . '/v1/stores/documents/class-insert.php';
        require plugin_dir_path(__FILE__) . '/v1/stores/documents/class-update.php';
        
        //Settings Classes
        require plugin_dir_path(__FILE__) . '/v1/settings/class-banner.php';
        require plugin_dir_path(__FILE__) . '/v1/settings/class-logo.php';

        //Category Classes
         require plugin_dir_path(__FILE__) . '/v1/category/class-delete.php';
         require plugin_dir_path(__FILE__) . '/v1/category/class-insert.php';
         require plugin_dir_path(__FILE__) . '/v1/category/class-listing.php';
         require plugin_dir_path(__FILE__) . '/v1/category/class-select.php';
         require plugin_dir_path(__FILE__) . '/v1/category/class-select-type.php';
         require plugin_dir_path(__FILE__) . '/v1/category/class-select-by-store.php';
         require plugin_dir_path(__FILE__) . '/v1/category/class-store-insert.php';
         require plugin_dir_path(__FILE__) . '/v1/category/class-update.php';

        //Operations Classes
        require plugin_dir_path(__FILE__) . '/v1/operations/class-list-open.php';
        require plugin_dir_path(__FILE__) . '/v1/operations/class-list-month.php';
        require plugin_dir_path(__FILE__) . '/v1/operations/class-list-orders.php';
        require plugin_dir_path(__FILE__) . '/v1/operations/class-list-by-date.php';

        //Orders Classes
        require plugin_dir_path(__FILE__) . '/v1/order/class-total-sales.php';
        require plugin_dir_path(__FILE__) . '/v1/order/class-total-sales-date.php';
        require plugin_dir_path(__FILE__) . '/v1/order/class-listing-stages.php';
        require plugin_dir_path(__FILE__) . '/v1/order/class-listing-date.php';

        //Global Classes
        require plugin_dir_path(__FILE__) . '/v1/class-globals.php';
         

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
            
            register_rest_route( 'tindapress/v1/products', 'best_seller/global', array(
                'methods' => 'POST',
                'callback' => array('TP_Best_Seller_Product','listen'),
            ));

            register_rest_route( 'tindapress/v1/products', 'best_seller/local', array(
                'methods' => 'POST',
                'callback' => array('TP_Best_Seller_Product_Local','listen'),
            ));


        /*
         * DOCUMENTS RESTAPI
        */
            register_rest_route( 'tindapress/v1/stores/documents', 'insert', array(
                'methods' => 'POST',
                'callback' => array('TP_Insert_Documents','listen'),
            ));

            register_rest_route( 'tindapress/v1/documents', 'delete', array(
                'methods' => 'POST',
                'callback' => array('TP_Delte_Documents','listen'),
            ));

            register_rest_route( 'tindapress/v1/documents', 'update', array(
                'methods' => 'POST',
                'callback' => array('TP_Update_Documents','listen'),
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

            register_rest_route( 'tindapress/v1/stores', 'search', array(
                'methods' => 'POST',
                'callback' => array('TP_SearchStore','listen'),
            ));
    
            register_rest_route( 'tindapress/v1/stores', 'newest', array(
                'methods' => 'POST',
                'callback' => array('TP_Newest','listen'),
            ));

            register_rest_route( 'tindapress/v1/stores', 'best_seller', array(
                'methods' => 'POST',
                'callback' => array('TP_Best_Seller_Store','listen'),
            ));

            register_rest_route( 'tindapress/v1/stores', 'popular_store', array(
                'methods' => 'POST',
                'callback' => array('TP_Popular_Store','listen'),
            ));

        /*
         * ORDER RESTAPI
        */
            
            register_rest_route( 'tindapress/v1/order', 'total/sales', array(
                'methods' => 'POST',
                'callback' => array('TP_Total_sales','listen'),
            ));

            register_rest_route( 'tindapress/v1/order', 'total/monthly', array(
                'methods' => 'POST',
                'callback' => array('TP_Total_sales_date','listen'),
            ));

            register_rest_route( 'tindapress/v1/order', 'stage', array(
                'methods' => 'POST',
                'callback' => array('TP_OrdersByStage','listen'),
            ));

            register_rest_route( 'tindapress/v1/order', 'date', array(
                'methods' => 'POST',
                'callback' => array('TP_OrdersByDate','listen'),
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
            register_rest_route( 'tindapress/v1/settings', 'logo', array(
                'methods' => 'POST',
                'callback' => array('TP_Logo_update','listen'),
            ));

        /*
         * OPERATIONS RESTAPI
        */

            register_rest_route( 'tindapress/v1/operations', 'list/open', array(
                'methods' => 'POST',
                'callback' => array('TP_List_Open','listen'),
            ));

            register_rest_route( 'tindapress/v1/operations', 'list/orders', array(
                'methods' => 'POST',
                'callback' => array('TP_List_Orders','listen'),
            ));

            register_rest_route( 'tindapress/v1/operations', 'list/month', array(
                'methods' => 'POST',
                'callback' => array('TP_List_Month','listen'),
            ));

            register_rest_route( 'tindapress/v1/operations', 'list/date', array(
                'methods' => 'POST',
                'callback' => array('TP_List_Date','listen'),
            ));


    }
    add_action( 'rest_api_init', 'tindapress_route' );