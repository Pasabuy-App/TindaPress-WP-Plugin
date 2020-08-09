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

	//Defining Global Variables
	define('TP_PREFIX', 'tp_'); 
	define('MP_PREFIX', 'mp_'); 
	
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
	define('TP_CATEGORIES_GROUP_TABLE', TP_PREFIX.'categories_group');
	define('TP_CATEGORIES_FIELDS','(title, info, status, types, created_by, date_created )');
	
	//tp_configs config
	define('TP_CONFIGS_TABLE', TP_PREFIX.'configs');

	//Orders Config (Note : The prefix used here is MP which is mobilePOS)
	define('MP_ORDERS_TABLE', MP_PREFIX.'orders');
	define('MP_ORDER_ITEMS_TABLE', MP_PREFIX.'order_items');


	//Personnels Config
	define('TP_PERSONNELS_TABLE', TP_PREFIX.'personnels');

	//Roles Config
	define('TP_ROLES_TABLE', TP_PREFIX.'roles');
	define('TP_ROLES_META_TABLE', TP_PREFIX.'roles_meta');
	define('TP_ROLES_ACCESS_TABLE', TP_PREFIX.'roles_access');

	// Required Documents PEFIX
	define('TP_DOCU_TABLE', TP_PREFIX.'documents');
	define('DTI_REG', 'dti_registration');
	define('BRGY_CLR', 'barangay_clearance');
	define('LEASE_CONTR', 'lease_contract');
	define('COMNTY_TAX', 'community_tax');
	define('OCCT_PERMIT', 'occupancy_permit');
	define('SANTY_PERMIT', 'sanitary_permit');
	define('FIRE_PERMIT', 'fire_permit');
	define('MYRS_PERMIT', 'mayors_permit');
	define('DOCS_FIELDS', 'stid, preview, doctype');
	define('PREVIEW', 'preview');
	define('DOCUMENTS', 'documents');