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

	//Products Config
	define('TP_PRODUCT_TABLE', TP_PREFIX.'products');
	define('TP_PRODUCT_FIELDS', '(stid, ctid, title, preview, short_info, long_info, status, sku, price,  weight,  dimension , created_by, date_created)');

	//Revision Config
	define('TP_REVISION_TABLE', TP_PREFIX.'revisions');
	define('TP_REVISION_FIELDS','(revs_type, parent_id, child_key , child_val, created_by, date_created )');

	//Stores config
	define('TP_STORES_TABLE', TP_PREFIX.'stores');
	define('TP_STORES_FIELDS','(ctid, title, short_info, long_info, logo, banner, status, address, created_by, date_created )');

	//Categories config
	define('TP_CATEGORIES_TABLE', TP_PREFIX.'categories');
	define('TP_CATEGORIES_FIELDS','(title, info, status, types, created_by, date_created )');
	
	//tp_configs config
	define('TP_CONFIGS_TABLE', TP_PREFIX.'configs');


	define('TP_PERSONNELS_TABLE', TP_PREFIX.'personnels');

	define('TP_ROLES_TABLE', TP_PREFIX.'roles');

	define('TP_ROLES_META_TABLE', TP_PREFIX.'roles_meta');

	define('TP_ADDRESS_TABLE', TP_PREFIX.'address');

	define('TP_ORDERS_TABLE', TP_PREFIX.'orders');

	define('TP_ORDERS_ITEMS_TABLE', TP_PREFIX.'orders_items');

	define('TP_ROLES_ACCESS_TABLE', TP_PREFIX.'roles_access');

	define('TP_DOCU_TABLE', TP_PREFIX.'documents');

	// Required Documents PEFIX
	define('DTI_REG', 'dti_registration');
	define('BRGY_CLR', 'barangay_clearance');
	define('LEASE_CONTR', 'lease_contract');
	define('COMNTY_TAX', 'community_tax');
	define('OCCT_PERMIT', 'occupancy_permit');
	define('SANTY_PERMIT', 'sanitary_permit');
	define('FIRE_PERMIT', 'fire_permit');
	define('MYRS_PERMIT', 'mayors_permit');

	// tp_document fields
	define('DOCS_FIELDS', 'stid, preview, doctype');
	// document child key in tp_revisions
	define('PREVIEW', 'preview');
	// document revision type
	define('DOCUMENTS', 'documents');


?>