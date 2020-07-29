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
	define('TP_REVISION', TP_PREFIX.'revisions');

	define('TP_STORES_TABLE', TP_PREFIX.'stores');
	
	define('TP_STORES_REVS_TABLE', TP_PREFIX.'stores_revs');
	
	define('TP_CATEGORIES_TABLE', TP_PREFIX.'categories');
	
	define('TP_CATEGORIES_REVS_TABLE', TP_PREFIX.'categories_revs');

	define('TP_PRODUCT_TABLE', TP_PREFIX.'products');
	
	define('TP_PRODUCT_REVS_TABLE', TP_PREFIX.'products_revs');

	define('TP_ADDRESS_TABLE', TP_PREFIX.'address');

	define('TP_ORDERS_TABLE', TP_PREFIX.'orders');

	define('TP_ORDERS_ITEMS_TABLE', TP_PREFIX.'orders_items');

	define('TP_PERSONNELS_TABLE', TP_PREFIX.'personnels');
	
	define('TP_PLUGIN_CONFIG_TABLE', TP_PREFIX.'plugin_config');

	define('TP_REVISIONS_TABLE', TP_PREFIX.'revisions');

	define('TP_ROLES_TABLE', TP_PREFIX.'roles');

	define('TP_ROLES_ACCESS_TABLE', TP_PREFIX.'roles_access');



	//Initializing table fields to be called

?>