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

		// Hardening QA 12:00 8/31/2020
		// Miguel Igdalino

	//Initializing table fields to be called
    $checkUSNarr = array(
        TP_MENU_STARTED,
        TP_MENU_CATEGORY,
        TP_MENU_STORE,
        TP_MENU_PRODUCT,
        TP_MENU_VARIANT,
        TP_MENU_SETTING,
    );

	if( isset($_GET['page']) && in_array($_GET['page'], $checkUSNarr) )
    {
        function tindapress_plugin_admin_enqueue()
        {
            wp_enqueue_script( 'tp_popper_script', TP_PLUGIN_URL . 'assets/popper/popper.min.js' );
            wp_enqueue_script( 'tp_clipboard_script', TP_PLUGIN_URL . 'assets/clipboard/clipboard.min.js' );
            wp_enqueue_script( 'tp_chartjs_script', TP_PLUGIN_URL . 'assets/chartjs/chart.min.js' );
            wp_enqueue_script( 'tp_handlebars_script', TP_PLUGIN_URL . 'assets/handlebars/handlebars.js' );

            wp_enqueue_style( 'tp_bootstrap_style', TP_PLUGIN_URL . 'assets/bootstrap/css/bootstrap.min.css' );
            wp_enqueue_script( 'tp_bootstrap_script', TP_PLUGIN_URL . 'assets/bootstrap/js/bootstrap.min.js' );
            wp_enqueue_style( 'tp_jqueryui_style', TP_PLUGIN_URL . 'assets/jquery-ui/jquery-ui.min.css' );

            wp_enqueue_style( 'tp_datatables_style', TP_PLUGIN_URL . 'assets/datatables/datatables.min.css' );
            wp_enqueue_script( 'tp_datatables_script', TP_PLUGIN_URL . 'assets/datatables/datatables.min.js' );

            wp_enqueue_script( 'tp_jqueryui_script', TP_PLUGIN_URL . 'assets/jquery-ui/jquery-ui.min.js' );

            wp_enqueue_style( 'tp_admin_style', TP_PLUGIN_URL . 'assets/styles/main.css' );
            wp_enqueue_script( 'tp_admin_script', TP_PLUGIN_URL . 'assets/scripts/main.js', array('jquery'), '1.0', true );
            wp_localize_script( 'tp_admin_script', 'ajaxurl', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
        }
        add_action( 'admin_enqueue_scripts', 'tindapress_plugin_admin_enqueue' );
    }