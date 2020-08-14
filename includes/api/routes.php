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
        require plugin_dir_path(__FILE__) . '/v1/products/class-activate.php';
        require plugin_dir_path(__FILE__) . '/v1/products/class-delete.php';
        require plugin_dir_path(__FILE__) . '/v1/products/class-insert.php';
        require plugin_dir_path(__FILE__) . '/v1/products/class-update.php';
        require plugin_dir_path(__FILE__) . '/v1/products/class-listing.php';

        //Stores Classes
        require plugin_dir_path(__FILE__) . '/v1/stores/class-insert.php';
        require plugin_dir_path(__FILE__) . '/v1/stores/class-update.php';
        require plugin_dir_path(__FILE__) . '/v1/stores/class-delete.php';
        require plugin_dir_path(__FILE__) . '/v1/stores/class-listing.php';
        require plugin_dir_path(__FILE__) . '/v1/stores/class-search.php';
     
        require plugin_dir_path(__FILE__) . '/v1/stores/class-newest.php';
        require plugin_dir_path(__FILE__) . '/v1/stores/class-popular.php';
        require plugin_dir_path(__FILE__) . '/v1/stores/class-best-seller.php';
        require plugin_dir_path(__FILE__) . '/v1/stores/class-activate.php';
        require plugin_dir_path(__FILE__) . '/v1/stores/class-near-me.php';

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
         require plugin_dir_path(__FILE__) . '/v1/category/class-store-insert.php';
         require plugin_dir_path(__FILE__) . '/v1/category/class-update.php';
         require plugin_dir_path(__FILE__) . '/v1/category/class-activate.php';
        //  new
         require plugin_dir_path(__FILE__) . '/v1/category/class-listing.php';

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

        // Address Folder
        require plugin_dir_path(__FILE__) . '/v1/stores/address/class-insert.php';
        require plugin_dir_path(__FILE__) . '/v1/stores/address/class-update.php';
        require plugin_dir_path(__FILE__) . '/v1/stores/address/class-delete.php';
        require plugin_dir_path(__FILE__) . '/v1/stores/address/class-listing.php';
        require plugin_dir_path(__FILE__) . '/v1/stores/address/class-activate.php';

        // Contact Folder
        require plugin_dir_path(__FILE__) . '/v1/stores/contacts/class-insert.php';
        require plugin_dir_path(__FILE__) . '/v1/stores/contacts/class-listing.php';

        // Variants Classes
        require plugin_dir_path(__FILE__) . '/v1/variants/class-insert-variants.php';
        require plugin_dir_path(__FILE__) . '/v1/variants/class-select-by-product.php';



        //Global Classes
        require plugin_dir_path(__FILE__) . '/v1/class-globals.php';
         

	// Init check if USocketNet successfully request from wapi.
    function tindapress_route()
    {
        /*
         * PRODUCT RESTAPI
        */
            register_rest_route( 'tindapress/v1/products', 'activate', array(
                'methods' => 'POST',
                'callback' => array('TP_Product_Activate','listen'),
            ));

            register_rest_route( 'tindapress/v1/products', 'delete', array(
                'methods' => 'POST',
                'callback' => array('TP_Product_Delete','listen'),
            ));

            register_rest_route( 'tindapress/v1/products', 'insert', array(
                'methods' => 'POST',
                'callback' => array('TP_Product_Insert','listen'),
            ));

            register_rest_route( 'tindapress/v1/products/list', 'active', array(
                'methods' => 'POST',
                'callback' => array('TP_Product_List_Active','listen'),
            ));

            register_rest_route( 'tindapress/v1/products/list', 'all', array(
                'methods' => 'POST',
                'callback' => array('TP_Product_List_All','listen'),
            ));

            register_rest_route( 'tindapress/v1/products/list', 'inactive', array(
                'methods' => 'POST',
                'callback' => array('TP_Product_List_Inactive','listen'),
            ));

            register_rest_route( 'tindapress/v1/products/category', 'select', array(
                'methods' => 'POST',
                'callback' => array('TP_Product_Select_Category','listen'),
            ));

            //Select by product id
            register_rest_route( 'tindapress/v1/products', 'select', array(
                'methods' => 'POST',
                'callback' => array('TP_Product_Select','listen'),
            ));

            register_rest_route( 'tindapress/v1/products/store', 'select', array(
                'methods' => 'POST',
                'callback' => array('TP_Product_Select_Store','listen'),
            ));

            register_rest_route( 'tindapress/v1/products', 'update', array(
                'methods' => 'POST',
                'callback' => array('TP_Product_Update','listen'),
            ));

            register_rest_route( 'tindapress/v1/products/store', 'active', array(
                'methods' => 'POST',
                'callback' => array('TP_Product_Store_Active','listen'),
            ));

            register_rest_route( 'tindapress/v1/products/store', 'inactive', array(
                'methods' => 'POST',
                'callback' => array('TP_Product_Store_Inactive','listen'),
            ));

            register_rest_route( 'tindapress/v1/products', 'list', array(
                'methods' => 'POST',
                'callback' => array('TP_Product_Listing','listen'),
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
                'callback' => array('TP_Store_Select','listen'),
            ));

            register_rest_route( 'tindapress/v1/stores', 'list', array(
                'methods' => 'POST',
                'callback' => array('TP_Store_Listing','listen'),
            ));

            register_rest_route( 'tindapress/v1/stores', 'listbycat', array(
                'methods' => 'POST',
                'callback' => array('TP_StorebyCategory','listen'),
            ));
            register_rest_route( 'tindapress/v1/stores/list', 'active', array(
                'methods' => 'POST',
                'callback' => array('TP_Store_Listing_Active','listen'),
            ));

            register_rest_route( 'tindapress/v1/stores/list', 'inactive', array(
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
                'callback' => array('TP_Store_Best_Seller','listen'),
            ));

            register_rest_route( 'tindapress/v1/stores', 'popular', array(
                'methods' => 'POST',
                'callback' => array('TP_Popular_Store','listen'),
            ));

            register_rest_route( 'tindapress/v1/stores', 'activate', array(
                'methods' => 'POST',
                'callback' => array('TP_Activate_Store','listen'),
            ));

            register_rest_route( 'tindapress/v1/stores/category', 'select', array(
                'methods' => 'POST',
                'callback' => array('TP_Store_Select_by_Category','listen'),
            ));

            // Address Folder
            register_rest_route( 'tindapress/v1/stores/address', 'insert', array(
                'methods' => 'POST',
                'callback' => array('TP_Store_Insert_address','listen'),
            ));

            register_rest_route( 'tindapress/v1/stores/address', 'update', array(
                'methods' => 'POST',
                'callback' => array('TP_Store_Update_address','listen'),
            ));

            register_rest_route( 'tindapress/v1/stores/address', 'activate', array(
                'methods' => 'POST',
                'callback' => array('TP_Store_Activate_Address','listen'),
            ));

            register_rest_route( 'tindapress/v1/stores/address', 'delete', array(
                'methods' => 'POST',
                'callback' => array('TP_Store_Delete_Address','listen'),
            ));

            register_rest_route( 'tindapress/v1/stores/address', 'select', array(
                'methods' => 'POST',
                'callback' => array('TP_Store_Select_Address','listen'),
            ));

            register_rest_route( 'tindapress/v1/stores/address', 'list', array(
                'methods' => 'POST',
                'callback' => array('TP_Store_Listing_Address','listen'),
            ));

            // Contact Folder

            register_rest_route( 'tindapress/v1/stores/contacts', 'insert', array(
                'methods' => 'POST',
                'callback' => array('TP_Store_Insert_Contacts','listen'),
            ));

            register_rest_route( 'tindapress/v1/stores/contacts', 'list', array(
                'methods' => 'POST',
                'callback' => array('TP_Store_Listing_Contacts','listen'),
            ));

            register_rest_route( 'tindapress/v1/stores', 'nearme', array(
                'methods' => 'POST',
                'callback' => array('TP_Store_Near_ME','listen'),
            ));

            
            
            // End of Address folder
            

        /*
         * ORDER RESTAPI
        */
            
            register_rest_route( 'tindapress/v1/order/total', 'sales', array(
                'methods' => 'POST',
                'callback' => array('TP_Total_sales','listen'),
            ));

            register_rest_route( 'tindapress/v1/order/total', 'monthly', array(
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
            register_rest_route( 'tindapress/v1/category/list', 'all', array(
                'methods' => 'POST',
                'callback' => array('TP_Category_List_All','listen'),
            ));

            register_rest_route( 'tindapress/v1/category/list', 'active', array(
                'methods' => 'POST',
                'callback' => array('TP_Category_List_Active','listen'),
            ));

            register_rest_route( 'tindapress/v1/category/list', 'inactive', array(
                'methods' => 'POST',
                'callback' => array('TP_Category_List_Inactive','listen'),
            ));

            register_rest_route( 'tindapress/v1/category/list/store', 'active', array(
                'methods' => 'POST',
                'callback' => array('TP_Category_List_Store_Active','listen'),
            ));

            register_rest_route( 'tindapress/v1/category/list/store', 'inactive', array(
                'methods' => 'POST',
                'callback' => array('TP_Category_List_Store_Inactive','listen'),
            ));

            register_rest_route( 'tindapress/v1/category/list/product', 'active', array(
                'methods' => 'POST',
                'callback' => array('TP_Category_List_Product_Active','listen'),
            ));

            register_rest_route( 'tindapress/v1/category/list/product', 'inactive', array(
                'methods' => 'POST',
                'callback' => array('TP_Category_List_Product_Inactive','listen'),
            ));

            register_rest_route( 'tindapress/v1/category', 'insert', array(
                'methods' => 'POST',
                'callback' => array('TP_Category_Insert','listen'),
            ));

            register_rest_route( 'tindapress/v1/category', 'delete', array(
                'methods' => 'POST',
                'callback' => array('TP_Category_Delete','listen'),
            ));

            register_rest_route( 'tindapress/v1/category', 'activate', array(
                'methods' => 'POST',
                'callback' => array('TP_Category_Activate','listen'),
            ));

            register_rest_route( 'tindapress/v1/category', 'update', array(
                'methods' => 'POST',
                'callback' => array('TP_Category_Update','listen'),
            ));

            register_rest_route( 'tindapress/v1/category/list', 'type', array(
                'methods' => 'POST',
                'callback' => array('TP_Category_List','listen'),
            ));
            
            register_rest_route( 'tindapress/v1/category', 'select', array(
                'methods' => 'POST',
                'callback' => array('TP_Category_Select','listen'),
            ));

            register_rest_route( 'tindapress/v1/category/select', 'store', array(
                'methods' => 'POST',
                'callback' => array('TP_Category_Select_Store','listen'),
            ));

            register_rest_route( 'tindapress/v1/category', 'list', array(
                'methods' => 'POST',
                'callback' => array('TP_Category_Listing','listen'),
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

            register_rest_route( 'tindapress/v1/operations/list', 'open', array(
                'methods' => 'POST',
                'callback' => array('TP_List_Open','listen'),
            ));

            register_rest_route( 'tindapress/v1/operations/list', 'orders', array(
                'methods' => 'POST',
                'callback' => array('TP_List_Orders','listen'),
            ));

            register_rest_route( 'tindapress/v1/operations/list', 'month', array(
                'methods' => 'POST',
                'callback' => array('TP_List_Month','listen'),
            ));

            register_rest_route( 'tindapress/v1/operations/list', 'date', array(
                'methods' => 'POST',
                'callback' => array('TP_List_Date','listen'),
            ));

        /*
         * VARIANTS RESTAPI
        */

            register_rest_route( 'tindapress/v1/variants', 'insert', array(
                'methods' => 'POST',
                'callback' => array('TP_Insert_Variants','listen'),
            ));

            register_rest_route( 'tindapress/v1/variants/product', 'select', array(
                'methods' => 'POST',
                'callback' => array('TP_Select_Variants','listen'),
            ));
                
    
    }
    add_action( 'rest_api_init', 'tindapress_route' );