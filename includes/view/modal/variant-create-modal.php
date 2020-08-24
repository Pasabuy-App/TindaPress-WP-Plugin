
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

<div id="CreateNewApp" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="vertical-alignment-helper">
        <div class="modal-dialog vertical-align-center" style="margin-top: 49px;">

            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title" style="text-align: center;">Create Variant</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <form id="create-app-form">
                        <div class="form-group">
                            <label for="new_title">Name:</label>
                            <input required type="text" class="form-control" id="new_title" name="new_title" placeholder="Name of this Variants.">
                        </div>
                        <div class="form-group">
                            <label for="new_info">Description:</label>
                            <textarea required type="text" class="form-control" id="new_info" name="new_info" rows="3"
                                placeholder="Short description of your Variants." ></textarea>
                        </div>
                        <div class="form-group">
                            <label for="new_product">Product:</label><br>
                            <select class="form-control" id="new_product" name="new_product">
                                <option selected="selected" value="<?php echo $_GET['pdid']; ?>"><?php echo $_GET['pdname']; ?></option>
                            </select>
                        </div>
                        <?php if( !isset($_GET['vrid']) && !isset($_GET['vrname']) ) { ?>
                        <div class="form-group">
                            <label for="new_base">Base:</label><br>
                            <select class="form-control" id="new_base" name="new_base">
                                <option selected="selected" value="0">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                        <?php } else { ?>
                        <div class="form-group">
                            <label for="new_price">PRICE:</label>
                            <input required oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                type="number" maxlength="7" class="form-control" id="new_price" name="new_price" placeholder="1,000.00">
                        </div>
                        <?php } ?>
                        <div class="form-group">
                            <div class="alert alert-dark tp-center-item" role="alert">
                                <strong>NOTE:</strong> Before we submit your request a dialog confirmation will appear 
                                to ask for your permission to complete the task.
                            </div>
                        </div>
                        <div class="tp-center-item">
                            <button id="create-app-btn" type="submit" class="btn btn-success"> - SUBMIT - </button>
                        </div>
                        <div id="dialog-confirm-create" title="Confirmation">
                            <p id="confirm-content-create"></p>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <div id="CNAMessage" class="alert tp-fullwidth tp-center-item tp-display-hide" role="alert">
                        <p id="CNAMcontent">A simple success alert—check it out!</p>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>