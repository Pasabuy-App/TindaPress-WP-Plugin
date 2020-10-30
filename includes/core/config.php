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

		// Hardening QA 11:59 8/31/2020
		// Miguel Igdalino

		// Config declaration for version one

			//Defining Global Variables
			define('TP_CUSTOM', 'eCommerce');
			define('TP_PREFIX', 'tp_');
			define('TP_UIHOST', 'http://localhost/wordpress/');
			define('TP_FULLMODE', true);

			// Views
			define('TP_STORES_VIEW', TP_PREFIX.'stores_view');
			define('TP_PRODUCTS_VIEW', TP_PREFIX.'products_view');

			//Categories config
			define('TP_CATEGORIES_TABLE', TP_PREFIX.'categories');
			define('TP_CATEGORIES_GROUP_TABLE', TP_PREFIX.'categories_group');
			define('TP_CATEGORIES_FIELDS','(stid, title, info, status, types, created_by, date_created )');

			//Configs config
			define('TP_CONFIGS_TABLE', TP_PREFIX.'configs');
			define('TP_CONFIGS_FIELDS', '`title`, `info`, `config_key`, `config_value`');
			define('TP_CONFIGS_VALUE', $tp_config_vals);
			define('TP_CONFIGS_DATA', $tp_config_value);

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

			//Products Featured store
			define('TP_FEATURED_STORE_TABLE', TP_PREFIX.'featured_store');
			define('TP_FEATURED_STORE_FIELDS', '`type`, `stid`, `logo`, `banner`, `created_by`');

			define('TP_FEATURED_STORE__SEEN_TABLE', TP_PREFIX.'featured_store_seen');
			define('TP_FEATURED_STORE__SEEN_FIELDS', 'wpid, fid');

			// Schedule Config
			define('TP_SCHEDULE', TP_PREFIX.'schedule');
			define('TP_SCHEDULE_FILEDS', 'stid, type, open, close, created_by');


			//Products Config
			define('TP_PRODUCT_TABLE', TP_PREFIX.'products');
			define('TP_PRODUCT_FIELDS', '(stid, ctid, title, preview, short_info, long_info, status, sku, price,  weight,  dimension , created_by, date_created)');

			//Revision Config
			define('TP_REVISIONS_TABLE', TP_PREFIX.'revisions');
			define('TP_REVISION_FIELDS','(revs_type, parent_id, child_key , child_val, created_by, date_created )');


			//Roles Config
			define('TP_WISHLIST_TABLE', TP_PREFIX.'wishlist');
			define('TP_WISHLIST_FIELDS','`product_id`, `status`, `created_by`');

			define('TP_ACCESS_TABLE', TP_PREFIX.'access');
			define('TP_ACCESS_VALUE', $tp_access_val);
			define('TP_ACCESS_TABLE_FIELDS', 'access');

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
		// End

		// Config declaration for version two
			define('TP_PREFIX_v2', 'tp_v2_');

			define('TP_STORES_v2', TP_PREFIX_v2.'stores');
			define('TP_STORES_FIELDS_v2', ' `scid`, `title`, `info`, `adid`, `created_by`');

			define('TP_STORES_CATEGORIES_v2', TP_PREFIX_v2.'stores_categories');
			define('TP_STORES_CATEGORIES_FIELDS_v2', ' `title`, `info`, `groups` `avatar`, `created_by`');

			define('TP_STORE_DOCS_TYPES_v2', TP_PREFIX_v2.'store_doc_types');
			define('TP_STORE_DOCS_TYPES_FIELDS_v2', ' `title`, `info`, `created_by`');

			define('TP_STORES_RATINGS_v2', TP_PREFIX_v2.'store_rating');
			define('TP_STORES_RATINGS_FIELDS_v2', ' `stid`, `rates`, `comments`, `rated_by`');

			define('TP_STORES_SEEN_v2', TP_PREFIX_v2.'stores_seen');
			define('TP_STORES_SEEN_FIELDS_v2', ' `stid`, `wpid`');

			define('TP_FEATURED_STORES_GROUPS_v2', TP_PREFIX_v2.'featured_store_group');
			define('TP_FEATURED_STORES_GROUPS_FIELDS_v2', ' `title`, `info`,`created_by` ');

			define('TP_FEATURED_STORES_v2', TP_PREFIX_v2.'featured_store');
			define('TP_FEATURED_STORES_FIELDS_v2', ' `stid`, `groups`, `created_by` ');

			define('TP_FEATURED_STORES_SEEN_v2', TP_PREFIX_v2.'featured_store');
			define('TP_FEATURED_STORES_SEEN_FIELDS_v2', ' `ftid`, `wpid` ');

			define('TP_STORES_CANCELLED_v2', TP_PREFIX_v2.'store_cancelled');
			define('TP_STORES_CANCELLED_FIELDS_v2', ' `stid`, `comments`, `executed_by` ');

			define('TP_PRODUCT_CATEGORY_v2', TP_PREFIX_v2.'products_categories');
			define('TP_PRODUCT_CATEGORY_FIELDS_v2', ' `stid`, `title`, `info`, `created_by` ');

			define('TP_PRODUCT_v2', TP_PREFIX_v2.'products');
			define('TP_PRODUCT_FIELDS_v2', ' `stid`, `pcid`, `title`, `info`, `price`, `discount`, `inventory` `created_by` ');

			define('TP_PRODUCT_VARIANTS_v2', TP_PREFIX_v2.'product_variants');
			define('TP_PRODUCT_VARIANTS_FILEDS_v2', ' `pdid`, `title`, `info`, `price`, `required`, `created_by` ');

			define('TP_PRODUCT_RATING_v2', TP_PREFIX_v2.'products_ratings');
			define('TP_PRODUCT_RATING_FIELDS_v2', ' `pdid`, `rates`, `comments`, `rated_by` ');

			define('TP_FEATURED_PRODUCT_v2', TP_PREFIX_v2.'featured_products');
			define('TP_FEATURED_PRODUCT_FIELDS_v2', ' `stid`, `pdid`, `created_by` ');

			define('TP_FEATURED_PRODUCT_SEEN_v2', TP_PREFIX_v2.'featured_products_seen');
			define('TP_FEATURED_PRODUCT_SEEN_FIELDS_v2', ' `pfid`, `wpid` ');

			define('TP_STORES_CATEGORY_GROUPS_v2', TP_PREFIX_v2.'stores_categories_groups');
			define('TP_STORES_CATEGORY_GROUPS_FIELDS_v2', ' `title`, `info`, `created_by` ');

		// End



