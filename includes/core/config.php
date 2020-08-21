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
	define('TP_CUSTOM', 'eCommerce'); 
	define('TP_PREFIX', 'tp_'); 
	
	//Categories config
	define('TP_CATEGORIES_TABLE', TP_PREFIX.'categories');
	define('TP_CATEGORIES_GROUP_TABLE', TP_PREFIX.'categories_group');
	define('TP_CATEGORIES_FIELDS','(stid, title, info, status, types, created_by, date_created )');

	//Configs config
	define('TP_CONFIGS_TABLE', TP_PREFIX.'configs');

	//Documents config
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

	//Personnels Config
	define('TP_PERSONNELS_TABLE', TP_PREFIX.'personnels');

	//Products Config
	define('TP_PRODUCT_TABLE', TP_PREFIX.'products');
	define('TP_PRODUCT_FIELDS', '(stid, ctid, title, preview, short_info, long_info, status, sku, price,  weight,  dimension , created_by, date_created)');

	//Revision Config
	define('TP_REVISIONS_TABLE', TP_PREFIX.'revisions');
	define('TP_REVISION_FIELDS','(revs_type, parent_id, child_key , child_val, created_by, date_created )');

	//Roles Config
	define('TP_ROLES_TABLE', TP_PREFIX.'roles');
	define('TP_ROLES_META_TABLE', TP_PREFIX.'roles_meta');

	//Stores config
	define('TP_STORES_TABLE', TP_PREFIX.'stores');
	define('TP_STORES_FIELDS','(ctid, title, short_info, long_info, logo, banner, status, address, created_by, date_created )');

	//Variants Config
	define('TP_VARIANTS_TABLE', TP_PREFIX.'variants');
	define('TP_VARIANTS_FIELDS','(parent_id, pdid, created_by, date_created )');



	//Store WP Menu
	define('TP_MENU_STARTED', TP_PREFIX.'getting-started');
	define('TP_MENU_CATEGORY', TP_PREFIX.'categories');
	define('TP_MENU_STORE', TP_PREFIX.'stores');
	define('TP_MENU_PRODUCT', TP_PREFIX.'products');
	define('TP_MENU_VARIANT', TP_PREFIX.'variants');
	define('TP_MENU_SETTING', TP_PREFIX.'settings');

