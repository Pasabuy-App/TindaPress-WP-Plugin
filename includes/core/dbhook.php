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

		// Table Declaration for Version two
			$tbl_store_v2 = TP_STORES_v2;
			$tbl_stores_category_v2 = TP_STORES_CATEGORIES_v2;
			$tbl_store_docs_types_v2 = TP_STORES_DOCS_TYPES_v2;
			$tbl_store_ratings_v2 = TP_STORES_RATINGS_v2;
			$tbl_store_seen_v2 = TP_STORES_SEEN_v2;
			$tbl_store_documents =TP_STORES_DOCS_v2;
			$tbl_featured_store_groups_v2 = TP_FEATURED_STORES_GROUPS_v2;
			$tbl_featied_store_v2 = TP_FEATURED_STORES_v2;
			$tbl_featured_store_seen_v2 = TP_FEATURED_STORES_SEEN_v2;
			$tbl_store_cancelled_v2 = TP_STORES_CANCELLED_v2;
			$tbl_product_category_v2 = TP_PRODUCT_CATEGORY_v2;
			$tbl_product_v2 = TP_PRODUCT_v2;
			$tbl_product_varints_v2 = TP_PRODUCT_VARIANTS_v2;
			$tbl_product_ratings_v2 = TP_PRODUCT_RATING_v2;
			$tbl_featured_product_v2 = TP_FEATURED_PRODUCT_v2;
			$tbl_featured_product_seen_v2 = TP_FEATURED_PRODUCT_SEEN_v2;
			$tbl_store_category_groups_v2 = TP_STORES_CATEGORY_GROUPS_v2;
		// End
		$wpdb->query("START TRANSACTION");

		// Table query for Version two
			//Database table creation for store category
			if($wpdb->get_var( "SHOW TABLES LIKE '$tbl_stores_category_v2'" ) != $tbl_stores_category_v2) {
				$sql = "CREATE TABLE `".$tbl_stores_category_v2."` (";
					$sql .= " `ID` bigint(20) NOT NULL AUTO_INCREMENT, ";
					$sql .= " `hsid` varchar(255) NOT NULL COMMENT 'This column is used for table relation ship.', ";
					$sql .= " `title` varchar(100) NOT NULL COMMENT 'Title of this store categories.', ";
					$sql .= " `info` varchar(150) COMMENT 'Info of this store categories.', ";
					$sql .= " `avatar` varchar(255) NOT NULL COMMENT 'Logo of this store categories.', ";
					$sql .= " `groups` varchar(150) NOT NULL  COMMENT 'Store category groups id.', ";
					$sql .= " `status` enum('active', 'inactive') NOT NULL  COMMENT 'Status of this store categories.', ";
					$sql .= " `created_by` bigint(20) NOT NULL  COMMENT 'The one who created this store categories.', ";
					$sql .= " `date_created` datetime NULL DEFAULT current_timestamp() COMMENT 'Date created this store categories.', ";
					$sql .= "PRIMARY KEY (`ID`) ";
					$sql .= ") ENGINE = InnoDB; ";
				$result = $wpdb->get_results($sql);
			}

			//Database table creation for store document types
			if($wpdb->get_var( "SHOW TABLES LIKE '$tbl_store_docs_types_v2'" ) != $tbl_store_docs_types_v2) {
				$sql = "CREATE TABLE `".$tbl_store_docs_types_v2."` (";
					$sql .= " `ID` bigint(20) NOT NULL AUTO_INCREMENT, ";
					$sql .= " `hsid` varchar(255) NOT NULL COMMENT 'This column is used for table relation ship.', ";
					$sql .= " `title` varchar(100) NOT NULL COMMENT 'Title of this document type.', ";
					$sql .= " `info` varchar(150)  COMMENT 'Info of this store categories.', ";
					$sql .= " `status` enum('active', 'inactive') NOT NULL  COMMENT 'Status of this store categories.', ";
					$sql .= " `created_by` bigint(20) NOT NULL  COMMENT 'The one who created this store categories.', ";
					$sql .= " `date_created` datetime NULL DEFAULT current_timestamp() COMMENT 'Date created this store categories.', ";
					$sql .= "PRIMARY KEY (`ID`) ";
					$sql .= ") ENGINE = InnoDB; ";
				$result = $wpdb->get_results($sql);
			}

			//Database table creation for store
			if($wpdb->get_var( "SHOW TABLES LIKE '$tbl_store_v2'" ) != $tbl_store_v2) {
				$sql = "CREATE TABLE `".$tbl_store_v2."` (";
					$sql .= " `ID` bigint(20) NOT NULL AUTO_INCREMENT, ";
					$sql .= " `hsid` varchar(255) NOT NULL COMMENT 'This column is used for table relation ship.', ";
					$sql .= " `scid` varchar(100) NOT NULL COMMENT 'Store category id.', ";
					$sql .= " `title` varchar(150) NOT NULL COMMENT 'Title of this store.', ";
					$sql .= " `info` varchar(150)  COMMENT 'Info of this store.', ";
					$sql .= " `avatar` varchar(150) NOT NULL COMMENT 'Logo of this store.', ";
					$sql .= " `banner` varchar(150) NOT NULL COMMENT 'Banner of this store.', ";
					$sql .= " `adid` varchar(150) NOT NULL COMMENT 'Address id of this store.', ";
					$sql .= " `status` enum('active', 'inactive') NOT NULL  COMMENT 'Status of this store.', ";
					$sql .= " `commision` varchar(50) NOT NULL, ";
					$sql .= " `created_by` bigint(20) NOT NULL  COMMENT 'The one who created this store.', ";
					$sql .= " `date_created` datetime NULL DEFAULT current_timestamp() COMMENT 'Date created this store.', ";
					$sql .= "PRIMARY KEY (`ID`), ";
					$sql .= "KEY `scid` (`scid`) USING BTREE, ";
					$sql .= "KEY `hsid` (`hsid`) USING BTREE, ";
					$sql .= "KEY `status` (`status`) USING BTREE, ";
					$sql .= "PRIMARY KEY (`ID`) ";
					$sql .= ") ENGINE = InnoDB; ";
				$result = $wpdb->get_results($sql);
			}

			//Database table creation for store
			if($wpdb->get_var( "SHOW TABLES LIKE '$tbl_store_ratings_v2'" ) != $tbl_store_ratings_v2) {
				$sql = "CREATE TABLE `".$tbl_store_ratings_v2."` (";
					$sql .= " `ID` bigint(20) NOT NULL AUTO_INCREMENT, ";
					$sql .= " `hsid` varchar(255) NOT NULL COMMENT 'This column is used for table relation ship.', ";
					$sql .= " `stid` varchar(100) NOT NULL COMMENT 'Store id.', ";
					$sql .= " `rates` tinyint(10) NOT NULL COMMENT 'Rates.', ";
					$sql .= " `comments` varchar(150)  COMMENT 'Comments for this store.', ";
					$sql .= " `rated_by` bigint(20) NOT NULL COMMENT 'The one who rated this store.', ";
					$sql .= " `date_created` datetime NULL DEFAULT current_timestamp() COMMENT 'Date created this store.', ";
					$sql .= "PRIMARY KEY (`ID`) ";
					$sql .= ") ENGINE = InnoDB; ";
				$result = $wpdb->get_results($sql);
			}

			//Database table creation for store seen
			if($wpdb->get_var( "SHOW TABLES LIKE '$tbl_store_seen_v2'" ) != $tbl_store_seen_v2) {
				$sql = "CREATE TABLE `".$tbl_store_seen_v2."` (";
					$sql .= " `ID` bigint(20) NOT NULL AUTO_INCREMENT, ";
					$sql .= " `hsid` varchar(255) NOT NULL COMMENT 'This column is used for table relation ship.', ";
					$sql .= " `stid` varchar(100) NOT NULL COMMENT '	 id.', ";
					$sql .= " `wpid` bigint(20) NOT NULL COMMENT 'The user who seen this store.', ";
					$sql .= " `date_created` datetime NULL DEFAULT current_timestamp() COMMENT 'Date created this store.', ";
					$sql .= "PRIMARY KEY (`ID`) ";
					$sql .= ") ENGINE = InnoDB; ";
				$result = $wpdb->get_results($sql);
			}

			//Database table creation for store featured store groups
			if($wpdb->get_var( "SHOW TABLES LIKE '$tbl_featured_store_groups_v2'" ) != $tbl_featured_store_groups_v2) {
				$sql = "CREATE TABLE `".$tbl_featured_store_groups_v2."` (";
					$sql .= " `ID` bigint(20) NOT NULL AUTO_INCREMENT, ";
					$sql .= " `hsid` varchar(255) NOT NULL COMMENT 'This column is used for table relation ship.', ";
					$sql .= " `title` varchar(100) NOT NULL COMMENT 'title of this featured store groups.', ";
					$sql .= " `info` varchar(255) COMMENT 'Info of this store.', ";
					$sql .= " `status` enum('active', 'inactive') NOT NULL COMMENT 'Status of this featured store .', ";
					$sql .= " `created_by` bigint(20) NOT NULL COMMENT 'The user who create this featured store groups.', ";
					$sql .= " `date_created` datetime NULL DEFAULT current_timestamp() COMMENT 'Date created this store.', ";
					$sql .= "PRIMARY KEY (`ID`) ";
					$sql .= ") ENGINE = InnoDB; ";
				$result = $wpdb->get_results($sql);
			}


			//Database table creation for featured store
			if($wpdb->get_var( "SHOW TABLES LIKE '$tbl_featied_store_v2'" ) != $tbl_featied_store_v2) {
				$sql = "CREATE TABLE `".$tbl_featied_store_v2."` (";
					$sql .= " `ID` bigint(20) NOT NULL AUTO_INCREMENT, ";
					$sql .= " `hsid` varchar(255) NOT NULL COMMENT 'This column is used for table relation ship.', ";
					$sql .= " `stid` varchar(150) NOT NULL COMMENT 'Store ID of this featured store groups.', ";
					$sql .= " `groups` varchar(100) NOT NULL COMMENT 'Groups of this featured store .', ";
					$sql .= " `avatar` varchar(255) NOT NULL COMMENT 'Avatar of this store.', ";
					$sql .= " `banner` varchar(255) NOT NULL COMMENT 'Banner of this store.', ";
					$sql .= " `status` enum('active', 'inactive') NOT NULL COMMENT 'Status of this featured store .', ";
					$sql .= " `created_by` bigint(20) NOT NULL COMMENT 'The user who create this featured store groups.', ";
					$sql .= " `date_created` datetime NULL DEFAULT current_timestamp() COMMENT 'Date created this store.', ";
					$sql .= "PRIMARY KEY (`ID`) ";
					$sql .= ") ENGINE = InnoDB; ";
				$result = $wpdb->get_results($sql);
			}

			//Database table creation for featured store seen
			if($wpdb->get_var( "SHOW TABLES LIKE '$tbl_featured_store_seen_v2'" ) != $tbl_featured_store_seen_v2) {
				$sql = "CREATE TABLE `".$tbl_featured_store_seen_v2."` (";
					$sql .= " `ID` bigint(20) NOT NULL AUTO_INCREMENT, ";
					$sql .= " `hsid` varchar(255) NOT NULL COMMENT 'This column is used for table relation ship.', ";
					$sql .= " `ftid` varchar(100) NOT NULL COMMENT 'Featured store id.', ";
					$sql .= " `wpid` bigint(20) NOT NULL COMMENT 'The user who seen this store.', ";
					$sql .= " `date_created` datetime NULL DEFAULT current_timestamp() COMMENT 'Date created this store.', ";
					$sql .= "PRIMARY KEY (`ID`) ";
					$sql .= ") ENGINE = InnoDB; ";
				$result = $wpdb->get_results($sql);
			}

			//Database table creation for store cancelled
			if($wpdb->get_var( "SHOW TABLES LIKE '$tbl_store_cancelled_v2'" ) != $tbl_store_cancelled_v2) {
				$sql = "CREATE TABLE `".$tbl_store_cancelled_v2."` (";
					$sql .= " `ID` bigint(20) NOT NULL AUTO_INCREMENT, ";
					$sql .= " `hsid` varchar(255) NOT NULL COMMENT 'This column is used for table relation ship.', ";
					$sql .= " `stid` varchar(100) NOT NULL COMMENT 'Featured store id.', ";
					$sql .= " `status` enum('active', 'inactive') NOT NULL COMMENT 'Featured store id.', ";
					$sql .= " `comments` varchar(255)  COMMENT 'Featured store id.', ";
					$sql .= " `activated` enum('true', 'false') NOT NULL COMMENT 'Featured store id.', ";
					$sql .= " `executed_by` bigint(20) NOT NULL COMMENT 'The user who seen this store.', ";
					$sql .= " `date_created` datetime NULL DEFAULT current_timestamp() COMMENT 'Date created this store.', ";
					$sql .= "PRIMARY KEY (`ID`) ";
					$sql .= ") ENGINE = InnoDB; ";
				$result = $wpdb->get_results($sql);
			}

			//Database table creation for product categories
			if($wpdb->get_var( "SHOW TABLES LIKE '$tbl_product_category_v2'" ) != $tbl_product_category_v2) {
				$sql = "CREATE TABLE `".$tbl_product_category_v2."` (";
					$sql .= " `ID` bigint(20) NOT NULL AUTO_INCREMENT, ";
					$sql .= " `hsid` varchar(255) NOT NULL COMMENT 'This column is used for table relation ship.', ";
					$sql .= " `stid` varchar(100) NOT NULL COMMENT 'Store id.', ";
					$sql .= " `title` varchar(255) NOT NULL COMMENT 'Title of this category.', ";
					$sql .= " `info` varchar(255)  COMMENT 'Info of this category.', ";
					$sql .= " `status` enum('active', 'inactive') NOT NULL COMMENT 'Status of this cateogry.', ";
					$sql .= " `created_by` bigint(20) NOT NULL COMMENT 'The user who seen this store.', ";
					$sql .= " `date_created` datetime NULL DEFAULT current_timestamp() COMMENT 'Date created this store.', ";
					$sql .= "PRIMARY KEY (`ID`) ";
					$sql .= ") ENGINE = InnoDB; ";
				$result = $wpdb->get_results($sql);
			}

			//Database table creation for product
			if($wpdb->get_var( "SHOW TABLES LIKE '$tbl_product_varints_v2'" ) != $tbl_product_varints_v2) {
				$sql = "CREATE TABLE `".$tbl_product_varints_v2."` (";
					$sql .= " `ID` bigint(20) NOT NULL AUTO_INCREMENT, ";
					$sql .= " `hsid` varchar(255) NOT NULL COMMENT 'This column is used for table relation ship.', ";
					$sql .= " `pdid` varchar(100) NOT NULL COMMENT 'Product id.', ";
					$sql .= " `title` varchar(255) NOT NULL COMMENT 'Title of this variants.', ";
					$sql .= " `info` varchar(255) COMMENT 'Info of this variants.', ";
					$sql .= " `price` varchar(255) NOT NULL COMMENT 'Price of this variants.', ";
					$sql .= " `required` enum('false', 'true') NOT NULL COMMENT 'this variants required.', ";
					$sql .= " `parents` varchar(255) NOT NULL COMMENT 'Parent of this variant.', ";
					$sql .= " `status` enum('active', 'inactive') NOT NULL COMMENT 'Status of this variants.', ";
					$sql .= " `created_by` bigint(20) NOT NULL COMMENT 'The user who seen this store.', ";
					$sql .= " `date_created` datetime NULL DEFAULT current_timestamp() COMMENT 'Date created this variants.', ";
					$sql .= "PRIMARY KEY (`ID`) ";
					$sql .= ") ENGINE = InnoDB; ";
				$result = $wpdb->get_results($sql);
			}


			//Database table creation for product
			if($wpdb->get_var( "SHOW TABLES LIKE '$tbl_product_ratings_v2'" ) != $tbl_product_ratings_v2) {
				$sql = "CREATE TABLE `".$tbl_product_ratings_v2."` (";
					$sql .= " `ID` bigint(20) NOT NULL AUTO_INCREMENT, ";
					$sql .= " `hsid` varchar(255) NOT NULL COMMENT 'This column is used for table relation ship.', ";
					$sql .= " `pdid` varchar(100) NOT NULL COMMENT 'Product id.', ";
					$sql .= " `rates` tinyint(10) NOT NULL COMMENT 'Rates.', ";
					$sql .= " `comments` varchar(255) COMMENT 'Comments for this product rating', ";
					$sql .= " `rated_by` bigint(20) NOT NULL COMMENT 'The user who seen this store.', ";
					$sql .= " `status` enum('active', 'inactive') NOT NULL COMMENT 'Status of this featured product.', ";
					$sql .= " `date_created` datetime NULL DEFAULT current_timestamp() COMMENT 'Date created this variants.', ";
					$sql .= "PRIMARY KEY (`ID`) ";
					$sql .= ") ENGINE = InnoDB; ";
				$result = $wpdb->get_results($sql);
			}

			//Database table creation for product
			if($wpdb->get_var( "SHOW TABLES LIKE '$tbl_featured_product_v2'" ) != $tbl_featured_product_v2) {
				$sql = "CREATE TABLE `".$tbl_featured_product_v2."` (";
					$sql .= " `ID` bigint(20) NOT NULL AUTO_INCREMENT, ";
					$sql .= " `hsid` varchar(255) NOT NULL COMMENT 'This column is used for table relation ship.', ";
					$sql .= " `stid` varchar(150) NOT NULL COMMENT 'Store id.', ";
					$sql .= " `pdid` varchar(150) NOT NULL COMMENT 'Product ID.', ";
					$sql .= " `avatar` varchar(255) NOT NULL COMMENT 'Avatar of this featured product', ";
					$sql .= " `status` enum('active', 'inactive') NOT NULL COMMENT 'Status of this featured product.', ";
					$sql .= " `created_by` bigint(20) NOT NULL COMMENT 'The user who seen this store.', ";
					$sql .= " `date_created` datetime NULL DEFAULT current_timestamp() COMMENT 'Date created this variants.', ";
					$sql .= "PRIMARY KEY (`ID`) ";
					$sql .= ") ENGINE = InnoDB; ";
				$result = $wpdb->get_results($sql);
			}

			//Database table creation for product
			if($wpdb->get_var( "SHOW TABLES LIKE '$tbl_featured_product_seen_v2'" ) != $tbl_featured_product_seen_v2) {
				$sql = "CREATE TABLE `".$tbl_featured_product_seen_v2."` (";
					$sql .= " `ID` bigint(20) NOT NULL AUTO_INCREMENT, ";
					$sql .= " `hsid` varchar(255) NOT NULL COMMENT 'This column is used for table relation ship.', ";
					$sql .= " `pfid` varchar(150) NOT NULL COMMENT 'Featured product id.', ";
					$sql .= " `wpid` bigint(20) NOT NULL COMMENT 'The user who seen this product.', ";
					$sql .= " `date_created` datetime NULL DEFAULT current_timestamp() COMMENT 'Date created this variants.', ";
					$sql .= "PRIMARY KEY (`ID`) ";
					$sql .= ") ENGINE = InnoDB; ";
				$result = $wpdb->get_results($sql);
			}

			//Database table creation for store category
			if($wpdb->get_var( "SHOW TABLES LIKE '$tbl_store_category_groups_v2'" ) != $tbl_store_category_groups_v2) {
				$sql = "CREATE TABLE `".$tbl_store_category_groups_v2."` (";
					$sql .= " `ID` bigint(20) NOT NULL AUTO_INCREMENT, ";
					$sql .= " `hsid` varchar(255) NOT NULL COMMENT 'This column is used for table relation ship.', ";
					$sql .= " `title` varchar(150) NOT NULL COMMENT 'Store category group title.', ";
					$sql .= " `info` varchar(255)  COMMENT 'Store category group info.', ";
					$sql .= " `status` enum('active', 'inactive') NOT NULL COMMENT 'Status of this category groups.', ";
					$sql .= " `created_by` bigint(20) NOT NULL COMMENT 'The user who creates this category groups.', ";
					$sql .= " `date_created` datetime NULL DEFAULT current_timestamp() COMMENT 'Date created this variants.', ";
					$sql .= "PRIMARY KEY (`ID`) ";
					$sql .= ") ENGINE = InnoDB; ";
				$result = $wpdb->get_results($sql);
			}

			//Database table creation for store category
			if($wpdb->get_var( "SHOW TABLES LIKE '$tbl_product_v2'" ) != $tbl_product_v2) {
				$sql = "CREATE TABLE `".$tbl_product_v2."` (";
					$sql .= " `ID` bigint(20) NOT NULL AUTO_INCREMENT, ";
					$sql .= " `hsid` varchar(255) NOT NULL COMMENT 'This column is used for table relation ship.', ";
					$sql .= " `stid` varchar(100) NOT NULL COMMENT 'Store id.', ";
					$sql .= " `pcid` varchar(100) NOT NULL COMMENT 'Store id.', ";
					$sql .= " `title` varchar(255) NOT NULL COMMENT 'Title of this category.', ";
					$sql .= " `info` varchar(255)  COMMENT 'Info of this category.', ";
					$sql .= " `avatar` varchar(255) NOT NULL COMMENT 'Info of this category.', ";
					$sql .= " `banner` varchar(255) NOT NULL COMMENT 'Info of this category.', ";
					$sql .= " `price` varchar(255) NOT NULL COMMENT 'Info of this category.', ";
					$sql .= " `discount` varchar(255) NOT NULL COMMENT 'Info of this category.', ";
					$sql .= " `inventory` enum('false', 'true') NOT NULL COMMENT 'Status of this category groups.', ";
					$sql .= " `status` enum('active', 'inactive') NOT NULL COMMENT 'Status of this category groups.', ";
					$sql .= " `created_by` bigint(20) NOT NULL COMMENT 'The user who creates this category groups.', ";
					$sql .= " `date_created` datetime NULL DEFAULT current_timestamp() COMMENT 'Date created this variants.', ";
					$sql .= "PRIMARY KEY (`ID`) ";
					$sql .= ") ENGINE = InnoDB; ";
				$result = $wpdb->get_results($sql);
			}

		// END

		//Database table creation for wishlist
		if($wpdb->get_var( "SHOW TABLES LIKE '$tbl_wishlist'" ) != $tbl_wishlist) {
			$sql = "CREATE TABLE `".$tbl_wishlist."` (";
				$sql .= "`ID` bigint(20) NOT NULL AUTO_INCREMENT, ";
				$sql .= "`hsid` varchar(255) NOT NULL COMMENT 'Hash of id.', ";
				$sql .= "`product_id` bigint(20) DEFAULT NULL, ";
				$sql .= "`status` tinyint(2) NOT NULL, ";
				$sql .= "`created_by` bigint(20) NOT NULL, ";
				$sql .= " `date_created` datetime DEFAULT current_timestamp() COMMENT 'The date this role meta created.', ";
				$sql .= "PRIMARY KEY (`ID`) ";
				$sql .= ") ENGINE = InnoDB; ";
			$result = $wpdb->get_results($sql);
		}


		//Database table creation for store documents
		if($wpdb->get_var( "SHOW TABLES LIKE '$tbl_store_documents'" ) != $tbl_store_documents) {
			$sql = "CREATE TABLE `".$tbl_store_documents."` (";
				$sql .= " `ID` bigint(20) NOT NULL AUTO_INCREMENT, ";
				$sql .= " `hsid` varchar(255) NOT NULL COMMENT 'Hash of id.', ";
				$sql .= " `stid` varchar(150) DEFAULT NULL, ";
				$sql .= " `preview` varchar(255) NOT NULL, ";
				$sql .= " `types` varchar(150) NOT NULL, ";
				$sql .= " `status` enum('active', 'inactive') NOT NULL, ";
				$sql .= " `comments` varchar(255) , ";
				$sql .= " `executed_by` bigint(20) , ";
				$sql .= " `activated` enum('false', 'true') NOT NULL, ";
				$sql .= " `date_created` datetime DEFAULT current_timestamp() COMMENT 'The date this Store doducmnet.', ";
				$sql .= "PRIMARY KEY (`ID`) ";
				$sql .= ") ENGINE = InnoDB; ";
			$result = $wpdb->get_results($sql);
		}


		$wpdb->query("COMMIT");

    }
    add_action( 'activated_plugin', 'tp_dbhook_activate' );
