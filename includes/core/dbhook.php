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
?>
<?php
	
	function tp_dbhook_activate() {
		
		global $wpdb;

		//Initializing table name
		$tbl_products = PRODUCT_TABLE;
		$tbl_address = ADDRESS_TABLE;
		$tbl_categories = CATEGORIES_TABLE;
		$tbl_orders = ORDERS_TABLE;
		$tbl_order_items = ORDERS_ITEMS_TABLE;
		$tbl_personnels = PERSONNELS_TABLE;
		$tbl_plugin_config = PLUGIN_CONFIG_TABLE;
		$tbl_revisions = REVISIONS_TABLE;
		$tbl_roles = ROLES_TABLE;
		$tbl_roles_access = ROLES_ACCESS_TABLE;
		$tbl_stores = STORES_TABLE;


		//Initializing table name for meta
		$tbl_order_meta = TP_PREFIX.'order_meta';
		$tbl_products_meta = TP_PREFIX.'products_meta';
		$tbl_roles_meta = TP_PREFIX.'roles_meta';

		//Database table creation for stores
		if($wpdb->get_var( "SHOW TABLES LIKE '$tbl_stores'" ) != $tbl_stores) {
			$sql = "CREATE TABLE `".$tbl_stores."` (";
				$sql .= "`ID` bigint(20) NOT NULL AUTO_INCREMENT, ";
				$sql .= "`s_name` VARCHAR(50) NOT NULL, ";
				$sql .= "`s_info` VARCHAR(250) NULL, ";
				$sql .= "`address_id` int(11) NOT NULL, ";
				$sql .= "PRIMARY KEY (`ID`) ";
				$sql .= ") ENGINE = InnoDB; ";
			$result = $wpdb->get_results($sql);
		}

		//Database table creation for roles_meta
		if($wpdb->get_var( "SHOW TABLES LIKE '$tbl_roles_meta'" ) != $tbl_roles_meta) {
			$sql = "CREATE TABLE `".$tbl_roles_meta."` (";
				$sql .= "`ID` bigint(20) NOT NULL AUTO_INCREMENT, ";
				$sql .= "`r_group` int(11) NOT NULL, ";
				$sql .= "`r_per_id` int(11) NOT NULL, ";
				$sql .= "`rm_status` tinyint(4)  NULL, ";
				$sql .= "`rm_timestamp` datetime NOT NULL, ";
				$sql .= "PRIMARY KEY (`ID`) ";
				$sql .= ") ENGINE = InnoDB; ";
			$result = $wpdb->get_results($sql);
		}

		//Database table creation for roles_access
		if($wpdb->get_var( "SHOW TABLES LIKE '$tbl_roles_access'" ) != $tbl_roles_access) {
			$sql = "CREATE TABLE `".$tbl_roles_access."` (";
				$sql .= "`ID` bigint(20) NOT NULL AUTO_INCREMENT, ";
				$sql .= "`ra_key` VARCHAR(40) NOT NULL, ";
				$sql .= "`ra_value` VARCHAR(255) NOT NULL, ";
				$sql .= "`ra_last_update` datetime  NULL, ";
				$sql .= "`ra_timestamp` datetime NOT NULL, ";
				$sql .= "PRIMARY KEY (`ID`) ";
				$sql .= ") ENGINE = InnoDB; ";
			$result = $wpdb->get_results($sql);
		}

		//Database table creation for roles
		if($wpdb->get_var( "SHOW TABLES LIKE '$tbl_roles'" ) != $tbl_roles) {
			$sql = "CREATE TABLE `".$tbl_roles."` (";
				$sql .= "`ID` bigint(20) NOT NULL AUTO_INCREMENT, ";
				$sql .= "`store_id` int(11) NOT NULL, ";
				$sql .= "`r_name` VARCHAR(40) NOT NULL, ";
				$sql .= "`r_info` VARCHAR(255)  NULL, ";
				$sql .= "`r_icon` VARCHAR(140)  NULL, ";
				$sql .= "`r_timestamp` datetime NOT NULL, ";
				$sql .= "`r_created_by` int(11) NOT NULL, ";
				$sql .= "PRIMARY KEY (`ID`) ";
				$sql .= ") ENGINE = InnoDB; ";
			$result = $wpdb->get_results($sql);
		}

		//Database table creation for revisions
		if($wpdb->get_var( "SHOW TABLES LIKE '$tbl_revisions'" ) != $tbl_revisions) {
			$sql = "CREATE TABLE `".$tbl_revisions."` (";
				$sql .= "`ID` bigint(20) NOT NULL AUTO_INCREMENT, ";
				$sql .= "`product_id` int(11) NOT NULL, ";
				$sql .= "`r_timestamp` datetime NOT NULL, ";
				$sql .= "`r_created_by` int(11) NOT NULL, ";
				$sql .= "PRIMARY KEY (`ID`) ";
				$sql .= ") ENGINE = InnoDB; ";
			$result = $wpdb->get_results($sql);
		}

		//Database table creation for products_meta
		if($wpdb->get_var( "SHOW TABLES LIKE '$tbl_products_meta'" ) != $tbl_products_meta) {
			$sql = "CREATE TABLE `".$tbl_products_meta."` (";
				$sql .= "`ID` bigint(20) NOT NULL AUTO_INCREMENT, ";
				$sql .= "`pm_key` VARCHAR(100) NOT NULL, ";
				$sql .= "`pm_value` VARCHAR(100) NOT NULL, ";
				$sql .= "`pm_barcode` int(11) NOT NULL, ";
				$sql .= "`pm_image` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`pm_image`)),";
				$sql .= "`pm_thumbnail` VARCHAR(150) NULL, ";
				$sql .= "`pm_price` float NOT NULL, ";
				$sql .= "`pm_weight` decimal(11,2) DEFAULT NULL, ";
				$sql .= "`pm_dimensions` VARCHAR(40) DEFAULT NULL, ";
				$sql .= "`pm_inventory` tinyint(1) NOT NULL, ";
				$sql .= "`pm_status` VARCHAR(50) NOT NULL, ";
				$sql .= "`pm_short_info` VARCHAR(50) NOT NULL, ";
				$sql .= "`pm_long_info` VARCHAR(250) NOT NULL, ";
				$sql .= "`revision_id` int(11) NOT NULL, ";
				$sql .= "PRIMARY KEY (`ID`) ";
				$sql .= ") ENGINE = InnoDB; ";
			$result = $wpdb->get_results($sql);
		}


		//Database table creation for plugin_config
		if($wpdb->get_var( "SHOW TABLES LIKE '$tbl_plugin_config'" ) != $tbl_plugin_config) {
			$sql = "CREATE TABLE `".$tbl_plugin_config."` (";
				$sql .= "`ID` bigint(20) NOT NULL AUTO_INCREMENT, ";
				$sql .= "`pc_key` VARCHAR(100) NOT NULL, ";
				$sql .= "`pc_value` VARCHAR(100) NOT NULL, ";
				$sql .= "PRIMARY KEY (`ID`) ";
				$sql .= ") ENGINE = InnoDB; ";
			$result = $wpdb->get_results($sql);
		}

		//Database table creation for personnels
		if($wpdb->get_var( "SHOW TABLES LIKE '$tbl_personnels'" ) != $tbl_personnels) {
			$sql = "CREATE TABLE `".$tbl_personnels."` (";
				$sql .= "`ID` bigint(20) NOT NULL AUTO_INCREMENT, ";
				$sql .= "`store_id` int(11) NOT NULL, ";
				$sql .= "`user_id` int(11) NOT NULL, ";
				$sql .= "`role_group_id` int(11) NOT NULL, ";
				$sql .= "`per_pin` int(11) NULL, ";
				$sql .= "`per_status` int(11) NOT NULL, ";
				$sql .= "PRIMARY KEY (`ID`) ";
				$sql .= ") ENGINE = InnoDB; ";
			$result = $wpdb->get_results($sql);
		}

		//Database table creation for order_meta
		if($wpdb->get_var( "SHOW TABLES LIKE '$tbl_order_meta'" ) != $tbl_order_meta) {
			$sql = "CREATE TABLE `".$tbl_order_meta."` (";
				$sql .= "`ID` bigint(20) NOT NULL AUTO_INCREMENT, ";
				$sql .= "`om_key` VARCHAR(100) NOT NULL, ";
				$sql .= "`om_value` VARCHAR(100) NOT NULL, ";
				$sql .= "`order_id` int(11) NOT NULL, ";
				$sql .= "`om_accept_date` datetime  NULL, ";
				$sql .= "`om_accept_who` int(11)  NULL, ";
				$sql .= "`om_prepared_date` datetime  NULL, ";
				$sql .= "`om_prepared_who` int(11)  NULL, ";
				$sql .= "`om_delivered_date` datetime  NULL, ";
				$sql .= "`om_delivered_who` int(11)  NULL, ";
				$sql .= "PRIMARY KEY (`ID`) ";
				$sql .= ") ENGINE = InnoDB; ";
			$result = $wpdb->get_results($sql);
		}

		//Database table creation for order_items
		if($wpdb->get_var( "SHOW TABLES LIKE '$tbl_order_items'" ) != $tbl_order_items) {
			$sql = "CREATE TABLE `".$tbl_order_items."` (";
				$sql .= "`ID` bigint(20) NOT NULL AUTO_INCREMENT, ";
				$sql .= "`order_id` int(11) NOT NULL, ";
				$sql .= "`product_id` int(11) NOT NULL, ";
				$sql .= "`oi_quantity` int(11) NOT NULL, ";
				$sql .= "PRIMARY KEY (`ID`) ";
				$sql .= ") ENGINE = InnoDB; ";
			$result = $wpdb->get_results($sql);
		}

		//Database table creation for orders 
		if($wpdb->get_var( "SHOW TABLES LIKE '$tbl_orders'" ) != $tbl_orders) {
			$sql = "CREATE TABLE `".$tbl_orders."` (";
				$sql .= "`ID` bigint(20) NOT NULL AUTO_INCREMENT, ";
				$sql .= "`store_ops_id` int(11) NOT NULL, ";
				$sql .= "`user_id` int(11) NOT NULL, ";
				$sql .= "`store_id` int(11) NOT NULL, ";
				$sql .= "`o_timestamp` datetime NOT NULL, ";
				$sql .= "`o_status` tinyint(4) NOT NULL, ";
				$sql .= "PRIMARY KEY (`ID`) ";
				$sql .= ") ENGINE = InnoDB; ";
			$result = $wpdb->get_results($sql);
		}

		//Database table creation for categories 
		if($wpdb->get_var( "SHOW TABLES LIKE '$tbl_categories'" ) != $tbl_categories) {
			$sql = "CREATE TABLE `".$tbl_categories."` (";
				$sql .= "`ID` bigint(20) NOT NULL AUTO_INCREMENT, ";
				$sql .= "`c_name` varchar(120) NOT NULL, ";
				$sql .= "`c_info` varchar(120) NULL, ";
				$sql .= "`c_icon` varchar(50)  NULL, ";
				$sql .= "`c_created_by` int(11) NOT NULL, ";
				$sql .= "`c_timestamp` datetime NOT NULL, ";
				$sql .= "`c_last_update` datetime NULL, ";
				$sql .= "PRIMARY KEY (`ID`) ";
				$sql .= ") ENGINE = InnoDB; ";
			$result = $wpdb->get_results($sql);
		}

		////Database table creation for address 
		if($wpdb->get_var( "SHOW TABLES LIKE '$tbl_address'" ) != $tbl_address) {
			$sql = "CREATE TABLE `".$tbl_address."` (";
				$sql .= "`ID` bigint(20) NOT NULL AUTO_INCREMENT, ";
				$sql .= "`user_id` int(11) NOT NULL, ";
				$sql .= "`store_id` int(11) NOT NULL DEFAULT 0, ";
				$sql .= "`supplier_id` int(11) NOT NULL DEFAULT 0, ";
				$sql .= "`a_street` varchar(120) NULL, ";
				$sql .= "`a_brgy_id` int(11) NOT NULL, ";
				$sql .= "`a_city_id` int(11) NOT NULL, ";
				$sql .= "`a_province_id` int(11) NOT NULL, ";
				$sql .= "`a_country_id` int(11) NOT NULL, ";
				$sql .= "`a_timestamp` datetime NOT NULL, ";
				$sql .= "`a_last_update` datetime NULL, ";
				$sql .= "PRIMARY KEY (`ID`) ";
				$sql .= ") ENGINE = InnoDB; ";
			$result = $wpdb->get_results($sql);
		}

		//Database table creation for products 
		if($wpdb->get_var( "SHOW TABLES LIKE '$tbl_products'" ) != $tbl_products) {
			$sql = "CREATE TABLE `".$tbl_products."` (";
				$sql .= "`ID` bigint(20) NOT NULL AUTO_INCREMENT, ";
				$sql .= "`category_id` int(11) NOT NULL, ";
				$sql .= "`store_id` int(11) NOT NULL, ";
				$sql .= "`p_name` varchar(120) NOT NULL, ";
				$sql .= "`p_timestamp` datetime NOT NULL, ";
				$sql .= "`p_created_by` datetime NOT NULL, ";
				$sql .= "`p_last_update` datetime NULL, ";
				$sql .= "`revision_id` int(11) NOT NULL, ";
				$sql .= "PRIMARY KEY (`ID`) ";
				$sql .= ") ENGINE = InnoDB; ";
			$result = $wpdb->get_results($sql);
		}

    } 
    add_action( 'activated_plugin', 'tp_dbhook_activate' );



?>
