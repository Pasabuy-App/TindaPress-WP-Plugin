<?php
	// Exit if accessed directly
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	/**
	 * @package tindapress-wp-plugin
     * @version 0.1.0
     * This is where you include CSS and JS files using WP enqueue script functions.
	*/

	//Initializing table fields to be called
	$checkUSNget = isset($_GET['page']);
    $checkUSNarr = array(
        'tp-getting_started',
        'tp-store_browser',
        'tp-settings',
    );
    

	if( $checkUSNget && in_array($_GET['page'], $checkUSNarr) )
    {
        function tindapress_plugin_admin_enqueue()
        {    
            wp_enqueue_script( 'usn_popper_script', TP_PLUGIN_URL . 'assets/popper/popper.min.js' ); 
            wp_enqueue_script( 'usn_clipboard_script', TP_PLUGIN_URL . 'assets/clipboard/clipboard.min.js' );    
            wp_enqueue_script( 'usn_chartjs_script', TP_PLUGIN_URL . 'assets/chartjs/chart.min.js' );
            wp_enqueue_script( 'usn_handlebars_script', TP_PLUGIN_URL . 'assets/handlebars/handlebars.js' );
            
            wp_enqueue_style( 'usn_bootstrap_style', TP_PLUGIN_URL . 'assets/bootstrap/css/bootstrap.min.css' );
            wp_enqueue_script( 'usn_bootstrap_script', TP_PLUGIN_URL . 'assets/bootstrap/js/bootstrap.min.js' );

            wp_enqueue_style( 'usn_datatables_style', TP_PLUGIN_URL . 'assets/datatables/datatables.min.css' );
            wp_enqueue_script( 'usn_datatables_script', TP_PLUGIN_URL . 'assets/datatables/datatables.min.js' );

            wp_enqueue_style( 'usn_jqueryui_style', TP_PLUGIN_URL . 'assets/jquery-ui/jquery-ui.min.css' );
            wp_enqueue_script( 'usn_jqueryui_script', TP_PLUGIN_URL . 'assets/jquery-ui/jquery-ui.min.js' );

            wp_enqueue_style( 'usn_admin_style', TP_PLUGIN_URL . 'assets/styles/main.css' );
            wp_localize_script( 'usn_admin_script', 'ajaxurl', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
        }
        add_action( 'admin_enqueue_scripts', 'tindapress_plugin_admin_enqueue' );
    }