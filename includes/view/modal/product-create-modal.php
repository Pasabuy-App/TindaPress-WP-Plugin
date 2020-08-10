
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
                    <h4 class="modal-title" style="text-align: center;">Create Product</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <form id="create-app-form">
                        <div class="form-group">
                            <label for="appname_create">Name:</label>
                            <input required type="text" class="form-control" id="appname_create" name="appname_create" placeholder="Public name of this Project.">
                        </div>
                        <div class="form-group">
                            <label for="appdesc_create">Description:</label>
                            <textarea required type="text" class="form-control" id="appdesc_create" name="appdesc_create" rows="3"
                                placeholder="Short description of your Project." ></textarea>
                        </div>
                        <div class="form-group">
                            <label for="appurl_create">URL:</label>
                            <input required type="text" class="form-control" id="appurl_create" name="appurl_create" placeholder="http:/www.host.com/games/test">
                        </div>
                        <div class="form-group">
                            <label for="appmtcap_create">USER / MATCH:</label>
                            <input required oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                type="number" maxlength="7" class="form-control" id="appmtcap_create" name="appmtcap_create" placeholder="10">
                        </div>
                        <div class="form-group">
                            <label for="appcap_create">MAX USER:</label>
                            <input required oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                type="number" maxlength="7" class="form-control" id="appcap_create" name="appcap_create" placeholder="1,000,000">
                        </div>
                        <div class="form-group">
                            <label for="appsta_create">STATUS:</label><br>
                            <select class="form-control" id="appsta_create" name="appsta_create">
                                <option selected="selected">Active</option>
                                <option>Inactive</option>
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