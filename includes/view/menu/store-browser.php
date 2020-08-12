
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
			<h1>STORE BROWSER</h1>
			<p>
                Add, Edit, Delete Stores.
			</p>
		</div>
	<?php /* Header Section */ ?>

	<div class="tp-panel-body">
		<div class="tp-panel-first">
			<?php if(isset($_GET['id']) && isset($_GET['id'])) { ?>
			<div class="alert alert-primary header-info">
				<strong>Category: </strong><strong id="<?= $_GET['id']; ?>"><?php echo $_GET['name']; ?></strong>
			</div>
			<?php } ?>
			<select class="space-left" id="set_status" name="set_status">
				<option value="0" selected="selected">All Status</option>
				<option value="1">Active</option>
				<option value="2">Inactive</option>
			</select>
			<button type="button" id="filter" name="filter" class="btn btn-primary space-left">Filter</button>
		</div>
		<table id="stores-datatables" class="stripe" style="width: 100%;"></table>
		<div id="stores-notification" class="alert alert-info tp-center-item " role="alert" style="margin-top: 20px;">
			Currently fetching updates for all available stores. Please wait...
		</div>
	</div>

	<?php include_once( TP_PLUGIN_PATH . "/includes/model/stores.php" ); ?>
	<?php include_once( TP_PLUGIN_PATH . "/includes/view/modal/store-create-modal.php" ); ?>
	<?php include_once( TP_PLUGIN_PATH . "/includes/view/modal/store-edit-modal.php" ); ?>
	<div id="jquery-overlay" class="modal-backdrop fade show tp-display-hide" style="z-index: 9999;"></div>
	