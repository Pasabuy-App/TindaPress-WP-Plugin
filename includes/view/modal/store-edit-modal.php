
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
                    <h4 class="modal-title" style="text-align: center;">Modify Store</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <form id="edit-app-form">
                        <div class="form-group">
                            <label for="appname_edit">Name:</label>
                            <input required type="text" class="form-control" id="appname_edit" name="appname_edit" placeholder="Public name of this Project.">
                        </div>
                        <div class="form-group">
                            <label for="appdesc_edit">Description:</label>
                            <textarea required type="text" class="form-control" id="appdesc_edit" name="appdesc_edit" rows="3"
                                placeholder="Short description of your Project." ></textarea>
                        </div>
                        <div class="form-group">
                            <label for="appurl_edit">URL:</label>
                            <input required type="text" class="form-control" id="appurl_edit" name="appurl_edit" placeholder="http:/www.host.com/games/test">
                        </div>
                        <div class="form-group">
                            <label for="appmtcap_edit">USER / MATCH:</label>
                            <input required oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                type="number" maxlength="7" class="form-control" id="appmtcap_edit" name="appmtcap_edit" placeholder="10">
                        </div>	
                        <div class="form-group">
                            <label for="appcap_edit">MAX USER:</label>
                            <input required oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                type="number" maxlength="7" class="form-control" id="appcap_edit" name="appcap_edit" placeholder="1,000,000">
                        </div>	
                        <div class="form-group">
                            <label for="appsta_edit">STATUS:</label><br>
                            <select class="form-control" id="appsta_edit" name="appsta_edit">
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
                            <input id="appid_edit" type="hidden" value="">
                            <button id="delete-app-btn" type="submit" class="btn btn-danger"> - DELETE - </button>
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