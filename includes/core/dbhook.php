<?php
	// Exit if accessed directly
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	/**
	 * @package tindapress-wp-plugin
     * @version 0.1.0
     * Here is where you add hook to WP to create our custom database if not found.
	*/

	function tp_dbhook_activate() {

		global $wpdb;

		// Hardening QA 11:56 8/31/2020
		// Miguel Igdalino

		//Initializing table name
		$tbl_categories = TP_CATEGORIES_TABLE;
		$tbl_cat_group = TP_CATEGORIES_GROUP_TABLE;
		$tbl_configs = TP_CONFIGS_TABLE;
		$tbl_docu = TP_DOCU_TABLE;
		$tbl_personnels = TP_PERSONNELS_TABLE;
		$tbl_products = TP_PRODUCT_TABLE;
		$tbl_revisions = TP_REVISIONS_TABLE;
		$tbl_roles = TP_ROLES_TABLE;
		$tbl_roles_meta = TP_ROLES_META_TABLE;
		$tbl_stores = TP_STORES_TABLE;
		$tbl_variants = TP_VARIANTS_TABLE;
		$tbl_access = TP_ACCESS_TABLE;
		$tbl_access_fields = TP_ACCESS_TABLE_FIELDS;
		$tbl_access_data = TP_ACCESS_VALUE;
		$tbl_wishlist = TP_WISHLIST_TABLE;
		$tbl_featured_store = TP_FEATURED_STORE_TABLE;
		$tbl_featured_store_seen = TP_FEATURED_STORE__SEEN_TABLE;
		$tbl_schedule = TP_SCHEDULE;
		$tbl_stores_view = TP_STORES_VIEW;
		$tbl_products_view = TP_PRODUCTS_VIEW;

		$wpdb->query("START TRANSACTION");

		//Database table creation for stores
		if($wpdb->get_var( "SHOW TABLES LIKE '$tbl_stores'" ) != $tbl_stores) {
			$sql = "CREATE TABLE `".$tbl_stores."` (";
				$sql .= "`ID` bigint(20) NOT NULL AUTO_INCREMENT, ";
				$sql .= "`hash_id` varchar(255) NOT NULL COMMENT 'Hash of id.', ";
				$sql .= "`ctid` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Category of this Store with revision ID.', ";
				$sql .= "`title` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Name of the store with revision ID.',  ";
				$sql .= "`short_info` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Description of the store with revision ID.', ";
				$sql .= "`long_info` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Description of the store with revision ID.', ";
				$sql .= "`logo` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Logo of the store with revision Id.', ";
				$sql .= "`banner` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Preview image url with revision ID.', ";
				$sql .= "`status` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Status of the store with revision ID.', ";
				$sql .= "`address` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Address id of this store, 0 if not set', ";
				$sql .= "`created_by` bigint(20) NOT NULL DEFAULT 0 COMMENT 'User id who created this store', ";
				$sql .= "`date_created` datetime(0) NULL DEFAULT NULL COMMENT 'The date this store was created.', ";
				$sql .= "PRIMARY KEY (`ID`) ";
				$sql .= ") ENGINE = InnoDB; ";
			$result = $wpdb->get_results($sql);
		}

		//Database table creation for roles_meta
		if($wpdb->get_var( "SHOW TABLES LIKE '$tbl_access'" ) != $tbl_access) {
			$sql = "CREATE TABLE `".$tbl_access."` (";
				$sql .= " `ID` bigint(20) NOT NULL AUTO_INCREMENT, ";
				$sql .= "  `hash_id` varchar(255) NOT NULL,";
				$sql .= "  `access` varchar(255) NOT NULL, ";
				$sql .= "PRIMARY KEY (`ID`) ";
				$sql .= ") ENGINE = InnoDB; ";
			$result = $wpdb->get_results($sql);

			$wpdb->query("INSERT INTO $tbl_access (hash_id, access) VALUES  $tbl_access_data ");
		}

		//Database table creation for roles
		if($wpdb->get_var( "SHOW TABLES LIKE '$tbl_roles'" ) != $tbl_roles) {
			$sql = "CREATE TABLE `".$tbl_roles."` (";
				$sql .= " `ID` bigint(20) NOT NULL AUTO_INCREMENT, ";
				$sql .= " `hash_id` varchar(255) NOT NULL COMMENT 'Hash of id.', ";
				$sql .= " `title` bigint(20) NOT NULL DEFAULT 0 COMMENT 'User id who have access.', ";
				$sql .= " `info` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Store id this roles belong.', ";
				$sql .= " `stid` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Store id this roles belong.', ";
				$sql .= " `status` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Status of role.', ";
				$sql .= " `created_by` bigint(20) NOT NULL DEFAULT 0 COMMENT 'User who created this role.', ";
				$sql .= " `date_created` datetime(0) NULL DEFAULT current_timestamp() COMMENT 'The date this roles created.', ";
				$sql .= "PRIMARY KEY (`ID`) ";
				$sql .= ") ENGINE = InnoDB; ";
			$result = $wpdb->get_results($sql);

		}

		//Database table creation for roles_meta
		if($wpdb->get_var( "SHOW TABLES LIKE '$tbl_roles_meta'" ) != $tbl_roles_meta) {
			$sql = "CREATE TABLE `".$tbl_roles_meta."` (";
				$sql .= "`ID` bigint(20) NOT NULL AUTO_INCREMENT, ";
				$sql .= "`hash_id` varchar(255) NOT NULL COMMENT 'Hash of id.', ";
				$sql .= " `roid` bigint(20) NOT NULL DEFAULT '0' COMMENT 'Role ID', ";
				$sql .= " `status` tinyint(5) NOT NULL DEFAULT '0' COMMENT 'Role ID', ";
				$sql .= " `acsid` bigint(20)  NOT NULL DEFAULT '0' COMMENT 'Access ID .', ";
				$sql .= " `date_created` datetime DEFAULT current_timestamp() COMMENT 'The date this role meta created.', ";
				$sql .= " PRIMARY KEY (`ID`) ";
				$sql .= ") ENGINE = InnoDB; ";
			$result = $wpdb->get_results($sql);

		}

		//Database table creation for revisions
		if($wpdb->get_var( "SHOW TABLES LIKE '$tbl_revisions'" ) != $tbl_revisions) {
			$sql = "CREATE TABLE `".$tbl_revisions."` (";
				$sql .= "`ID` bigint(20) NOT NULL AUTO_INCREMENT, ";
				$sql .= "`hash_id` varchar(255) NOT NULL COMMENT 'Hash of id.', ";
				$sql .= "`revs_type` enum('none','configs','categories','documents','stores','products','personnels','roles','variants') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Target table', ";
				$sql .= "`parent_id` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Parent ID of this Revision', ";
				$sql .= "`child_key` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Column name on the table', ";
				$sql .= "`child_val` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Text Value of the row Key.', ";
				$sql .= "`created_by` bigint(20) NOT NULL DEFAULT 0 COMMENT 'User ID created this Revision.', ";
				$sql .= "`date_created` datetime(0) NULL DEFAULT NULL COMMENT 'The date this Revision is created.', ";
				$sql .= "PRIMARY KEY (`ID`) ";
				$sql .= ") ENGINE = InnoDB; ";
			$result = $wpdb->get_results($sql);

			$conf_list_value = TP_CONFIGS_DATA;
			$rev_fields = TP_REVISION_FIELDS;

			$wpdb->query("INSERT INTO `".$tbl_revisions."` $rev_fields VALUES $conf_list_value");
		}

		//Database table creation for products
		if($wpdb->get_var( "SHOW TABLES LIKE '$tbl_products'" ) != $tbl_products) {
			$sql = "CREATE TABLE `".$tbl_products."` (";
				$sql .= "`ID` bigint(20) NOT NULL AUTO_INCREMENT, ";
				$sql .= "`hash_id` varchar(255) NOT NULL COMMENT 'Hash of id.', ";
				$sql .= "`stid` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Store id of this product.', ";
				$sql .= "`ctid` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Category id of this product.', ";
				$sql .= "`title` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Name of the store with revision ID.', ";
				$sql .= "`preview` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Preview image url with revision ID.', ";
				$sql .= "`short_info` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Description of the store with revision ID.', ";
				$sql .= "`long_info` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Description of the store with revision ID.',";
				$sql .= "`status` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Can be active or inactive, 0 being inactive.', ";
				$sql .= "`sku` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Stock Keeping unit with revision ID.', ";
				$sql .= "`price` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Price of the product with revision ID.', ";
				$sql .= "`weight` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Weight of the product with revision ID.', ";
				$sql .= "`dimension` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Dimension of this product.', ";
				$sql .= "`created_by` bigint(20) NOT NULL DEFAULT 0 COMMENT 'User who created this product.', ";
				$sql .= "`date_created` datetime(0) NULL DEFAULT NULL COMMENT 'The date this product is created.', ";
				$sql .= "PRIMARY KEY (`ID`) ";
				$sql .= ") ENGINE = InnoDB; ";
			$result = $wpdb->get_results($sql);
		}

		//Database table creation for personnels
		if($wpdb->get_var( "SHOW TABLES LIKE '$tbl_personnels'" ) != $tbl_personnels) {
			$sql = "CREATE TABLE `".$tbl_personnels."` (";
				$sql .= "`ID` bigint(20) NOT NULL AUTO_INCREMENT, ";
				$sql .= "`hash_id` varchar(255) NOT NULL COMMENT 'Hash of id.', ";
				$sql .= "`stid` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Store id.', ";
				$sql .= "`wpid` bigint(20) NOT NULL DEFAULT 0 COMMENT 'User id.', ";
				$sql .= "`roid` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Role id.', ";
				$sql .= "`pincode` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'User store access.', ";
				$sql .= "`status` enum('inactive','active') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'inactive' COMMENT 'If personal is actively working in this store.', ";
				$sql .= "`created_by` bigint(20) NOT NULL DEFAULT 0 COMMENT 'The user who added this personnel.', ";
				$sql .= "`date_created` datetime(0) NULL DEFAULT current_timestamp() COMMENT 'The date this personnel entry is created.', ";
				$sql .= "PRIMARY KEY (`ID`) ";
				$sql .= ") ENGINE = InnoDB; ";
			$result = $wpdb->get_results($sql);
		}

		//Database table creation for documents
		if($wpdb->get_var( "SHOW TABLES LIKE '$tbl_configs'" ) != $tbl_configs) {
			$sql = "CREATE TABLE `".$tbl_configs."` (";
				$sql .= "`ID` bigint(20) NOT NULL AUTO_INCREMENT, ";
				$sql .= "`hash_id` varchar(255) NOT NULL COMMENT 'Hash of id.', ";
				$sql .= "`title` varchar(255)  NOT NULL COMMENT 'Config Title', ";
				$sql .= "`info` varchar(255)  NOT NULL COMMENT 'Config Information', ";
				$sql .= "`config_key` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Config KEY', ";
				$sql .= "`config_value` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Config VALUES', ";
				$sql .= "PRIMARY KEY (`ID`) ";
				$sql .= ") ENGINE = InnoDB; ";
			$result = $wpdb->get_results($sql);
			$conf_fields  = TP_CONFIGS_FIELDS;
			$conf_list = TP_CONFIGS_VALUE;
			$wpdb->query("INSERT INTO `".$tbl_configs."` ($conf_fields) VALUES $conf_list");
		}

		//Database table creation for plugin_config
		if($wpdb->get_var( "SHOW TABLES LIKE '$tbl_docu'" ) != $tbl_docu) {
			$sql = "CREATE TABLE `".$tbl_docu."` (";
				$sql .= "`ID` bigint(20) NOT NULL AUTO_INCREMENT, ";
				$sql .= "`hash_id` varchar(255) NOT NULL COMMENT 'Hash of id.', ";
				$sql .= "`stid` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Store ID of Merchant', ";
				$sql .= "`preview` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Image url of document', ";
				$sql .= "`doctype` enum('none','dti_registration','barangay_clearance','lease_contract','community_tax','occupancy_permit','sanitary_permit','fire_permit','mayors_permit') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Document type', ";
				$sql .= "`status` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Revision ID of status', ";
				$sql .= "`approved_by` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Pasabuy Staff User ID who approved this document', ";
				$sql .= "`date_created` datetime(0) NULL DEFAULT NULL COMMENT 'Date document was created', ";
				$sql .= "PRIMARY KEY (`ID`) ";
				$sql .= ") ENGINE = InnoDB; ";
			$result = $wpdb->get_results($sql);
		}
		//Database table creation for categories
		if($wpdb->get_var( "SHOW TABLES LIKE '$tbl_categories'" ) != $tbl_categories) {
			$sql = "CREATE TABLE `".$tbl_categories."` (";
				$sql .= "`ID` bigint(20) NOT NULL AUTO_INCREMENT, ";
				$sql .= "`hash_id` varchar(255) NOT NULL COMMENT 'Hash of id.', ";
				$sql .= "`title` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Name of the Category with revision id.',";
				$sql .= "`info` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Description of the Category with revision id.', ";
				$sql .= "`status` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Status of the category with revision id.', ";
				$sql .= "`name` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Global = 1 , local = 0', ";
				$sql .= "`parent` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Parent if value is more than 0', ";
				$sql .= " `types` enum('none','product','store','tags','branch') CHARACTER SET utf8mb4 NOT NULL DEFAULT 'none' COMMENT 'Type of category with revision id.', ";
				$sql .= "  `groups` enum('inhouse','robinson') NOT NULL COMMENT 'Groups of category either inhouse or robinson', ";
				$sql .= "`stid` bigint(20) NOT NULL COMMENT 'Store ID',";
				$sql .= "`created_by` bigint(20) NOT NULL DEFAULT 0 COMMENT 'User created this category with revision id.', ";
				$sql .= "`date_created` datetime(0) NULL DEFAULT NULL COMMENT 'The date this category is created.', ";
				$sql .= "PRIMARY KEY (`ID`) ";
				$sql .= ") ENGINE = InnoDB; ";
			$result = $wpdb->get_results($sql);
		}

		//Database table creation for categories gropu
		if($wpdb->get_var( "SHOW TABLES LIKE '$tbl_cat_group'" ) != $tbl_cat_group) {
			$sql = "CREATE TABLE `".$tbl_cat_group."` (";
				$sql .= "`ID` bigint(20) NOT NULL AUTO_INCREMENT, ";
				$sql .= "`hash_id` varchar(255) NOT NULL COMMENT 'Hash of id.', ";
				$sql .= "`ctid` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Category id', ";
				$sql .= "`types` enum('none','product','store') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none' COMMENT 'Type of category with revision id.', ";
				$sql .= "`row_id` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Row which this group belongs to', ";
				$sql .= "`created_by` bigint(20) NOT NULL DEFAULT 0 COMMENT 'User created this category with revision id.', ";
				$sql .= "`date_created` datetime(0) NULL DEFAULT NULL COMMENT 'The date this category is created.', ";
				$sql .= "PRIMARY KEY (`ID`) ";
				$sql .= ") ENGINE = InnoDB; ";
			$result = $wpdb->get_results($sql);
		}

		//Database table creation for variants
		if($wpdb->get_var( "SHOW TABLES LIKE '$tbl_variants'" ) != $tbl_variants) {
			$sql = "CREATE TABLE `".$tbl_variants."` (";
				$sql .= "`ID` bigint(20) NOT NULL AUTO_INCREMENT, ";
				$sql .= "`hash_id` varchar(255) NOT NULL COMMENT 'Hash of id.', ";
				$sql .= "`parent_id` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Parent id from this table 0 if this row is parent.', ";
				$sql .= "`pdid` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Product id from revision.', ";
				$sql .= "`created_by` bigint(20) NOT NULL DEFAULT 0 COMMENT 'User who created this variant.', ";
				$sql .= "`isRequired` enum('no', 'yes') NOT NULL DEFAULT 0 COMMENT 'If this variant is required or not.', ";
				$sql .= "`date_created` datetime(0) NULL DEFAULT NULL COMMENT 'The date this variant is created.', ";
				$sql .= "PRIMARY KEY (`ID`) ";
				$sql .= ") ENGINE = InnoDB; ";
			$result = $wpdb->get_results($sql);
		}

		//Database table creation for wishlist
		if($wpdb->get_var( "SHOW TABLES LIKE '$tbl_wishlist'" ) != $tbl_wishlist) {
			$sql = "CREATE TABLE `".$tbl_wishlist."` (";
				$sql .= " `ID` bigint(20) NOT NULL AUTO_INCREMENT, ";
				$sql .= " `hash_id` varchar(255) NOT NULL COMMENT 'Hash of id.', ";
				$sql .= " `product_id` bigint(20) DEFAULT NULL, ";
				$sql .= " `status` tinyint(2) NOT NULL, ";
				$sql .= " `created_by` bigint(20) NOT NULL, ";
				$sql .= " `date_created` datetime DEFAULT current_timestamp() COMMENT 'The date this wishlist created.', ";
				$sql .= "PRIMARY KEY (`ID`) ";
				$sql .= ") ENGINE = InnoDB; ";
			$result = $wpdb->get_results($sql);
		}

		//Database table creation for wishlist
		if($wpdb->get_var( "SHOW TABLES LIKE '$tbl_featured_store'" ) != $tbl_featured_store) {
			$sql = "CREATE TABLE `".$tbl_featured_store."` (";
				$sql .= "  `ID` bigint(20) NOT NULL AUTO_INCREMENT, ";
				$sql .= "  `type` enum('food','store','market') NOT NULL, ";
				$sql .= "  `stid` bigint(20) NOT NULL, ";
				$sql .= "  `logo` varchar(255) NOT NULL, ";
				$sql .= "  `banner` varchar(255) NOT NULL, ";
				$sql .= "  `created_by` bigint(20) NOT NULL, ";
				$sql .= "  `status` enum('active','inactive') NOT NULL, ";
				$sql .= "  `date_created` datetime DEFAULT current_timestamp() COMMENT 'The date and time created this wishlist.', ";
				$sql .= "PRIMARY KEY (`ID`) ";
				$sql .= ") ENGINE = InnoDB; ";
			$result = $wpdb->get_results($sql);
		}

		if($wpdb->get_var( "SHOW TABLES LIKE '$tbl_featured_store_seen'" ) != $tbl_featured_store_seen) {
			$sql = "CREATE TABLE `".$tbl_featured_store_seen."` (";
				$sql .= "  `ID` bigint(20) NOT NULL AUTO_INCREMENT, ";
				$sql .= "  `fid` bigint(20) NOT NULL, ";
				$sql .= "  `wpid` bigint(20) NOT NULL, ";
				$sql .= "  `date_created` datetime DEFAULT current_timestamp() COMMENT 'The date and time created this wishlist.', ";
				$sql .= "PRIMARY KEY (`ID`) ";
				$sql .= ") ENGINE = InnoDB; ";
			$result = $wpdb->get_results($sql);
		}

		if($wpdb->get_var( "SHOW TABLES LIKE '$tbl_schedule'" ) != $tbl_schedule) {
			$sql = "CREATE TABLE `".$tbl_schedule."` (";
				$sql .= "  `ID`   bigint(20) NOT NULL AUTO_INCREMENT, ";
				$sql .= "  `stid` bigint(20) NOT NULL, ";
				$sql .= "  `type`  enum('mon','tue', 'wed', 'thu', 'fri', 'sat', 'sun') NOT NULL, ";
				$sql .= "  `open` time NOT NULL, ";
				$sql .= "  `close` time NOT NULL, ";
				$sql .= "  `created_by` bigint(20) NOT NULL, ";
				$sql .= "  `date_created` datetime DEFAULT current_timestamp() COMMENT 'The date and time created this schedule.', ";
				$sql .= "PRIMARY KEY (`ID`) ";
				$sql .= ") ENGINE = InnoDB; ";
			$result = $wpdb->get_results($sql);
		}

		/**
		 * Mysql Views
		*/
			// Store View
			if($wpdb->get_var( "SHOW CREATE VIEW $tbl_stores_view" ) != $tbl_stores_view) {
				$sql = "CREATE ALGORITHM=UNDEFINED  VIEW  `".$tbl_stores_view."` AS SELECT";
					$sql .= "   str.ID,
					str.ctid AS `catid`,
					str.address AS `add_id`,
					IF(( SELECT rev.child_val FROM tp_revisions rev WHERE rev.parent_id = str.ID AND  rev.date_created = ( SELECT MAX(date_created) FROM tp_revisions tp_rev WHERE tp_rev.ID = rev.ID AND revs_type = 'stores'  ) AND child_key ='isPartner' )is null ,
					'false', IF( ( SELECT rev.child_val FROM tp_revisions rev WHERE rev.parent_id = str.ID AND  rev.date_created = ( SELECT MAX(date_created) FROM tp_revisions tp_rev WHERE tp_rev.ID = rev.ID AND revs_type = 'stores'  ) AND child_key ='isPartner' ) = 'false', 'false', 'true'  )) AS `partner`,
					CONCAT(( SELECT rev.child_val FROM tp_revisions rev WHERE rev.parent_id = str.ID AND  rev.date_created = ( SELECT MAX(date_created) FROM tp_revisions tp_rev WHERE tp_rev.ID = rev.ID AND revs_type = 'stores'  ) AND child_key ='commission' ), '%') AS comm,
					( SELECT rev.child_val FROM tp_revisions rev WHERE rev.ID = cat.title  AND rev.date_created = (SELECT MAX(tp_rev.date_created) FROM tp_revisions tp_rev WHERE ID = rev.ID  AND revs_type ='categories'   )  ) as cat_name,
					( SELECT rev.child_val FROM tp_revisions rev WHERE rev.id = str.title AND  rev.date_created = ( SELECT MAX(date_created) FROM tp_revisions tp_rev WHERE tp_rev.ID = rev.ID AND revs_type = 'stores' )  ) AS title,
					( SELECT rev.child_val FROM tp_revisions rev WHERE rev.id = str.short_info AND  rev.date_created = ( SELECT MAX(date_created) FROM tp_revisions tp_rev WHERE tp_rev.ID = rev.ID AND revs_type = 'stores' ) ) AS short_info,
					( SELECT rev.child_val FROM tp_revisions rev WHERE rev.id = str.long_info AND  rev.date_created = ( SELECT MAX(date_created) FROM tp_revisions tp_rev WHERE tp_rev.ID = rev.ID AND revs_type = 'stores' ) ) AS long_info,
					( SELECT rev.child_val FROM tp_revisions rev WHERE rev.id = str.logo AND  rev.date_created = ( SELECT MAX(date_created) FROM tp_revisions tp_rev WHERE tp_rev.ID = rev.ID AND revs_type = 'stores' ) ) AS avatar,
					( SELECT rev.child_val FROM tp_revisions rev WHERE rev.id = str.banner AND  rev.date_created = ( SELECT MAX(date_created) FROM tp_revisions tp_rev WHERE tp_rev.ID = rev.ID AND revs_type = 'stores' ) ) AS banner,
					IF ( ( SELECT rev.child_val FROM tp_revisions rev WHERE rev.id = str.`status` AND date_created = (SELECT MAX(tp_rev.date_created) FROM tp_revisions tp_rev WHERE tp_rev.ID = rev.ID AND tp_rev.child_key = 'status')   ) = 1, 'Active', 'Inactive' ) AS `status`,
					( SELECT dv_rev.child_val FROM dv_revisions  dv_rev WHERE dv_rev.ID = `add`.street AND dv_rev.date_created = (SELECT MAX(date_created)  FROM dv_revisions WHERE ID = dv_rev.ID AND revs_type ='address')   ) AS street,
					( SELECT brgy_name FROM dv_geo_brgys WHERE ID = ( SELECT dv_rev.child_val FROM dv_revisions dv_rev WHERE dv_rev.id = `add`.brgy  AND dv_rev.date_created = (SELECT MAX(date_created)  FROM dv_revisions WHERE ID = dv_rev.ID AND revs_type ='address') ) ) AS brgy,
					( SELECT city_name FROM dv_geo_cities WHERE city_code = ( SELECT dv_rev.child_val FROM dv_revisions dv_rev WHERE dv_rev.id = `add`.city  AND dv_rev.date_created = (SELECT MAX(date_created)  FROM dv_revisions WHERE ID = dv_rev.ID AND revs_type ='address')  ) ) AS city,
					( SELECT prov_name FROM dv_geo_provinces WHERE prov_code = ( SELECT dv_rev.child_val FROM dv_revisions dv_rev WHERE dv_rev.id = `add`.province AND dv_rev.date_created = (SELECT MAX(date_created)  FROM dv_revisions WHERE ID = dv_rev.ID AND revs_type ='address')  ) ) AS province,
					( SELECT country_name FROM dv_geo_countries WHERE id = ( SELECT dv_rev.child_val FROM dv_revisions dv_rev WHERE dv_rev.id = `add`.country  AND dv_rev.date_created = (SELECT MAX(date_created)  FROM dv_revisions WHERE ID = dv_rev.ID AND revs_type ='address')  ) ) AS country,
					( SELECT dv_rev.child_val FROM dv_revisions  dv_rev WHERE dv_rev.ID = `add`.latitude AND dv_rev.date_created = (SELECT MAX(date_created)  FROM dv_revisions WHERE ID = dv_rev.ID AND revs_type ='address') AND child_key ='latitude' AND revs_type ='address'  ) AS `lat`,
					( SELECT dv_rev.child_val FROM dv_revisions  dv_rev WHERE dv_rev.ID = `add`.longitude AND dv_rev.date_created = (SELECT MAX(date_created)  FROM dv_revisions WHERE ID = dv_rev.ID AND revs_type ='address') AND child_key ='longitude' AND revs_type ='address'  ) AS `long`,
					( SELECT child_val FROM dv_revisions WHERE ID = ( SELECT revs FROM dv_contacts WHERE types = 'phone' AND stid = str.ID LIMIT 1 ) LIMIT 1 ) AS phone,
					( SELECT child_val FROM dv_revisions WHERE ID = ( SELECT revs FROM dv_contacts  WHERE types = 'email' AND stid = str.ID LIMIT 1 ) LIMIT 1 ) AS email
				FROM
					tp_stores str
					INNER JOIN dv_address `add` ON str.address = `add`.ID
					INNER JOIN tp_categories cat ON cat.ID = str.ctid ";
				$result = $wpdb->get_results($sql);
			}

			// Store View
			if($wpdb->get_var( "SHOW CREATE VIEW $tbl_products_view" ) != $tbl_products_view) {
				$sql = "CREATE ALGORITHM=UNDEFINED  VIEW  `".$tbl_products_view."` AS SELECT";
					$sql .= "    tp_prod.ID,
					tp_prod.stid,
					tp_prod.ctid AS catid,
					( SELECT COUNT(pdid) FROM tp_variants WHERE pdid = tp_prod.ID AND parent_id = 0 ) as `total`,
					( SELECT tp_rev.child_val FROM tp_revisions tp_rev WHERE ID = ( SELECT `title` FROM tp_stores WHERE ID = tp_prod.stid ) AND revs_type = 'stores' AND child_key ='title' AND tp_rev.ID = (SELECT MAX(ID) FROM tp_revisions WHERE ID = tp_rev.ID )  ) AS `store_name`,
					( SELECT tp_rev.child_val FROM tp_revisions tp_rev WHERE ID = c.title AND revs_type = 'categories' AND child_key ='title' AND tp_rev.ID = (SELECT MAX(ID) FROM tp_revisions WHERE ID = tp_rev.ID ) ) AS `cat_name`,
					( SELECT tp_rev.child_val FROM tp_revisions tp_rev WHERE tp_rev.ID = tp_prod.title  AND revs_type = 'products' AND child_key ='title' AND tp_rev.ID = (SELECT MAX(ID) FROM tp_revisions WHERE ID = tp_rev.ID )  ) AS product_name,
					( SELECT tp_rev.child_val FROM tp_revisions tp_rev WHERE ID = tp_prod.short_info  AND revs_type = 'products' AND child_key ='short_info' AND tp_rev.ID = (SELECT MAX(ID) FROM tp_revisions WHERE ID = tp_rev.ID ) ) AS `short_info`,
					( SELECT tp_rev.child_val FROM tp_revisions tp_rev WHERE ID = tp_prod.long_info AND revs_type = 'products' AND child_key ='long_info' AND tp_rev.ID = (SELECT MAX(ID) FROM tp_revisions WHERE ID = tp_rev.ID )  ) AS `long_info`,
					( SELECT tp_rev.child_val FROM tp_revisions tp_rev WHERE ID = tp_prod.sku   AND revs_type = 'products' AND child_key ='sku' AND tp_rev.ID = (SELECT MAX(ID) FROM tp_revisions WHERE ID = tp_rev.ID ) ) AS `sku`,
					( SELECT tp_rev.child_val FROM tp_revisions tp_rev WHERE ID = tp_prod.price AND revs_type = 'products' AND child_key ='price' AND tp_rev.ID = (SELECT MAX(ID) FROM tp_revisions WHERE ID = tp_rev.ID )  ) AS `price`,
					( SELECT tp_rev.child_val FROM tp_revisions tp_rev WHERE ID = tp_prod.weight AND revs_type = 'products' AND child_key ='weight' AND tp_rev.ID = (SELECT MAX(ID) FROM tp_revisions WHERE ID = tp_rev.ID )  ) AS `weight`,
					( SELECT tp_rev.child_val FROM tp_revisions tp_rev WHERE ID = tp_prod.preview AND revs_type = 'products' AND child_key ='preview' AND tp_rev.ID = (SELECT MAX(ID) FROM tp_revisions WHERE ID = tp_rev.ID )  ) AS `preview`,
					( SELECT tp_rev.child_val FROM tp_revisions tp_rev WHERE ID = tp_prod.dimension AND revs_type = 'products' AND child_key ='dimension' AND tp_rev.ID = (SELECT MAX(ID) FROM tp_revisions WHERE ID = tp_rev.ID ) ) AS `dimension`,

					IF  ( ( SELECT tp_rev.child_val FROM tp_revisions tp_rev WHERE ID = tp_prod.`status` AND revs_type = 'products' AND child_key = 'status' AND tp_rev.ID = ( SELECT MAX(ID) FROM tp_revisions WHERE ID = tp_rev.ID )  ) = 1, 'Active', 'Inactive' ) AS `status`,
					null as discount
				FROM
					tp_products tp_prod
					INNER JOIN tp_revisions tp_rev ON tp_rev.ID = tp_prod.title
					INNER JOIN tp_categories c ON c.ID = tp_prod.ctid  ";
				$result = $wpdb->get_results($sql);
			}


		$wpdb->query("COMMIT");

    }
    add_action( 'activated_plugin', 'tp_dbhook_activate' );
