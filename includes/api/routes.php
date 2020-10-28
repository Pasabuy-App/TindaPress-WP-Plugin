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

        // Version one file path

            //Products Classes
            require plugin_dir_path(__FILE__) . '/v1/products/class-activate.php';
            require plugin_dir_path(__FILE__) . '/v1/products/class-delete.php';
            require plugin_dir_path(__FILE__) . '/v1/products/class-insert.php';
            require plugin_dir_path(__FILE__) . '/v1/products/class-update.php';
            require plugin_dir_path(__FILE__) . '/v1/products/class-listing.php';
            require plugin_dir_path(__FILE__) . '/v1/products/class-product-nearme.php';
            require plugin_dir_path(__FILE__) . '/v1/products/class-discount-create.php';
            require plugin_dir_path(__FILE__) . '/v1/products/class-discount-update.php';
            require plugin_dir_path(__FILE__) . '/v1/products/class-wishlist-insert.php';

            // Personel Classes
            require plugin_dir_path(__FILE__) . '/v1/personel/class-insert-role.php';
            require plugin_dir_path(__FILE__) . '/v1/personel/class-insert-role-access.php';
            require plugin_dir_path(__FILE__) . '/v1/personel/class-verify.php';
            require plugin_dir_path(__FILE__) . '/v1/personel/class-listing-access.php';
            require plugin_dir_path(__FILE__) . '/v1/personel/class-insert-personnel.php';
            require plugin_dir_path(__FILE__) . '/v1/personel/class-delete-personnel.php';
            require plugin_dir_path(__FILE__) . '/v1/personel/class-activate-personnel.php';
            require plugin_dir_path(__FILE__) . '/v1/personel/class-assigned-store-list.php';
            require plugin_dir_path(__FILE__) . '/v1/personel/class-listing-personnel.php';
            require plugin_dir_path(__FILE__) . '/v1/personel/class-update-personnel.php';

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
            require plugin_dir_path(__FILE__) . '/v1/stores/class-store-nearme.php';
            require plugin_dir_path(__FILE__) . '/v1/stores/class-update-commision.php';
            require plugin_dir_path(__FILE__) . '/v1/stores/class-listing-byCategory.php';
            require plugin_dir_path(__FILE__) . '/v1/stores/class-update-partner.php';
            require plugin_dir_path(__FILE__) . '/v1/stores/class-store-info.php';
            require plugin_dir_path(__FILE__) . '/v1/stores/class-store-navigation.php';

            // Featured Store Classes
            require plugin_dir_path(__FILE__) . '/v1/stores/featured/class-insert.php';
            require plugin_dir_path(__FILE__) . '/v1/stores/featured/class-listing.php';

            // Store Schedule
            require plugin_dir_path(__FILE__) . '/v1/stores/schedule/class-update.php';
            require plugin_dir_path(__FILE__) . '/v1/stores/schedule/class-insert.php';
            require plugin_dir_path(__FILE__) . '/v1/stores/schedule/class-listing.php';


            // Document Classes
            require plugin_dir_path(__FILE__) . '/v1/stores/documents/class-delete.php';
            require plugin_dir_path(__FILE__) . '/v1/stores/documents/class-insert.php';
            require plugin_dir_path(__FILE__) . '/v1/stores/documents/class-update.php';
            require plugin_dir_path(__FILE__) . '/v1/stores/documents/class-approve.php';
            require plugin_dir_path(__FILE__) . '/v1/stores/documents/class-listing.php';

            //Settings Classes
            require plugin_dir_path(__FILE__) . '/v1/settings/class-banner.php';
            require plugin_dir_path(__FILE__) . '/v1/settings/class-logo.php';
            require plugin_dir_path(__FILE__) . '/v1/settings/class-search.php';

            //Category Classes
            require plugin_dir_path(__FILE__) . '/v1/category/class-delete.php';
            require plugin_dir_path(__FILE__) . '/v1/category/class-insert.php';
            require plugin_dir_path(__FILE__) . '/v1/category/class-store-insert.php';
            require plugin_dir_path(__FILE__) . '/v1/category/class-update.php';
            require plugin_dir_path(__FILE__) . '/v1/category/class-activate.php';
            require plugin_dir_path(__FILE__) . '/v1/category/class-listing.php';
            require plugin_dir_path(__FILE__) . '/v1/category/class-listing-with-product.php';

            // Address Folder
            require plugin_dir_path(__FILE__) . '/v1/stores/address/class-insert.php';
            require plugin_dir_path(__FILE__) . '/v1/stores/address/class-update.php';
            require plugin_dir_path(__FILE__) . '/v1/stores/address/class-delete.php';
            require plugin_dir_path(__FILE__) . '/v1/stores/address/class-listing.php';
            require plugin_dir_path(__FILE__) . '/v1/stores/address/class-activate.php';

            // Contact Folder
            require plugin_dir_path(__FILE__) . '/v1/stores/contacts/class-insert.php';
            require plugin_dir_path(__FILE__) . '/v1/stores/contacts/class-listing.php';
            require plugin_dir_path(__FILE__) . '/v1/stores/contacts/class-update.php';
            require plugin_dir_path(__FILE__) . '/v1/stores/contacts/class-delete.php';
            require plugin_dir_path(__FILE__) . '/v1/stores/contacts/class-activate.php';

            // Variants Classes
            require plugin_dir_path(__FILE__) . '/v1/variants/class-insert-variants.php';
            require plugin_dir_path(__FILE__) . '/v1/variants/class-update-variants.php';
            require plugin_dir_path(__FILE__) . '/v1/variants/class-delete-variants.php';
            require plugin_dir_path(__FILE__) . '/v1/variants/class-activate-variants.php';
            require plugin_dir_path(__FILE__) . '/v1/variants/class-listing.php';
            require plugin_dir_path(__FILE__) . '/v1/variants/class-var-opt-listing.php';
            require plugin_dir_path(__FILE__) . '/v1/variants/class-variant-options.php';
        // End

        // Version two file path
        // Store V2
            // Store Categories
                require plugin_dir_path(__FILE__) . '/v2/store/category/class-insert.php';
            // Documents
                require plugin_dir_path(__FILE__) . '/v2/store/documents/doc-types/class-insert.php';

            require plugin_dir_path(__FILE__) . '/v2/store/class-insert.php';

        // End

        //Global Classes
        require plugin_dir_path(__FILE__) . '/v1/class-globals.php';
        require plugin_dir_path(__FILE__) . '/v2/class-globals.php';

	// Init check if USocketNet successfully request from wapi.
    function tindapress_route()
    {

        /*
         * STORE VERSION ONE RESTAPI
        */

                register_rest_route( 'tindapress/v1/settings', 'search', array(
                    'methods' => 'POST',
                    'callback' => array('TP_Search','listen'),
                ));


            /*
            * SCHEDULE STORE RESTAPI
            */
                register_rest_route( 'tindapress/v1/store/schedule', 'insert', array(
                    'methods' => 'POST',
                    'callback' => array('TP_Store_Schedule_Insert','listen'),
                ));

                register_rest_route( 'tindapress/v1/store/schedule', 'list', array(
                    'methods' => 'POST',
                    'callback' => array('TP_Store_Schedule_Listing','listen'),
                ));

                register_rest_route( 'tindapress/v1/store/schedule', 'update', array(
                    'methods' => 'POST',
                    'callback' => array('TP_Store_Schedule_Update','listen'),
                ));

            /*
            * FEATURED STORE RESTAPI
            */

                register_rest_route( 'tindapress/v1/store/featured', 'insert', array(
                    'methods' => 'POST',
                    'callback' => array('TP_Featured_Store_Insert','listen'),
                ));

                register_rest_route( 'tindapress/v1/store/featured', 'list', array(
                    'methods' => 'POST',
                    'callback' => array('TP_Featured_Store_Listing','listen'),
                ));

            /*
            * PERSONNEL RESTAPI
            */

                register_rest_route( 'tindapress/v1/personel', 'update', array(
                    'methods' => 'POST',
                    'callback' => array('TP_Update_Personnel','listen'),
                ));

                register_rest_route( 'tindapress/v1/personel', 'list', array(
                    'methods' => 'POST',
                    'callback' => array('TP_Listing_Personnel','listen'),
                ));

                register_rest_route( 'tindapress/v1/personel/store', 'list', array(
                    'methods' => 'POST',
                    'callback' => array('TP_Assigned_Store_List','listen'),
                ));

                register_rest_route( 'tindapress/v1/personel/role', 'create', array(
                    'methods' => 'POST',
                    'callback' => array('TP_Personel_Create_Role','listen'),
                ));

                register_rest_route( 'tindapress/v1/personel/role/create', 'access', array(
                    'methods' => 'POST',
                    'callback' => array('TP_Personel_Create_Role_Meta','listen'),
                ));

                register_rest_route( 'tindapress/v1/personel/role', 'verify', array(
                    'methods' => 'POST',
                    'callback' => array('TP_Verify_Store_Personel','listen'),
                ));

                register_rest_route( 'tindapress/v1/personel/role/access', 'list', array(
                    'methods' => 'POST',
                    'callback' => array('TP_Listing_Access','listen'),
                ));

                register_rest_route( 'tindapress/v1/personel', 'insert', array(
                    'methods' => 'POST',
                    'callback' => array('TP_Insert_Personnel','listen'),
                ));

                register_rest_route( 'tindapress/v1/personel', 'activate', array(
                    'methods' => 'POST',
                    'callback' => array('TP_Activate_Personnel','listen'),
                ));

                register_rest_route( 'tindapress/v1/personel', 'delete', array(
                    'methods' => 'POST',
                    'callback' => array('TP_Delete_Personnel','listen'),
                ));
            /*
            * PRODUCT RESTAPI
            */



                register_rest_route( 'tindapress/v1/products/variants', 'list', array(
                    'methods' => 'POST',
                    'callback' => array('TP_Product_Variants','listen'),
                ));

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

                register_rest_route( 'tindapress/v1/products', 'update', array(
                    'methods' => 'POST',
                    'callback' => array('TP_Product_Update','listen'),
                ));

                register_rest_route( 'tindapress/v1/products', 'list', array(
                    'methods' => 'POST',
                    'callback' => array('TP_Product_Listing','listen'),
                ));

                register_rest_route( 'tindapress/v1/products', 'nearme', array(
                    'methods' => 'POST',
                    'callback' => array('TP_Product_Nearme','listen'),
                ));

                register_rest_route( 'tindapress/v1/products/discount', 'create', array(
                    'methods' => 'POST',
                    'callback' => array('TP_Product_Discount_Create','listen'),
                ));

                register_rest_route( 'tindapress/v1/products/discount', 'update', array(
                    'methods' => 'POST',
                    'callback' => array('TP_Product_Discount_Update','listen'),
                ));

                register_rest_route( 'tindapress/v1/products/wishlist', 'insert', array(
                    'methods' => 'POST',
                    'callback' => array('TP_Wishlist_Insert','listen'),
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
                    'callback' => array('TP_Delete_Documents','listen'),
                ));

                register_rest_route( 'tindapress/v1/documents', 'update', array(
                    'methods' => 'POST',
                    'callback' => array('TP_Update_Documents','listen'),
                ));

                register_rest_route( 'tindapress/v1/documents', 'approve', array(
                    'methods' => 'POST',
                    'callback' => array('TP_Approve_Documents','listen'),
                ));

                register_rest_route( 'tindapress/v1/documents', 'listing', array(
                    'methods' => 'POST',
                    'callback' => array('TP_Store_Listing_Documents','listen'),
                ));

            /*
            * STORE RESTAPI
            */

                register_rest_route( 'tindapress/v1/store', 'navigation', array(
                    'methods' => 'POST',
                    'callback' => array('TP_Store_Navigation','listen'),
                ));

                register_rest_route( 'tindapress/v1/stores', 'info', array(
                    'methods' => 'POST',
                    'callback' => array('TP_Store_Info','listen'),
                ));

                register_rest_route( 'tindapress/v1/stores', 'partner', array(
                    'methods' => 'POST',
                    'callback' => array('TP_Update_Partner','listen'),
                ));

                register_rest_route( 'tindapress/v1/stores/list', 'category', array(
                    'methods' => 'POST',
                    'callback' => array('TP_Store_Listing_By_Category','listen'),
                ));

                register_rest_route( 'tindapress/v1/stores', 'comm', array(
                    'methods' => 'POST',
                    'callback' => array('TP_Update_Commision','listen'),
                ));

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

                register_rest_route( 'tindapress/v1/stores', 'list', array(
                    'methods' => 'POST',
                    'callback' => array('TP_Store_Listing','listen'),
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

                register_rest_route( 'tindapress/v1/stores/contacts', 'update', array(
                    'methods' => 'POST',
                    'callback' => array('TP_Store_Update_Contacts','listen'),
                ));

                register_rest_route( 'tindapress/v1/stores/contacts', 'delete', array(
                    'methods' => 'POST',
                    'callback' => array('TP_Store_Delete_Contacts','listen'),
                ));

                register_rest_route( 'tindapress/v1/stores/contacts', 'activate', array(
                    'methods' => 'POST',
                    'callback' => array('TP_Store_Activate_Contacts','listen'),
                ));

                register_rest_route( 'tindapress/v1/stores', 'nearme', array(
                    'methods' => 'POST',
                    'callback' => array('TP_Store_Nearme','listen'),
                ));

            /*
            * CATEGORIES RESTAPI
            */

                register_rest_route( 'tindapress/v1/category/list', 'product', array(
                    'methods' => 'POST',
                    'callback' => array('TP_Category_Listing_With_Product','listen'),
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
            * VARIANTS RESTAPI
            */

                register_rest_route( 'tindapress/v1/variants/option', 'list', array(
                    'methods' => 'POST',
                    'callback' => array('TP_List_Variants_With_Options','listen'),
                ));

                register_rest_route( 'tindapress/v1/variants', 'insert', array(
                    'methods' => 'POST',
                    'callback' => array('TP_Insert_Variants','listen'),
                ));

                register_rest_route( 'tindapress/v1/variants', 'list', array(
                    'methods' => 'POST',
                    'callback' => array('TP_List_Variants','listen'),
                ));

                register_rest_route( 'tindapress/v1/variants', 'delete', array(
                    'methods' => 'POST',
                    'callback' => array('TP_Delete_Variants','listen'),
                ));

                register_rest_route( 'tindapress/v1/variants', 'activate', array(
                    'methods' => 'POST',
                    'callback' => array('TP_Activate_Variants','listen'),
                ));

                register_rest_route( 'tindapress/v1/variants/options', 'list', array(
                    'methods' => 'POST',
                    'callback' => array('TP_Var_Opt_List','listen'),
                ));

                //Pending
                register_rest_route( 'tindapress/v1/variants', 'update', array(
                    'methods' => 'POST',
                    'callback' => array('TP_Update_Variants','listen'),
                ));
        /*
        * END
        */
        /*
         * STORE VERSION TWO RESTAPI
        */
            register_rest_route( 'tindapress/v2/store', 'insert', array(
                'methods' => 'POST',
                'callback' => array('TP_Store_Insert_v2','listen'),
            ));

                // Store category
                    register_rest_route( 'tindapress/v2/store/type', 'insert', array(
                        'methods' => 'POST',
                        'callback' => array('TP_Store_Category_Insert_v2','listen'),
                    ));
                // Documents
                    register_rest_route( 'tindapress/v2/store/document/type', 'insert', array(
                        'methods' => 'POST',
                        'callback' => array('TP_Store_Doc_type_Insert_v2','listen'),
                    ));


        /*
         * END
        */
    }
    add_action( 'rest_api_init', 'tindapress_route' );