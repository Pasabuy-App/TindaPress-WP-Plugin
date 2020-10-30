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


<div id="PartnerModal" class="modal fade"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="margin-top: 49px;">

            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title" style="text-align: center;">Add/Change Logo / Banner</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">

                </div>

                <div class="modal-footer">
                    <div id="LogoMessage" class="alert tp-fullwidth tp-center-item tp-display-hide" role="alert">
                        <p id="LogoContent">A simple success alert—check it out!</p>
                    </div>
                </div>

            </div>

        </div>
</div>
<!--
<div id="PartnerModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="vertical-alignment-helper">
        <div class="modal-dialog vertical-align-center" style="margin-top: 49px;">

            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title" style="text-align: center;">Modify Partner</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="ispartner-app-form">
                        <div class="form-group">
                            <label for="edit_store_name_partner">Store Name:</label>
                            <input disabled="disabled" type="text"  class="form-control" id="edit_store_name_partner" name="edit_store_name_partner">
                        </div>
                        <div class="form-group">
                            <label for="edit_partner">Partner:</label>
                            <select class="form-control tp-max-width" id="edit_partner" name="edit_partner" required>
                                <option value="" hidden selected>Select Partner Status</option>
                                <option value="true">True</option>
                                <option value="false">False</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <div class="alert alert-dark tp-center-item" role="alert">
                                <strong>NOTE:</strong> Before we submit your request a dialog confirmation will appear
                                to ask for your permission to complete the task.
                            </div>
                        </div>
                        <div class="tp-center-item">
                            <input id="ispartner_store_id" type="hidden" value="">
                            <button id="update-app-btn-ispartner" type="submit" class="btn btn-success"> - UPDATE - </button>
                        </div>
                        <div id="dialog-confirm-ispartner" title="Confirmation">
                            <p id="confirm-content-ispartner"></p>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <div id="IsPartnerMessage" class="alert tp-fullwidth tp-center-item tp-display-hide" role="alert">
                        <p id="IsPartnerContent">A simple success alert—check it out!</p>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div> -->