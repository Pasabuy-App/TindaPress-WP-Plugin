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

        // Store V2
            // Store Categories
                require plugin_dir_path(__FILE__) . '/v2/store/category/class-insert.php';
                require plugin_dir_path(__FILE__) . '/v2/store/category/class-listing.php';
                // Groups
                    require plugin_dir_path(__FILE__) . '/v2/store/category/groups/class-insert.php';

            // Documents
                require plugin_dir_path(__FILE__) . '/v2/store/documents/doc-types/class-insert.php';
                require plugin_dir_path(__FILE__) . '/v2/store/documents/doc-types/class-listing.php';
                require plugin_dir_path(__FILE__) . '/v2/store/documents/class-listing.php';
                require plugin_dir_path(__FILE__) . '/v2/store/documents/class-insert.php';
                require plugin_dir_path(__FILE__) . '/v2/store/documents/class-update.php';
                require plugin_dir_path(__FILE__) . '/v2/store/documents/class-delete.php';

            // Rates
                require plugin_dir_path(__FILE__) . '/v2/store/rates/class-insert.php';
            // Featured
                // Groups
                    require plugin_dir_path(__FILE__) . '/v2/store/featured/groups/class-insert.php';

                require plugin_dir_path(__FILE__) . '/v2/store/featured/class-insert.php';
                require plugin_dir_path(__FILE__) . '/v2/store/featured/class-listing.php';

            require plugin_dir_path(__FILE__) . '/v2/store/class-insert.php';
            require plugin_dir_path(__FILE__) . '/v2/store/class-listing.php';
            require plugin_dir_path(__FILE__) . '/v2/store/class-update.php';

        // End

        // Product V2
            require plugin_dir_path(__FILE__) . '/v2/product/class-insert.php';
            require plugin_dir_path(__FILE__) . '/v2/product/class-listing.php';
            require plugin_dir_path(__FILE__) . '/v2/product/class-update.php';
            require plugin_dir_path(__FILE__) . '/v2/product/class-delete.php';

                // Categories
                    require plugin_dir_path(__FILE__) . '/v2/product/category/class-insert.php';
                    require plugin_dir_path(__FILE__) . '/v2/product/category/class-listing.php';
                    require plugin_dir_path(__FILE__) . '/v2/product/category/class-update.php';
                    require plugin_dir_path(__FILE__) . '/v2/product/category/class-delete.php';
                    require plugin_dir_path(__FILE__) . '/v2/product/category/class-listing-with-product.php';
                // Variants
                    require plugin_dir_path(__FILE__) . '/v2/product/variants/class-insert.php';
                    require plugin_dir_path(__FILE__) . '/v2/product/variants/class-listing.php';
                    require plugin_dir_path(__FILE__) . '/v2/product/variants/class-update.php';
                    require plugin_dir_path(__FILE__) . '/v2/product/variants/class-delete.php';
                // Ratings
                    require plugin_dir_path(__FILE__) . '/v2/product/rates/class-insert.php';
                // Featured
                    require plugin_dir_path(__FILE__) . '/v2/product/featured/class-insert.php';
                    require plugin_dir_path(__FILE__) . '/v2/product/featured/class-listing.php';

        // END
        //Global Classes
        require plugin_dir_path(__FILE__) . '/v2/class-globals.php';

	// Init check if USocketNet successfully request from wapi.
    function tindapress_route()
    {
        /*
         * VERSION TWO RESTAPI
        */
                // Store Rest api's

                    register_rest_route( 'tindapress/v2/store', 'list', array(
                        'methods' => 'POST',
                        'callback' => array('TP_Store_Listing_v2','listen'),
                    ));

                    register_rest_route( 'tindapress/v2/store', 'insert', array(
                        'methods' => 'POST',
                        'callback' => array('TP_Store_Insert_v2','listen'),
                    ));

                    register_rest_route( 'tindapress/v2/store', 'update', array(
                        'methods' => 'POST',
                        'callback' => array('TP_Store_Update_v2','listen'),
                    ));

                        // Store category
                            register_rest_route( 'tindapress/v2/store/category', 'insert', array(
                                'methods' => 'POST',
                                'callback' => array('TP_Store_Category_Insert_v2','listen'),
                            ));

                            register_rest_route( 'tindapress/v2/store/category', 'list', array(
                                'methods' => 'POST',
                                'callback' => array('TP_Store_Category_Listing_v2','listen'),
                            ));

                            // Groups
                                register_rest_route( 'tindapress/v2/store/category/groups', 'insert', array(
                                    'methods' => 'POST',
                                    'callback' => array('TP_Store_Category_Group_Insert_v2','listen'),
                                ));

                        // Documents
                            register_rest_route( 'tindapress/v2/store/document/type', 'insert', array(
                                'methods' => 'POST',
                                'callback' => array('TP_Store_Doc_type_Insert_v2','listen'),
                            ));

                            register_rest_route( 'tindapress/v2/store/document/type', 'list', array(
                                'methods' => 'POST',
                                'callback' => array('TP_Store_Doc_type_Listing_v2','listen'),
                            ));

                            register_rest_route( 'tindapress/v2/store/document', 'insert', array(
                                'methods' => 'POST',
                                'callback' => array('TP_Store_Insert_Docs_v2','listen'),
                            ));

                            register_rest_route( 'tindapress/v2/store/document', 'list', array(
                                'methods' => 'POST',
                                'callback' => array('TP_Store_Lisitng_Docs_v2','listen'),
                            ));

                            register_rest_route( 'tindapress/v2/store/document', 'update', array(
                                'methods' => 'POST',
                                'callback' => array('TP_Store_Update_Docs_v2','listen'),
                            ));

                            register_rest_route( 'tindapress/v2/store/document', 'delete', array(
                                'methods' => 'POST',
                                'callback' => array('TP_Store_Delete_Docs_v2','listen'),
                            ));

                        // Rates
                            register_rest_route( 'tindapress/v2/store/rates', 'insert', array(
                                'methods' => 'POST',
                                'callback' => array('TP_Store_Rates_Insert_v2','listen'),
                            ));
                        // Featured

                            register_rest_route( 'tindapress/v2/store/featured', 'list', array(
                                'methods' => 'POST',
                                'callback' => array('TP_Featured_Store_Listing_v2','listen'),
                            ));

                            register_rest_route( 'tindapress/v2/store/featured', 'insert', array(
                                'methods' => 'POST',
                                'callback' => array('TP_Featured_Store_Insert_v2','listen'),
                            ));

                            // Groups
                                register_rest_route( 'tindapress/v2/store/featured/groups', 'insert', array(
                                    'methods' => 'POST',
                                    'callback' => array('TP_Featured_Store_Groups_Insert_v2','listen'),
                                ));



                // Products rest api's

                    register_rest_route( 'tindapress/v2/product', 'insert', array(
                        'methods' => 'POST',
                        'callback' => array('TP_Product_Insert_v2','listen'),
                    ));

                    register_rest_route( 'tindapress/v2/product', 'list', array(
                        'methods' => 'POST',
                        'callback' => array('TP_Product_Listing_v2','listen'),
                    ));

                    register_rest_route( 'tindapress/v2/product', 'update', array(
                        'methods' => 'POST',
                        'callback' => array('TP_Product_Update_v2','listen'),
                    ));

                    register_rest_route( 'tindapress/v2/product', 'delete', array(
                        'methods' => 'POST',
                        'callback' => array('TP_Product_Delete_v2','listen'),
                    ));

                    // Category

                        register_rest_route( 'tindapress/v2/product/category', 'insert', array(
                            'methods' => 'POST',
                            'callback' => array('TP_Products_Category_Insert_v2','listen'),
                        ));

                        register_rest_route( 'tindapress/v2/product/category', 'delete', array(
                            'methods' => 'POST',
                            'callback' => array('TP_Product_Category_Delete_v2','listen'),
                        ));

                        register_rest_route( 'tindapress/v2/product/category', 'list', array(
                            'methods' => 'POST',
                            'callback' => array('TP_Product_Category_Listing_v2','listen'),
                        ));

                        register_rest_route( 'tindapress/v2/product/category', 'update', array(
                            'methods' => 'POST',
                            'callback' => array('TP_Product_Category_Update_v2','listen'),
                        ));

                        register_rest_route( 'tindapress/v2/product/category/list', 'product', array(
                            'methods' => 'POST',
                            'callback' => array('TP_Product_Category_Listing_Product_v2','listen'),
                        ));

                    // Variants

                        register_rest_route( 'tindapress/v2/product/variant', 'insert', array(
                            'methods' => 'POST',
                            'callback' => array('TP_Product_Variants_Insert_v2','listen'),
                        ));

                        register_rest_route( 'tindapress/v2/product/variant', 'list', array(
                            'methods' => 'POST',
                            'callback' => array('TP_Product_Variant_Listing_v2','listen'),
                        ));

                        register_rest_route( 'tindapress/v2/product/variant', 'update', array(
                            'methods' => 'POST',
                            'callback' => array('TP_Product_Variants_Update_v2','listen'),
                        ));

                        register_rest_route( 'tindapress/v2/product/variant', 'delete', array(
                            'methods' => 'POST',
                            'callback' => array('TP_Product_Variants_Delete_v2','listen'),
                        ));

                    // Rates

                        register_rest_route( 'tindapress/v2/product/rates', 'insert', array(
                            'methods' => 'POST',
                            'callback' => array('TP_Products_Ratings_Insert_v2','listen'),
                        ));

                    // Featured

                        register_rest_route( 'tindapress/v2/product/featured', 'insert', array(
                            'methods' => 'POST',
                            'callback' => array('TP_Featured_Products_Insert_v2','listen'),
                        ));

                        register_rest_route( 'tindapress/v2/product/featured', 'list', array(
                            'methods' => 'POST',
                            'callback' => array('TP_Product_Featued_Listing_v2','listen'),
                        ));

        /*
         * END
        */
    }
    add_action( 'rest_api_init', 'tindapress_route' );