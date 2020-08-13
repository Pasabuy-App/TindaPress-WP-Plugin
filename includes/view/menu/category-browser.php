
<?php
	// Exit if accessed directly
	if ( ! defined( 'ABSPATH' ) ) 
	{
		exit;
	}

	/**
	 * @package tindapress-wp-plugin
     * @version 0.1.0
    */
?>

	<?php /* Header Section */ ?>
		<div class="tp-welcome-header">
			<h1>CATEGORY BROWSER</h1>
			<p>
                Add, Edit, Delete Categories.
			</p>
		</div>
	<?php /* Header Section */ ?>

	<div class="tp-panel-body">
		<div class="tp-panel-first">
			<?php if(isset($_GET['type']) ) { ?>
				<?php if( $_GET['type'] == 'product' ) { ?>
					<div class="alert alert-primary header-info">
						<strong>Product Category</strong>
					</div>
				<?php } else { ?>
					<div class="alert alert-primary header-info">
						<strong>Store Category</strong>
					</div>
				<?php } ?>
			<?php } else { ?>
				<div class="alert alert-primary header-info">
					<strong>All Categories</strong>
				</div>
			<?php } ?>
			<select class="space-left" id="set_type" name="set_type">
				<option value="0" selected="selected">All Types</option>
				<option value="1">Store</option>
				<option value="2">Product</option>
			</select>
			<select class="space-left" id="set_status" name="set_status">
				<option value="0" selected="selected">All Status</option>
				<option value="1">Active</option>
				<option value="2">Inactive</option>
			</select>
			<button type="button" id="filter" name="filter" class="btn btn-primary space-left">Filter</button>
		</div>
		<table id="categories-datatables" class="stripe" style="width: 100%;"></table>
		<div id="stores-notification" class="alert alert-info tp-center-item " role="alert" style="margin-top: 20px;">
			Currently fetching updates for all available products. Please wait...
		</div>
	</div>
	
	<?php include_once( TP_PLUGIN_PATH . "/includes/model/categories.php" ); ?>
	<?php include_once( TP_PLUGIN_PATH . "/includes/view/modal/category-create-modal.php" ); ?>
	<?php include_once( TP_PLUGIN_PATH . "/includes/view/modal/category-edit-modal.php" ); ?>
	<div id="jquery-overlay" class="modal-backdrop fade show tp-display-hide" style="z-index: 9999;"></div>
	