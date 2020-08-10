
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
            add_menu_page(TP_CUSTOM, TP_CUSTOM, 'manage_options', 'tp-getting_started', 
                'tindapress_gettingstarted_page_callback', TP_PLUGIN_URL . '/icon.png', 4 );

            add_submenu_page('tp-getting_started', 'TP Getting Started', 'Getting Started',
                'manage_options', 'tp-getting_started' );

            add_submenu_page('tp-getting_started', 'TP Store Browser', 'Stores',
                'manage_options', 'tp-store_browser', 'tindapress_store_browser_page_callback' );

            add_submenu_page('tp-getting_started', 'TP Product Browser', 'Products',
                'manage_options', 'tp-product_browser', 'tindapress_product_browser_page_callback' );

            add_submenu_page('tp-getting_started', 'TP Category Browser', 'Categories',
                'manage_options', 'tp-category_browser', 'tindapress_category_browser_page_callback' );

             add_submenu_page('tp-getting_started', 'TP Settings', 'Settings',
                'manage_options', 'tp-settings', 'tindapress_setting_page_callback' );
        }
        add_action('admin_menu', 'tindapress_init_admin_menu');

        function tindapress_gettingstarted_page_callback()
        {
            include_once( TP_PLUGIN_PATH . '/includes/view/menu/getting-started.php' );
        }

        function tindapress_store_browser_page_callback()
        {
            include_once( TP_PLUGIN_PATH . '/includes/view/menu/store-browser.php' );
        }

        function tindapress_product_browser_page_callback()
        {
            include_once( TP_PLUGIN_PATH . '/includes/view/menu/product-browser.php' );
        }

        function tindapress_category_browser_page_callback()
        {
            include_once( TP_PLUGIN_PATH . '/includes/view/menu/category-browser.php' );
        }

        function tindapress_setting_page_callback()
        {
            include_once( TP_PLUGIN_PATH . '/includes/view/menu/settings.php' );
        }
    #endregion
