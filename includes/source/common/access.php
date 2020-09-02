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
    "( sha2(1, 256), 'can_add_product'), 
    ( sha2(2, 256), 'can_edit_product'), 
    ( sha2(3, 256), 'can_delete_product'),
    ( sha2(4, 256), 'can_add_role'), 
    ( sha2(5, 256), 'can_edit_role'), 
    ( sha2(6, 256), 'can_delete_role'), 
    ( sha2(7, 256), 'can_add_personnel'), 
    ( sha2(8, 256), 'can_edit_personnel'), 
    ( sha2(9, 256), 'can_delete_personnel'), 
    ( sha2(10, 256), 'can_add_category_product'), 
    ( sha2(11, 256), 'can_edit_category_product'), 
    ( sha2(12, 256), 'can_delete_category_duct'), 
    ( sha2(13, 256), 'can_add_supplier'), 
    ( sha2(14, 256), 'can_edit_supplier'), 
    ( sha2(15, 256), 'can_delete_supplier'),
    ( sha2(16, 256), 'can_receive_order'),
    ( sha2(17, 256), 'can_cancel_order'),
    ( sha2(18, 256), 'can_open_store'), 
    ( sha2(19, 256), 'can_close_store'), 
    ( sha2(20, 256), 'can_modify_store_info'), 
    ( sha2(21, 256), 'can_modify_store_address'), 
    ( sha2(22, 256), 'can_modify_store_contacts'), 
    ( sha2(23, 256), 'can_modify_store_documents ') ;";
