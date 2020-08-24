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
		
		
		//Database table creation for stores
		if($wpdb->get_var( "SHOW TABLES LIKE '$tbl_stores'" ) != $tbl_stores) {
			$sql = "CREATE TABLE `".$tbl_stores."` (";
				$sql .= "`ID` bigint(20) NOT NULL AUTO_INCREMENT, ";
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
		if($wpdb->get_var( "SHOW TABLES LIKE '$tbl_roles_meta'" ) != $tbl_roles_meta) {
			$sql = "CREATE TABLE `".$tbl_roles_meta."` (";
				$sql .= "`ID` bigint(20) NOT NULL AUTO_INCREMENT, ";
				$sql .= "`roid` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Role id this belong to.', ";
				$sql .= "`access` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Access key', ";
				$sql .= "`status` enum('inactive','active') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'inactive' COMMENT 'Role access is active or not.', ";
				$sql .= "`date_created` datetime(0) NULL DEFAULT NULL COMMENT 'The date this role meta created.', ";
				$sql .= "PRIMARY KEY (`ID`) ";
				$sql .= ") ENGINE = InnoDB; ";
			$result = $wpdb->get_results($sql);
		}

		//Database table creation for roles
		if($wpdb->get_var( "SHOW TABLES LIKE '$tbl_roles'" ) != $tbl_roles) {
			$sql = "CREATE TABLE `".$tbl_roles."` (";
				$sql .= "`ID` bigint(20) NOT NULL AUTO_INCREMENT, ";
				$sql .= "`stid` int(11) NOT NULL DEFAULT 0 COMMENT 'Store id this roles belong.', ";
				$sql .= "`title` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Name of this role with revision id.', ";
				$sql .= "`info` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Info about this role with revision id.', ";
				$sql .= "`created_by` bigint(20) NOT NULL DEFAULT 0 COMMENT 'User who created this role.', ";
				$sql .= "`date_created` datetime(0) NULL DEFAULT NULL COMMENT 'The date this roles created.', ";
				$sql .= "PRIMARY KEY (`ID`) ";
				$sql .= ") ENGINE = InnoDB; ";
			$result = $wpdb->get_results($sql);
		}

		//Database table creation for revisions
		if($wpdb->get_var( "SHOW TABLES LIKE '$tbl_revisions'" ) != $tbl_revisions) {
			$sql = "CREATE TABLE `".$tbl_revisions."` (";
				$sql .= "`ID` bigint(20) NOT NULL AUTO_INCREMENT, ";
				$sql .= "`revs_type` enum('none','configs','categories','documents','stores','products','personnels','roles','variants') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Target table', ";
				$sql .= "`parent_id` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Parent ID of this Revision', ";
				$sql .= "`child_key` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Column name on the table', ";
				$sql .= "`child_val` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Text Value of the row Key.', ";
				$sql .= "`created_by` bigint(20) NOT NULL DEFAULT 0 COMMENT 'User ID created this Revision.', ";
				$sql .= "`date_created` datetime(0) NULL DEFAULT NULL COMMENT 'The date this Revision is created.', ";
				$sql .= "PRIMARY KEY (`ID`) ";
				$sql .= ") ENGINE = InnoDB; ";
			$result = $wpdb->get_results($sql);
		}

		//Database table creation for products 
		if($wpdb->get_var( "SHOW TABLES LIKE '$tbl_products'" ) != $tbl_products) {
			$sql = "CREATE TABLE `".$tbl_products."` (";
				$sql .= "`ID` bigint(20) NOT NULL AUTO_INCREMENT, ";
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
				$sql .= "`stid` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Store id.', ";
				$sql .= "`wpid` bigint(20) NOT NULL DEFAULT 0 COMMENT 'User id.', ";
				$sql .= "`roid` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Role id.', ";
				$sql .= "`pincode` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'User store access.', ";
				$sql .= "`status` enum('inactive','active') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'inactive' COMMENT 'If personal is actively working in this store.', ";
				$sql .= "`created_by` bigint(20) NOT NULL DEFAULT 0 COMMENT 'The user who added this personnel.', ";
				$sql .= "`date_created` datetime(0) NULL DEFAULT NULL COMMENT 'The date this personnel entry is created.', ";
				$sql .= "PRIMARY KEY (`ID`) ";
				$sql .= ") ENGINE = InnoDB; ";
			$result = $wpdb->get_results($sql);
		}

		//Database table creation for documents
		if($wpdb->get_var( "SHOW TABLES LIKE '$tbl_configs'" ) != $tbl_configs) {
			$sql = "CREATE TABLE `".$tbl_configs."` (";
				$sql .= "`ID` bigint(20) NOT NULL AUTO_INCREMENT, ";
				$sql .= "`config_desc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Config Description', ";
				$sql .= "`config_key` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Config KEY', ";
				$sql .= "`config_value` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Config VALUES', ";
				$sql .= "PRIMARY KEY (`ID`) ";
				$sql .= ") ENGINE = InnoDB; ";
			$result = $wpdb->get_results($sql);
		}

		//Database table creation for plugin_config
		if($wpdb->get_var( "SHOW TABLES LIKE '$tbl_docu'" ) != $tbl_docu) {
			$sql = "CREATE TABLE `".$tbl_docu."` (";
				$sql .= "`ID` bigint(20) NOT NULL AUTO_INCREMENT, ";
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
				$sql .= "`title` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Name of the Category with revision id.',";
				$sql .= "`info` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Description of the Category with revision id.', ";
				$sql .= "`status` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Status of the category with revision id.', ";
				$sql .= "`name` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Global = 1 , local = 0', ";
				$sql .= "`parent` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Parent if value is more than 0', ";
				$sql .= "`types` enum('none','product','store', 'tags') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none' COMMENT 'Type of category with revision id.', ";
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
				// $sql .= "`status` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Status from revision id.', ";
				$sql .= "`parent_id` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Parent id from this table 0 if this row is parent.', ";
				// $sql .= "`orders ` tinyint(2)  NOT NULL DEFAULT 0 COMMENT 'Arrangement of variants.', ";
				$sql .= "`pdid` bigint(20) NOT NULL DEFAULT 0 COMMENT 'Product id from revision.', ";
				$sql .= "`created_by` bigint(20) NOT NULL DEFAULT 0 COMMENT 'User who created this variant.', ";
				$sql .= "`date_created` datetime(0) NULL DEFAULT NULL COMMENT 'The date this variant is created.', ";
				$sql .= "PRIMARY KEY (`ID`) ";
				$sql .= ") ENGINE = InnoDB; ";
			$result = $wpdb->get_results($sql);
		}


	
    } 
    add_action( 'activated_plugin', 'tp_dbhook_activate' );
