
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
                    <h4 class="modal-title" style="text-align: center;">Modify Category</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <form id="edit-app-form">
                        <div class="form-group">
                            <label for="edit_title">Name:</label>
                            <input required type="text" class="form-control" id="edit_title" name="edit_title" placeholder="Name of this Category.">
                        </div>
                        <div class="form-group">
                            <label for="edit_info">Description:</label>
                            <textarea required type="text" class="form-control" id="edit_info" name="edit_info" rows="3"
                                placeholder="Short description of your Category." ></textarea>
                        </div>
                        <div class="form-group">
                            <div class="alert alert-dark tp-center-item" role="alert">
                                <strong>NOTE:</strong> Before we submit your request a dialog confirmation will appear 
                                to ask for your permission to complete the task.
                            </div>
                        </div>
                        <div class="tp-center-item">
                            <input id="edit_id" type="hidden" value="">
                            <button id="delete-app-btn" type="submit" class="btn btn-danger"> - DEACTIVATE - </button>
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