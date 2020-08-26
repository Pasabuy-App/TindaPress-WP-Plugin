
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
                    <h4 class="modal-title" style="text-align: center;">Modify Product</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <form id="edit-app-form">
                        <div class="form-group">
                            <label for="edit_title">Name:</label>
                            <input required type="text" class="form-control" id="edit_title" name="edit_title" placeholder="Name of this Products.">
                        </div>
                        <div class="form-group">
                            <label for="edit_info">Description:</label>
                            <textarea required type="text" class="form-control" id="edit_info" name="edit_info" rows="3"
                                placeholder="Short description of your Products." ></textarea>
                        </div>
                        <div class="form-group">
                            <label for="edit_price">PRICE:</label>
                            <input required oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                type="number" maxlength="7" class="form-control" id="edit_price" name="edit_price" placeholder="1,000.00">
                        </div>
                        <div class="form-group">
                            <label for="edit_store">STORE:</label><br>
                            <select class="form-control tp-max-width" id="edit_store" name="edit_store">
                                <option selected="selected" value="<?php echo $_GET['stid']; ?>"><?php echo $_GET['stname']; ?></option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit_category">CATEGORY:</label><br>
                            <select class="form-control tp-max-width" id="edit_category" name="edit_category">
                                <option selected="selected" value="<?php echo $_GET['catid']; ?>"><?php echo $_GET['catname']; ?></option>
                            </select>
                        </div>
                        <div class="form-group">
                            <div class="alert alert-dark tp-center-item" role="alert">
                                <strong>NOTE:</strong> Before we submit your request a dialog confirmation will appear 
                                to ask for your permission to complete the task.
                            </div>
                        </div>
                        <div class="tp-center-item">
                            <input id="edit_id" type="hidden" value="">
                            <!-- <button id="delete-app-btn" type="submit" class="btn btn-danger"> - DELETE - </button> -->
                            <button id="update-app-btn" type="submit" class="btn btn-success"> - UPDATE - </button>
                        </div>
                        <div id="dialog-confirm-edit" title="Confirmation">
                            <p id="confirm-content-edit"></p>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <div id="DFAMessage" class="alert tp-fullwidth tp-center-item tp-display-hide" role="alert">
                        <p id="DFAMcontent">A simple success alert—check it out!</p>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>