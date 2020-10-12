<?php
	// Exit if accessed directly
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	/**
	 * @package tindapress-wp-plugin
     * @version 0.1.0
     * Data for TindaPress access.
    */

    $tp_access_val =
    "( sha2(1, 256), 'add_product'),
    ( sha2(2, 256), 'edit_product'),
    ( sha2(3, 256), 'delete_product'),
    ( sha2(4, 256), 'add_role'),
    ( sha2(5, 256), 'edit_role'),
    ( sha2(6, 256), 'delete_role'),
    ( sha2(7, 256), 'add_personnel'),
    ( sha2(8, 256), 'edit_personnel'),
    ( sha2(9, 256), 'delete_personnel'),
    ( sha2(10, 256), 'add_category'),
    ( sha2(11, 256), 'edit_category'),
    ( sha2(12, 256), 'delete_category'),
    ( sha2(13, 256), 'edit_schedule'),
    ( sha2(14, 256), 'add_variants'),
    ( sha2(15, 256), 'edit_variants'),
    ( sha2(16, 256), 'delete_variants'),
    ( sha2(17, 256), 'add_message'),
    ( sha2(18, 256), 'listing_message'),
    ( sha2(19, 256), 'listing_operations'),
    ( sha2(20, 256), 'add_operations'),
    ( sha2(21, 256), 'accept_order'),
    ( sha2(22, 256), 'edit_setting') ;";
