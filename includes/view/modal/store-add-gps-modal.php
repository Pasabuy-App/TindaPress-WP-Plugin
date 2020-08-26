
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


<div id="AddGPS" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="vertical-alignment-helper">
        <div class="modal-dialog vertical-align-center" style="margin-top: 49px;">

            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title" style="text-align: center;">Add/Change GPS</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <form id="gps-app-form">
                        
                        <div class="form-group">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-default">Latitude&nbsp&nbsp&nbsp</span>
                                </div>
                                <input required type="number" step = "any"  class="form-control" id="lat" aria-label="Default" aria-describedby="inputGroup-sizing-default">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-default">Longitude</span>
                                </div>
                                <input required type="number" step = "any"  class="form-control" id="long" aria-label="Default" aria-describedby="inputGroup-sizing-default">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="alert alert-dark tp-center-item" role="alert">
                                <strong>NOTE:</strong> Before we submit your request a dialog confirmation will appear 
                                to ask for your permission to complete the task.
                            </div>
                        </div>
                        <div class="tp-center-item">
                            <input id="address_id" type="hidden" value="">
                            <button id="add-gps-app-btn" type="submit" class="btn btn-primary"> - Add GPS - </button>
                        </div>
                        <div id="dialog-confirm-gps" title="Confirmation">
                            <p id="confirm-content-gps"></p>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <div id="GPSResponse" class="alert tp-fullwidth tp-center-item tp-display-hide" role="alert">
                        <p id="GPSContent">A simple success alert—check it out!</p>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>