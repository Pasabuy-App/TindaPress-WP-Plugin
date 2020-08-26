
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

<div id="CommissionModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="vertical-alignment-helper">
        <div class="modal-dialog vertical-align-center" style="margin-top: 49px;">

            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title" style="text-align: center;">Modify Commission</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="commission-app-form">
                        <div class="form-group">
                            <label for="edit_store_name">Store Name:</label>
                            <input disabled="disabled" type="text"  class="form-control" id="edit_store_name" name="edit_title">
                        </div>
                        <div class="form-group">
                            <label for="edit_commission">Commission Rate:</label>
                            <select class="form-control tp-max-width" id="edit_commission" name="edit_commission" required>
                                <option value="" hidden selected>Select Commission Rate</option>
                                <option value="17.5">17.5%</option>
                                <option value="20">20%</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <div class="alert alert-dark tp-center-item" role="alert">
                                <strong>NOTE:</strong> Before we submit your request a dialog confirmation will appear 
                                to ask for your permission to complete the task.
                            </div>
                        </div>
                        <div class="tp-center-item">
                            <input id="comm_store_id" type="hidden" value="">
                            <button id="update-app-btn" type="submit" class="btn btn-success"> - UPDATE - </button>
                        </div>
                        <div id="dialog-confirm-commission" title="Confirmation">
                            <p id="confirm-content-commission"></p>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <div id="CommMessage" class="alert tp-fullwidth tp-center-item tp-display-hide" role="alert">
                        <p id="CommContent">A simple success alert—check it out!</p>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>