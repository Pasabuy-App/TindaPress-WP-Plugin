
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

	<?php if( !isset($_GET['id']) && !isset($_GET['name']) ) { ?>

		<div class="tp-panel-body">
			<div class="tp-panel-first">
				<button id="RefreshAppList" type="button" class="btn btn-dark">Refresh List</button>
				<button type="button" class="btn btn-dark" data-toggle="modal" data-target="#CreateNewApp">Create Store</button>
			</div>
			<table id="stores-datatables" class="stripe" style="width: 100%;"></table>
			<div id="stores-notification" class="alert alert-info tp-center-item " role="alert" style="margin-top: 20px;">
				Currently fetching updates for all available stores. Please wait...
			</div>
		</div>
		<?php include_once( TP_PLUGIN_PATH . "/includes/model/stores.php" ); ?>

	<?php } else { ?>

		<div class="tp-panel-body">
			<div class="tp-panel-first">
				<div class="alert alert-secondary header-info">
					<strong>Category: </strong><strong id="parent-name"><?php echo $_GET['name']; ?></strong>
				</div>
				<button type="button" class="btn btn-dark" onclick="window.location.href = '<?php echo get_home_url()."/wp-admin/admin.php?tp=".$_GET['tp']; ?>';" >Go Back</button>
				<button id="RefreshAppList" type="button" class="btn btn-dark">Refresh List</button>
				<button type="button" class="btn btn-dark" data-toggle="modal" data-target="#CreateNewApp">Create Variant</button>
			</div>
			<table id="stores-datatables" class="stripe" style="width: 100%;"></table>
			<div id="stores-notification" class="alert alert-info tp-center-item " role="alert" style="margin-top: 20px;">
				Currently fetching updates for all available stores. Please wait...
			</div>
		</div>
		<?php //include_once( TP_PLUGIN_PATH . "/project-browser/variants.php" ); ?>
	<?php } ?>

	<?php include_once( TP_PLUGIN_PATH . "/includes/view/modal/store-create-modal.php" ); ?>
	<?php include_once( TP_PLUGIN_PATH . "/includes/view/modal/store-edit-modal.php" ); ?>
	<div id="jquery-overlay" class="modal-backdrop fade show tp-display-hide" style="z-index: 9999;"></div>
	