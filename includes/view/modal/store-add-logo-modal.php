
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


<div id="AddLogo" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="vertical-alignment-helper">
        <div class="modal-dialog vertical-align-center" style="margin-top: 49px;">

            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title" style="text-align: center;">Add/Change Logo</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <form id="logo-app-form">
                        <div class="form-group">
                            <div class="z-depth-1-half mb-4">
                                <img id=imageResult  src="<?php echo TP_PLUGIN_URL . "/assets/images/default.jpg"; ?>" class="img-fluid"
                                alt="example placeholder">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group mb-3 px-2 py-2 rounded-pill bg-white shadow-sm">
                                <input id="upload" required type="file" onchange="readURL(this);" class="form-control border-0">
                                <label id="upload-label" for="upload" class="font-weight-light text-muted">Select logo of this store</label>
                                <div class="input-group-append">
                                    <label for="upload" class="btn btn-light m-0 rounded-pill px-4"> <i class="fa fa-cloud-upload mr-2 text-muted"></i><small class="text-uppercase font-weight-bold text-muted">Choose file</small></label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="alert alert-dark tp-center-item" role="alert">
                                <strong>NOTE:</strong> Before we submit your request a dialog confirmation will appear 
                                to ask for your permission to complete the task.
                            </div>
                        </div>
                        <div class="tp-center-item">
                            <input id="logo_store_id" type="hidden" value="">
                            <button id="add-logo-app-btn" type="submit" class="btn btn-primary"> - Add Logo - </button>
                        </div>
                        <div id="dialog-confirm-logo" title="Confirmation">
                            <p id="confirm-content-logo"></p>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <div id="LogoMessage" class="alert tp-fullwidth tp-center-item tp-display-hide" role="alert">
                        <p id="LogoContent">A simple success alert—check it out!</p>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>