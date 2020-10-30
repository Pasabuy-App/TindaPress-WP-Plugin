<?php
	// Exit if accessed directly
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	/**
	 * @package tindapress-wp-plugin
     * @version 0.1.0
     * Data for TindaPress config.
    */
	$date = date("Y-m-d H:i:s");

	$tp_config_list = "('URL for e-commerce related APIs', 'Url to get the url for the REST APIs.', 'url_api', 'https://api.pasabuy.app');
	;";

	$tp_config_vals = "('Required Distance value','This config the value for calculating distance by radius', 'distance', '0');";

	$tp_config_value = "('configs','0', 'distance', '6', '1', '$date');";
