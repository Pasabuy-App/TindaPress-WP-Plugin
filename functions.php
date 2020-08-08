<?php

    /*
        Plugin Name: TindaPress WP Plugin
        Plugin URI: http://www.bytescrafter.net/projects/tindapress
        Description: TindaPress WP Plugin is an e-commerce plugin lorem ipsum.
        Version: 0.1.0
        Author: Bytes Crafter
        Author URI:   https://www.bytescrafter.net/about-us
        Text Domain:  tindapress-wp-plugin

        * @package      tindapress-wp-plugin
        * @author       Bytes Crafter

        * @copyright    2020 Bytes Crafter
        * @version      0.1.0

        * @wordpress-plugin
        * WC requires at least: 2.5.0
        * WC tested up to: 5.4.2
    */

    #region WP Recommendation - Prevent direct initilization of the plugin.
    if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly
    
    if ( ! function_exists( 'is_plugin_active' ) ) 
    {
        require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    }
    
    #endregion

    define("TP_PLUGIN_PATH", plugin_dir_path( __FILE__ ) );
    define("TP_PLUGIN_URL", plugin_dir_url( __FILE__ ) );

    //Important config files and plugin updates.
    include_once ( TP_PLUGIN_PATH . '/includes/core/config.php' );
    include_once ( TP_PLUGIN_PATH . '/includes/core/update.php' );

    // include_once ( TP_PLUGIN_PATH . '/includes/view/primary.php' );

     //Make sure to create required mysql tables.
    include_once ( TP_PLUGIN_PATH . '/includes/core/dbhook.php' );

    //Includes assets if page is defined.
    include_once ( TP_PLUGIN_PATH . '/includes/core/assets.php' );

    //Include the REST API of USocketNet to be accessible.
    include_once ( TP_PLUGIN_PATH . '/includes/api/routes.php' );
?>