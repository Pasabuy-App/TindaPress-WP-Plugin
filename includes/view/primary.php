
<?php
	// Exit if accessed directly
	if ( ! defined( 'ABSPATH' ) ) 
	{
		exit;
	}

    /**
	 * @package tindapress-wp-plugin
     * @version 0.1.0
    */

    #region Initilized new admin menu for this plugin including submenus.
        function tindapress_init_admin_menu() 
        {
            // Add new menu to the admin page.
            add_menu_page(TP_CUSTOM, TP_CUSTOM, 'manage_options', TP_MENU_STARTED, 
                'tindapress_gettingstarted_page_callback', TP_PLUGIN_URL . '/icon.png', 4 );

            add_submenu_page(TP_MENU_STARTED, 'TP Getting Started', 'Getting Started',
                'manage_options', TP_MENU_STARTED );

            add_submenu_page(TP_MENU_STARTED, 'TP Category Browser', 'Categories',
                'manage_options', TP_MENU_CATEGORY, 'tindapress_category_browser_page_callback' );

            add_submenu_page(TP_MENU_STARTED, 'TP Store Browser', 'Stores',
                'manage_options', TP_MENU_STORE, 'tindapress_store_browser_page_callback' );

            add_submenu_page(TP_MENU_STARTED, 'TP Product Browser', 'Products',
                'manage_options', TP_MENU_PRODUCT, 'tindapress_product_browser_page_callback' );

            add_submenu_page(TP_MENU_STARTED, 'TP Variant Browser', 'Variants',
                'manage_options', TP_MENU_VARIANT, 'tindapress_variant_browser_page_callback' );

             add_submenu_page(TP_MENU_STARTED, 'TP Settings', 'Settings',
                'manage_options', TP_MENU_SETTING, 'tindapress_setting_page_callback' );
        }
        add_action('admin_menu', 'tindapress_init_admin_menu');

        function tindapress_gettingstarted_page_callback()
        {
            include_once( TP_PLUGIN_PATH . '/includes/view/menu/getting-started.php' );
        }

        function tindapress_category_browser_page_callback()
        {
            include_once( TP_PLUGIN_PATH . '/includes/view/menu/category-browser.php' );
        }

        function tindapress_store_browser_page_callback()
        {
            include_once( TP_PLUGIN_PATH . '/includes/view/menu/store-browser.php' );
        }

        function tindapress_product_browser_page_callback()
        {
            include_once( TP_PLUGIN_PATH . '/includes/view/menu/product-browser.php' );
        }    
        
        function tindapress_variant_browser_page_callback()
        {
            include_once( TP_PLUGIN_PATH . '/includes/view/menu/variant-browser.php' );
        } 

        function tindapress_setting_page_callback()
        {
            include_once( TP_PLUGIN_PATH . '/includes/view/menu/settings.php' );
        }
    #endregion
