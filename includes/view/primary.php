
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
            add_menu_page('TindaPress', 'TindaPress', 'manage_options', 'tp-getting_started', 
                'tindapress_gettingstarted_page_callback', TP_PLUGIN_URL . '/icon.png', 4 );

            add_submenu_page('tp-getting_started', 'TP Getting Started', 'Getting Started',
                'manage_options', 'tp-getting_started' );

            add_submenu_page('tp-getting_started', 'TP Store Browser', 'Store Browser',
                'manage_options', 'tp-store_browser', 'tindapress_store_browser_page_callback' );

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

        function tindapress_setting_page_callback()
        {
            include_once( TP_PLUGIN_PATH . '/includes/view/menu/settings.php' );
        }
    #endregion
