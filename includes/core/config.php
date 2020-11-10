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

		// Config declaration for version two
			define('TP_PREFIX_v2', 'tp_v2_');

			define('TP_STORES_v2', TP_PREFIX_v2.'stores');
			define('TP_STORES_FIELDS_v2', ' `scid`, `title`, `info`, `adid`, `created_by`');

			define('TP_STORES_CATEGORIES_v2', TP_PREFIX_v2.'stores_categories');
			define('TP_STORES_CATEGORIES_FIELDS_v2', ' `title`, `info`, `groups`, `avatar`, `created_by`');

			define('TP_STORES_DOCS_TYPES_v2', TP_PREFIX_v2.'store_doc_types');
			define('TP_STORES_DOCS_TYPES_FIELDS_v2', ' `title`, `info`, `created_by`');

			define('TP_STORES_DOCS_v2', TP_PREFIX_v2.'store_documents');
			define('TP_STORES_DOCS_FIELDS_v2', ' `stid`, `preview`, `types`, `comments`');

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
			define('TP_PRODUCT_FIELDS_v2', ' `stid`, `pcid`, `title`, `info`, `price`, `discount`, `inventory`, `created_by` ');

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



