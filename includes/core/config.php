<?php
	// Exit if accessed directly
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	/**
	 * @package tindapress-wp-plugin
     * @version 0.1.0
     * This is where you provide all the constant config.
	*/
?>
<?php

	//Defining Global Variables
	define('TP_PREFIX', 'tp_'); 

	//Initializing table names
	define('STORES_TABLE', TP_PREFIX.'stores');
	define('STORES_REVS_TABLE', TP_PREFIX.'stores_revs');
	define('CATEGORIES_TABLE', TP_PREFIX.'categories');
	define('CATEGORIES_REVS_TABLE', TP_PREFIX.'categories_revs');
	define('PRODUCT_TABLE', TP_PREFIX.'product');
	define('PRODUCT_REVS_TABLE', TP_PREFIX.'products_revs');

	//Initializing table fields to be called
	define('POST_FIELDS', array('ID', 'user_id', 'post_type'));

?>