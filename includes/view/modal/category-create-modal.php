
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
                    <h4 class="modal-title" style="text-align: center;">Create Category</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <form id="create-app-form">
                        <div class="form-group">
                            <label for="new_title">Name:</label>
                            <input required type="text" class="form-control" id="new_title" name="new_title" placeholder="Name of this Category.">
                        </div>
                        <div class="form-group">
                            <label for="new_info">Description:</label>
                            <textarea required type="text" class="form-control" id="new_info" name="new_info" rows="3"
                                placeholder="Short description of your Category." ></textarea>
                        </div>
                        <div class="form-group">
                            <label for="new_types">TYPES:</label><br>
                            <select class="form-control" id="new_types" name="new_types">
                                <option selected="selected">store</option>
                                <option>product</option>
                            </select>
                        </div>
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