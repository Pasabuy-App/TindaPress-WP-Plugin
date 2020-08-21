
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
			<h1>VARIANT BROWSER</h1>
			<p>
                Add, Edit, Delete Variants.
			</p>
		</div>
	<?php /* Header Section */ ?>

	<div class="tp-panel-body">
		<div class="tp-panel-first">
			<?php if( isset($_GET['stid']) && isset($_GET['stname']) && isset($_GET['pdid']) && isset($_GET['pdname'])) { ?>
				<div class="alert alert-primary header-info">
					Variants: <strong id="<?= $_GET['pdid']; ?>"><?php echo $_GET['pdname']; ?></strong> from <strong id="<?= $_GET['stid']; ?>"><?php echo $_GET['stname']; ?></strong> Store
				</div>
			<?php } else { ?>
				<div class="alert alert-primary header-info">
					<strong>All Variants</strong>
				</div>
			<?php } ?>
			<?php if(isset($_GET['id']) && isset($_GET['name'])) { ?>
			<select class="space-left" id="set_cat" name="set_cat" required>
				<script type="text/javascript">
					jQuery(document).ready( function ( $ ) 
					{   
						$.ajax({
								dataType: 'json',
								type: 'POST', 
								data: {
									wpid: "<?php echo get_current_user_id(); ?>",
									snky: "<?php echo wp_get_session_token(); ?>",
									status: "1", //active.
									type: "2", //product.
									stid: "<?= (int)$_GET['id'] ?>"
								},
								url: '<?php echo site_url() . "/wp-json/tindapress/v1/category/list"; ?>',
								success: function(data) {
									console.log(data);
									var country = $('#set_cat');
										country.empty();
										var	selectDefault = 'All Category'; 
										country.append("<option value='0' selected='selected'>"+selectDefault+"</option>");

									if(data.status == "success") {
										for(var i=0; i<data.data.length; i++ ) {
											country.append('<option value=' + data.data[i].ID + '>' + data.data[i].title + '</option>');
										}
									} else {
										console.log("TindaPress: " + data);
									}
								},
								error : function(){
									console.log("TindaPress: Product Browser at Line 61.");
								}
						});
					});
				</script>                            
			</select>
			<?php } ?>
			<select class="space-left" id="set_status" name="set_status">
				<option value="0" selected="selected">All Status</option>
				<option value="1">Active</option>
				<option value="2">Inactive</option>
			</select>
			<button type="button" id="filter" name="filter" class="btn btn-primary space-left">Filter</button>
		</div>
		<table id="products-datatables" class="stripe" style="width: 100%;"></table>
		<div id="stores-notification" class="alert alert-info tp-center-item " role="alert" style="margin-top: 20px;">
			Currently fetching updates for all available variants. Please wait...
		</div>
	</div>

	<?php include_once( TP_PLUGIN_PATH . "/includes/model/variants.php" ); ?>
	<?php include_once( TP_PLUGIN_PATH . "/includes/view/modal/product-create-modal.php" ); ?>
	<?php include_once( TP_PLUGIN_PATH . "/includes/view/modal/product-edit-modal.php" ); ?>
	<div id="jquery-overlay" class="modal-backdrop fade show tp-display-hide" style="z-index: 9999;"></div>
	