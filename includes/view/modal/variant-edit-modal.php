
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


<div id="EditAppOption" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="vertical-alignment-helper">
        <div class="modal-dialog vertical-align-center" style="margin-top: 49px;">

            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title" style="text-align: center;">Modify Variant</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <form id="update-app-form">
                        <div class="form-group">
                            <label for="new_title">Name:</label>
                            <input required type="text" value="<?php echo $_GET['name']; ?>" class="form-control" id="edit_title" name="edit_title" placeholder="Name of this Variants.">
                        </div>
                        <div class="form-group">
                            <label for="new_info">Description:</label>
                            <textarea required type="text" class="form-control" id="edit_info" name="edit_info" rows="3"
                                placeholder="Short description of your Variants." value="<?php echo $_GET['info']; ?>"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="new_product">Product:</label><br>
                            <select class="form-control tp-max-width" id="edit_product" name="edit_product">
                                <option selected="selected" value="<?php echo $_GET['pdid']; ?>"><?php echo $_GET['pdname']; ?></option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="new_base">Base:</label><br>
                            <select class="form-control tp-max-width" id="edit_base" name="edit_base">
                                <option selected="selected" value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                       
                       
                        <div class="form-group">
                            <div class="alert alert-dark tp-center-item" role="alert">
                                <strong>NOTE:</strong> Before we submit your request a dialog confirmation will appear 
                                to ask for your permission to complete the task.
                            </div>
                        </div>
                        <div class="tp-center-item">
                            <input  type="hidden" value="<?php echo $_GET['id']; ?>" class="form-control" id="edit_vrid" name="edit_vrid" placeholder="Name of this Variants.">
                            <button id="update-app-btn" type="submit" class="btn btn-success"> - UPDATE - </button>
                        </div>
                        <div id="dialog-confirm-variant" title="Confirmation">
                            <p id="confirm-content-variant"></p>
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