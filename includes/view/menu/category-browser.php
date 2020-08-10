
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

	<?php if( !isset($_GET['id']) && !isset($_GET['name']) ) { ?>

		<div class="tp-panel-body">
			<div class="tp-panel-first">
				<button id="RefreshAppList" type="button" class="btn btn-dark">Refresh List</button>
				<button type="button" class="btn btn-dark" data-toggle="modal" data-target="#CreateNewApp">Create Category</button>
			</div>
			<table id="categories-datatables" class="stripe" style="width: 100%;"></table>
			<div id="stores-notification" class="alert alert-info tp-center-item " role="alert" style="margin-top: 20px;">
				Currently fetching updates for all available products. Please wait...
			</div>
		</div>
		<?php include_once( TP_PLUGIN_PATH . "/includes/model/categories.php" ); ?>

	<?php } else { ?>

		<div class="tp-panel-body">
			<div class="tp-panel-first">
				<div class="alert alert-secondary header-info">
					<strong>Project: </strong><strong id="parent-name"><?php echo $_GET['name']; ?></strong>
				</div>
				<button type="button" class="btn btn-dark" onclick="window.location.href = '<?php echo get_home_url()."/wp-admin/admin.php?tp=".$_GET['tp']; ?>';" >Go Back</button>
				<button id="RefreshAppList" type="button" class="btn btn-dark">Refresh List</button>
				<button type="button" class="btn btn-dark" data-toggle="modal" data-target="#CreateNewApp">Create Variant</button>
			</div>
			<table id="categories-datatables" class="stripe" style="width: 100%;"></table>
			<div id="stores-notification" class="alert alert-info tp-center-item " role="alert" style="margin-top: 20px;">
				Currently fetching updates for all available products. Please wait...
			</div>
		</div>
		<?php //include_once( plugin_dir_path( __FILE__ ) . "/project-browser/variants.php" ); ?>
	<?php } ?>

	<?php include_once( TP_PLUGIN_PATH . "/includes/view/modal/category-create-modal.php" ); ?>
	<?php include_once( TP_PLUGIN_PATH . "/includes/view/modal/category-edit-modal.php" ); ?>
	<div id="jquery-overlay" class="modal-backdrop fade show tp-display-hide" style="z-index: 9999;"></div>
	