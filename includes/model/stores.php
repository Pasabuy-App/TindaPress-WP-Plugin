
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

<script type="text/javascript">
    jQuery(document).ready( function ( $ )
    {
        if($("#set_status").val() !== 0) {
            <?php if(isset($_GET['status'])) { ?>
                $("#set_status").val('<?php echo $_GET['status']; ?>');
            <?php } ?>
        }

        $("#filter").click(() => {
            <?php
            $store_group ="";
            if(isset($_GET['id']) && isset($_GET['name'])) {
                $store_group = "&id=".$_GET['id']."&name=".$_GET['name'];
            }
            ?>
            window.location.href = '<?php echo TP_Globals::wp_admin_url().TP_MENU_STORE.$store_group."&status="; ?>' + $('#set_status').val();
        });

        //THIS ARE ALL THE PUBLIC VARIABLES.
        var activeTimeout = 'undefined';

        //#region Page = APPLICATION LIST
            //GET THE REFERENCE OF THE CURRENT PAGE DATTABLES.
            var storeTables = $('#stores-datatables');

            //SET INTERVAL DRAW UPDATE.
            loadingAppList( storeTables );

            //LOAD APPLIST WITH AJAX.
            var tptables = 'undefined';
            function loadingAppList( storeTables )
            {
                if( storeTables.length != 0 )
                {
                    if( $('#stores-notification').hasClass('tp-display-hide') )
                    {
                        $('#stores-notification').removeClass('tp-display-hide');
                    }

                    var postParam = {
                            "wpid": "<?php echo get_current_user_id(); ?>",
                            "snky": "<?php echo wp_get_session_token(); ?>"
                        };

                        <?php if( isset($_GET['catid']) ) { ?>
                            postParam.catid = '<?= $_GET['catid'] ?>';
                        <?php } ?>

                        <?php if( isset($_GET['status']) ) { ?>
                            postParam.status = '<?= $_GET['status'] ?>';
                        <?php } ?>

                        var postUrl = '<?php echo TP_UIHOST . "/wp-json/tindapress/v1/stores/list"; ?>';

                    $.ajax({
                        dataType: 'json',
                        type: 'POST',
                        data: postParam,
                        url: postUrl,
                        success : function( data )
                        {

                            if(data.status == "success") {
                                displayingLoadedApps( data.data );
                            } else {
                                displayingLoadedApps( [] );
                            }


                            if(data.avatar != 'None') {
                                $('#imageResult').attr('src', data.avatar );
                            }

                            if(data.banner != 'None') {
                                $('#banner').attr('src', data.banner );
                            }

                            if( !$('#stores-notification').hasClass('tp-display-hide') )
                            {
                                $('#stores-notification').addClass('tp-display-hide');
                            }
                        },
                        error : function(jqXHR, textStatus, errorThrown)
                        {
                            console.log("" + JSON.stringify(jqXHR) + " :: " + textStatus + " :: " + errorThrown);
                        }
                    });
                }
            }

            //DISPLAY DATA INTO THE TARGET DATATABLES.
            function displayingLoadedApps( data )
            {

                //Set table column header.
                var columns = [
                    //{ "sTitle": "IDENTITY",   "mData": "ID" },
                    <?php
                        //if(!isset($_GET['id'])) {
                    ?>
                            //{ "sTitle": "CATEGORY",   "mData": "catname" },
                    <?php
                        //}
                    ?>
                    { "sTitle": "NAME",   "mData": "title" },
                    { "sTitle": "SHORT",   "mData": "short_info" },
                    { "sTitle": "COMMISSION",   "mData": "comm"},
                    { "sTitle": "STATUS",   "mData": "status" },
                    { "sTitle": "PHONE",   "mData": "phone" },
                    { "sTitle": "EMAIL",   "mData": "email" },
                    { "sTitle": "Street",   "mData": "street" },
                    { "sTitle": "Barangay",   "mData": "brgy" },
                    { "sTitle": "City",   "mData": "city" },
                    { "sTitle": "Province",   "mData": "province" },
                    { "sTitle": "Country",   "mData": "country" },
                    {"sTitle": "Action", "mRender": function(data, type, item)
                        {
                            console.log(item)
                            return '' +

                                '<div class="btn-group" role="group" aria-label="Basic example">' +

                                    '<button type="button" class="btn btn-primary btn-sm"' +
                                        ' data-toggle="modal" data-target="#EditAppOption"' +
                                        ' title="Click this to modified or delete this project."' +
                                        ' data-id="' + item.ID + '"' +
                                        ' data-status="' + item.status + '"' +
                                        ' data-title="' + item.title + '"' +
                                        ' data-sinfo="' + item.short_info + '"' +
                                        ' data-linfo="' + item.long_info + '"' +
                                        ' >Modify</button>' +

                                    '<button type="button" class="btn btn-secondary btn-sm appkey-' + item.ID + '"' +
                                        ' data-clipboard-text="' + item.ID + '"' +
                                        ' onclick="copyFromId(\'CategoryID-' + item.ID + '\')" ' +
                                        ' title="Click this to copy the ID to your clipboard."' +
                                        '>Copy ID</button>' +

                                    '<button type="button" class="btn btn-info btn-sm"' +
                                        ' onclick="window.location.href = `<?php echo TP_Globals::wp_admin_url().TP_MENU_CATEGORY; ?>' +
                                        '&stid=' + item.ID + '&stname=' + item.title +
                                        '&type=' + '2' + '`;" ' +
                                        ' title="Click this to navigate to variant list of this project."' +
                                        ' >Categories</button>' +

                                        '<button type="button" class="btn btn-success btn-sm"' +
                                        ' data-toggle="modal" data-target="#AddLogo"' +
                                        ' title="Click this to upload logo / banner of the store."' +
                                        ' data-id="' + item.ID + '"' +
                                        ' data-status="' + item.status + '"' +
                                        ' data-logo="' + item.avatar + '"' +
                                        ' data-banner="' + item.banner + '"' +
                                        ' >Logo / Banner</button>' +

                                        '<button type="button" class="btn btn-primary btn-sm"' +
                                        ' data-toggle="modal" data-target="#AddGPS"' +
                                        ' title="Click this to add GPS location of the store."' +
                                        ' data-id="' + item.ID + '"' +
                                        ' data-status="' + item.status + '"' +
                                        ' data-addr="' + item.add_id + '"' +
                                        ' data-lat="' + item.lat + '"' +
                                        ' data-long="' + item.long + '"' +
                                        ' >GPS</button>' +

                                        '<button type="button" class="btn btn-info btn-sm"' +
                                        ' data-toggle="modal" data-target="#CommissionModal"' +
                                        ' title="Click this to add commission of the store."' +
                                        ' data-id="' + item.ID + '"' +
                                        ' data-title="' + item.title + '"' +
                                        ' data-comm="' + item.comm + '"' +
                                        ' >Commission</button>' +

                                        '<button type="button" class="btn btn-warning btn-sm"' +
                                        ' data-toggle="modal" data-target="#PartnerModal"' +
                                        ' title="Click this to add Partner of the Pasabuy."' +
                                        ' data-id="' + item.ID + '"' +
                                        ' data-title="' + item.title + '"' +
                                        ' data-isPartner="' + item.partner + '"' +
                                        ' >Partner</button>' +




                                    // '<button type="button" class="btn btn-success btn-sm"' +
                                    //     ' onclick="window.location.href = `<?php //echo TP_Globals::wp_admin_url().TP_MENU_PRODUCT."&id="; ?>' + item.ID + '&name=' +item.title + '`;" ' +
                                    //     ' title="Click this to navigate to variant list of this project."' +
                                    //     ' >Products</button>' +


                                '</div>';
                        }
                    }
                ];

                //Displaying data on datatables.
                tptables = $('#stores-datatables').DataTable({
                    destroy: true,
                    searching: true,
                    dom: 'Bfrtip',
                    buttons: [
                        {
                            text: 'Create',
                            action: function ( e, dt, node, config ) {
                                //loadingAppList( storeTables );
                                $('#CreateNewApp').modal('show');
                            }
                        },
                        {
                            text: 'Refresh',
                            action: function ( e, dt, node, config ) {
                                loadingAppList( storeTables );
                            }
                        }, //'copy', 'csv', 'excel', 'pdf',
                        'print',
                    ],
                    responsive: true,
                    "aaData": data,
                    "aoColumns": columns,
                    "columnDefs": [
                        {"className": "dt-center", "targets": "_all"}
                    ],
                });
            }

            //IMPLEMENT DATATABLES RESPONSIVENESS.
            if(typeof tptables !== 'undefined' && typeof tptables.on === 'function')
            {
                tptables.on( 'responsive-resize', function ( e, datatable, columns ) {
                    var count = columns.reduce( function (a,b) {
                        return b === false ? a+1 : a;
                    }, 0 );

                    console.log( count +' column(s) are hidden' );
                } );
            }

            //CREATE NEW APP ENTRY ON MODAL.
            $('#create-app-form').submit( function(event) {
                event.preventDefault();

                $( "#dialog-confirm-create" ).dialog({
                    title: 'Confirmation',
                    resizable: false,
                    height: "auto",
                    width: 320,
                    modal: false,
                    open: function() {
                        $('#jquery-overlay').removeClass('tp-display-hide');
                        $('#confirm-content-create').html(
                            '<span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span>' +
                            'Please confirm to complete the process, else just press cancel.'
                        );
                    },
                    buttons: {
                        "Confirm": function()
                        {
                            var e = document.getElementById("new_category");
                            var storeCategory = e.options[e.selectedIndex].value;

                            var e = document.getElementById("new_country");
                            var countryValue = e.options[e.selectedIndex].value;

                            var e = document.getElementById("new_province");
                            var provinceValue = e.options[e.selectedIndex].value;

                            var e = document.getElementById("new_city");
                            var cityValue = e.options[e.selectedIndex].value;

                            var e = document.getElementById("new_brgy");
                            var brgyValue = e.options[e.selectedIndex].value;

                            if(storeCategory == 0 || countryValue == 0 || provinceValue == 0 || cityValue == 0 || brgyValue == 0 ) {
                                $('#CNAMessage').addClass('alert-failed');
                                $('#CNAMessage').removeClass('tp-display-hide');
                                alert( "Please select an item to all dropdown!" );

                                $('#create-app-btn').removeClass('disabled');
                                activeTimeout = setTimeout( function() {
                                    $('#CNAMessage').removeClass('alert-failed');
                                    $('#CNAMessage').addClass('tp-display-hide');
                                    activeTimeout = 'undefined';
                                }, 4000);
                            } else {
                                confirmCreateProcess();
                                $('#jquery-overlay').addClass('tp-display-hide');
                                $( this ).dialog( "close" );
                            }
                        },
                        Cancel: function()
                        {
                            $('#jquery-overlay').addClass('tp-display-hide');
                            $( this ).dialog( "close" );
                        }
                    }
                });
            });

            function confirmCreateProcess()
            {
                $('#create-app-btn').addClass('disabled');

                //From native form object to json object.
                var e = document.getElementById("new_category");
                var storeCategory = e.options[e.selectedIndex].value;

                var e = document.getElementById("new_country");
                var countryValue = e.options[e.selectedIndex].value;

                var e = document.getElementById("new_province");
                var provinceValue = e.options[e.selectedIndex].value;

                var e = document.getElementById("new_city");
                var cityValue = e.options[e.selectedIndex].value;

                var e = document.getElementById("new_brgy");
                var brgyValue = e.options[e.selectedIndex].value;

                var postParam = {};
                    postParam.wpid = "<?php echo get_current_user_id(); ?>";
                    postParam.snky = "<?php echo wp_get_session_token(); ?>";
                    postParam.catid = storeCategory;
                    postParam.title = $('#new_title').val();
                    postParam.short_info = $('#new_info').val();
                    postParam.long_info = "None";
                    postParam.logo = "None";
                    postParam.banner = "None";
                    postParam.phone = $('#new_phone').val();
                    postParam.email = $('#new_email').val();
                    postParam.st = $('#new_street').val();
                    postParam.bg = brgyValue;
                    postParam.ct = cityValue;
                    postParam.pv = provinceValue;
                    postParam.co = countryValue;

                // This will be handled by create-app.php.
                $.ajax({
                    dataType: 'json',
                    type: 'POST',
                    data: postParam,
                    url:  '<?php echo TP_UIHOST . "/wp-json/tindapress/v1/stores/insert"; ?>',
                    success : function( data )
                    {
                        console.log(data);
                        if( data.status == 'success' ) {
                            $('#new_title').val('');
                            $('#new_info').val('');
                            $('#new_phone').val('');
                            $('#new_email').val('');
                            $('#new_category').prop('selectedIndex',0);
                            $('#new_country').prop('selectedIndex',0);
                            $('#new_province').prop('selectedIndex',0);
                            $('#new_city').prop('selectedIndex',0);
                            $('#new_brgy').prop('selectedIndex',0);
                            $('#new_street').val('');
                        }

                        $('#CNAMessage').addClass('alert-'+data.status);
                        $('#CNAMessage').removeClass('tp-display-hide');
                        $('#CNAMcontent').text( data.message );

                        loadingAppList( storeTables );
                        $('#create-app-btn').removeClass('disabled');
                        activeTimeout = setTimeout( function() {
                            $('#CNAMessage').removeClass('alert-'+data.status);
                            $('#CNAMessage').addClass('tp-display-hide');
                            activeTimeout = 'undefined';
                        }, 4000);
                    },
                    error : function(jqXHR, textStatus, errorThrown) {
                        $('#CNAMessage').addClass('alert-danger');
                        $('#CNAMessage').removeClass('tp-display-hide');
                        $('#CNAMcontent').text( textStatus + ': Kindly consult to your administrator for this issue.' );

                        $('#create-app-btn').removeClass('disabled');
                        activeTimeout = setTimeout( function() {
                            $('#CNAMessage').removeClass('alert-danger');
                            $('#CNAMessage').addClass('tp-display-hide');
                            activeTimeout = 'undefined';
                        }, 7000);
                        console.log("" + JSON.stringify(jqXHR) + " :: " + textStatus + " :: " + errorThrown);
                    }
                });
            }

            // LISTEN FOR MODAL SHOW AND ATTACHED ID.
            $('#CreateNewApp').on('show.bs.modal', function(e) {
                $('#create-app-btn').removeClass('disabled');
                $('#new_title').val('');
                $('#new_info').val('');
                $('#new_phone').val('');
                $('#new_email').val('');
                $('#new_category').prop('selectedIndex',0);
                $('#new_country').prop('selectedIndex',0);
                $('#new_province').prop('selectedIndex',0);
                $('#new_city').prop('selectedIndex',0);
                $('#new_brgy').prop('selectedIndex',0);
                $('#new_street').val('');
            });

            // MAKE SURE THAT TIMEOUT IS CANCELLED.
            $('#CreateNewApp').on('hide.bs.modal', function(e) {
                if( typeof activeTimeout !== 'undefined' )
                {
                    clearTimeout( activeTimeout );
                }

                if( !$('#CNAMessage').hasClass('tp-display-hide') )
                {
                    $('#CNAMessage').addClass('tp-display-hide');
                }
            });

            //DELETE OR UPDATE FOCUSED APP ON MODAL.
            $('#edit-app-form').submit( function(event) {
                event.preventDefault();
                var clickedBtnId = $(this).find("button[type=submit]:focus").attr('id');
                $( "#dialog-confirm-edit" ).dialog({
                    title: 'Confirmation',
                    resizable: false,
                    height: "auto",
                    width: 320,
                    modal: false,
                    open: function() {
                        $('#jquery-overlay').removeClass('tp-display-hide');
                        $('#confirm-content-edit').html(
                            '<span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span>' +
                            'Please confirm to complete the process, else just press cancel.'
                        );
                    },
                    buttons: {
                    "Confirm": function()
                    {
                        confirmEditProcess( clickedBtnId );
                        $('#jquery-overlay').addClass('tp-display-hide');
                        $( this ).dialog( "close" );
                    },
                    Cancel: function()
                    {
                        $('#jquery-overlay').addClass('tp-display-hide');
                        $( this ).dialog( "close" );
                    }
                    }
                });

            });

            function confirmEditProcess( clickedBtnId )
            {
                $('#delete-app-btn').addClass('disabled');
                $('#update-app-btn').addClass('disabled');

                //From native form object to json object.
                var postParam = {};
                    postParam.wpid = "<?php echo get_current_user_id(); ?>";
                    postParam.snky = "<?php echo wp_get_session_token(); ?>";
                    postParam.stid = $('#edit_id').val();

                var postUrl = "";

                if( clickedBtnId == "delete-app-btn" ) {
                    if( $('#edit_status').val() == 0 ) {
                        postUrl = '<?= TP_UIHOST . "/wp-json/tindapress/v1/stores/delete"; ?>';
                    } else {
                        postUrl = '<?= TP_UIHOST . "/wp-json/tindapress/v1/stores/activate"; ?>';
                    }
                } else {
                    postUrl = '<?= TP_UIHOST . "/wp-json/tindapress/v1/stores/update"; ?>';
                    postParam.title = $('#edit_title').val();
                    postParam.short_info = $('#edit_info').val();
                    postParam.long_info = $('#edit_long_info').val();
                }

                // This will be handled by create-app.php.
                $.ajax({
                    dataType: 'json',
                    type: 'POST',
                    data: postParam,
                    url: postUrl,
                    success : function( data )
                    {

                        if( clickedBtnId == 'delete-app-btn' ) {
                            $('#new_title').val('');
                            $('#new_info').val('');
                            $('#new_phone').val('');
                            $('#new_email').val('');
                            $('#new_category').prop('selectedIndex',0);
                            $('#new_country').prop('selectedIndex',0);
                            $('#new_province').prop('selectedIndex',0);
                            $('#new_city').prop('selectedIndex',0);
                            $('#new_brgy').prop('selectedIndex',0);
                            $('#new_street').val('');
                        } else {
                            $('#delete-app-btn').removeClass('disabled');
                            $('#update-app-btn').removeClass('disabled');
                        }

                        $('#DFAMessage').addClass('alert-'+data.status);
                        $('#DFAMessage').removeClass('tp-display-hide');
                        $('#DFAMcontent').text( data.message );

                        loadingAppList( storeTables );
                        activeTimeout = setTimeout( function() {
                            $('#DFAMessage').removeClass('alert-'+data.status);
                            $('#DFAMessage').addClass('tp-display-hide');
                            if( clickedBtnId == 'delete-app-btn' ) {
                                $('#EditAppOption').modal('hide');
                            }
                            activeTimeout = 'undefined';
                        }, 4000);
                    },
                    error : function(jqXHR, textStatus, errorThrown) {
                        $('#DFAMessage').addClass('alert-danger');
                        $('#DFAMessage').removeClass('tp-display-hide');
                        $('#DFAMcontent').text( textStatus + ': Kindly consult to your administrator for this issue.' );

                        $('#delete-app-btn').removeClass('disabled');
                        $('#update-app-btn').removeClass('disabled');
                        activeTimeout = setTimeout( function() {
                            $('#DFAMessage').removeClass('alert-danger');
                            $('#DFAMessage').addClass('tp-display-hide');
                            activeTimeout = 'undefined';
                        }, 7000);
                        console.log("" + jqXHR + " :: " + textStatus + " :: " + errorThrown);
                    }
                });
            }

            // LISTEN FOR MODAL SHOW AND ATTACHED ID.
            $('#EditAppOption').on('show.bs.modal', function(e) {
                var data = e.relatedTarget.dataset;
                $('#edit_id').val( data.id );
                $('#edit_title').val( data.title );
                $('#edit_info').val( data.sinfo );
                $('#edit_long_info').val( data.linfo );
                $('#edit_status').val( data.status == 'Active' ? 1 : 0 );

                $('#delete-app-btn').removeClass('disabled');
                $('#update-app-btn').removeClass('disabled');
            });

            // MAKE SURE THAT TIMEOUT IS CANCELLED.
            $('#EditAppOption').on('hide.bs.modal', function(e) {
                if( typeof activeTimeout !== 'undefined' ) {
                    clearTimeout( activeTimeout );
                }

                if( !$('#DFAMessage').hasClass('tp-display-hide') ){
                    $('#DFAMessage').addClass('tp-display-hide');
                }
            });

        //#endregion

        // LISTEN FOR MODAL SHOW AND ATTACHED ID.
        $('#AddLogo').on('show.bs.modal', function(e) {
                var data = e.relatedTarget.dataset;
                // Show Logo
                if ( (typeof null === data.logo && !null) || data.logo === 'None' || data.logo === null || !data.logo ) {
                    $('#imageResult').attr('src', '<?php echo TP_PLUGIN_URL . "/assets/images/default-avatar.png"; ?>' );

                } else {
                    $('#imageResult').attr('src', data.logo );

                }

                // Show Banner
                if ( (typeof null === data.banner && !null) || data.banner === 'None' || data.banner === null || !data.banner ) {
                    $('#banner').attr('src', '<?php echo TP_PLUGIN_URL . "/assets/images/default-banner.png"; ?>' );

                } else {
                    $('#banner').attr('src', data.banner );

                }

                $('#logo_store_id').val( data.id );
                $('#edit_status').val( data.status == 'Active' ? 1 : 0 );
        });


        $('#AddLogo').on('hide.bs.modal', function(e) {
                if( typeof activeTimeout !== 'undefined' )
                {
                    clearTimeout( activeTimeout );
                }

                if( !$('#LogoMessage').hasClass('tp-display-hide') )
                {
                    $('#LogoMessage').addClass('tp-display-hide');
                }
        });


        $('#AddGPS').on('show.bs.modal', function(e) {
                var data = e.relatedTarget.dataset;
                $('#address_id').val( data.addr );
                $('#edit_status').val( data.status == 'Active' ? 1 : 0 );
                (data.lat === null) ? $('#lat').val('') : $('#lat').val(data.lat);
                (data.long === null) ? $('#long').val('') : $('#long').val(data.long);


        });

        $('#AddGPS').on('hide.bs.modal', function(e) {
                if( typeof activeTimeout !== 'undefined' )
                {
                    clearTimeout( activeTimeout );
                }

                if( !$('#GPSResponse').hasClass('tp-display-hide') )
                {
                    $('#GPSResponse').addClass('tp-display-hide');
                }
        });2

        $('#CommissionModal').on('show.bs.modal', function(e) {
                var data = e.relatedTarget.dataset;
                $('#comm_store_id').val( data.id );
                $('#edit_store_name').val( data.title );
                (data.comm === null) ? $('#edit_commission').val('') : $('#edit_commission').val(data.comm.replace('%', '') );

                $('#delete-app-btn').removeClass('disabled');
                $('#update-app-btn').removeClass('disabled');
        });

        // MAKE SURE THAT TIMEOUT IS CANCELLED.
        $('#CommissionModal').on('hide.bs.modal', function(e) {
            if( typeof activeTimeout !== 'undefined' ) {
                clearTimeout( activeTimeout );
            }

            $('#CommMessage').removeClass('alert-danger');
            $('#CommMessage').removeClass('alert-success');

            if( !$('#CommMessage').hasClass('tp-display-hide') ){
                $('#CommMessage').addClass('tp-display-hide');
            }
        });

        $('#commission-app-form').submit( function(event) {
            event.preventDefault();
                $( "#dialog-confirm-commission" ).dialog({
                    title: 'Confirmation',
                    resizable: false,
                    height: "auto",
                    width: 320,
                    modal: false,
                    open: function() {
                        $('#jquery-overlay').removeClass('tp-display-hide');
                        $('#confirm-content-commission').html(
                            '<span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span>' +
                            'Please confirm to complete the process, else just press cancel.'
                        );
                    },
                    buttons: {
                        "Confirm": function()
                        {
                            confirmCommissionProcess();
                            $('#jquery-overlay').addClass('tp-display-hide');
                            $( this ).dialog( "close" );
                        },
                        Cancel: function()
                        {
                            $('#jquery-overlay').addClass('tp-display-hide');
                            $( this ).dialog( "close" );
                        }
                    }
                });
        });



        $('#PartnerModal').on('hide.bs.modal', function(e) {
            if( typeof activeTimeout !== 'undefined' ) {
                clearTimeout( activeTimeout );
            }

            $('#IsPartnerMessage').removeClass('alert-danger');
            $('#IsPartnerMessage').removeClass('alert-success');

            if( !$('#IsPartnerMessage').hasClass('tp-display-hide') ){
                $('#IsPartnerMessage').addClass('tp-display-hide');
            }

        });

        // SUBMIT STORE ISPARTNER
        $('#ispartner-app-form').submit( function(event) {
            event.preventDefault();
                $( "#dialog-confirm-ispartner" ).dialog({
                    title: 'Confirmation',
                    resizable: false,
                    height: "auto",
                    width: 320,
                    modal: false,
                    open: function() {
                        $('#jquery-overlay').removeClass('tp-display-hide');
                        $('#confirm-content-ispartner').html(
                            '<span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span>' +
                            'Please confirm to complete the process, else just press cancel.'
                        );
                    },
                    buttons: {
                        "Confirm": function()
                        {
                            confirmPartnerProcess();
                            $('#jquery-overlay').addClass('tp-display-hide');
                            $( this ).dialog( "close" );
                        },
                        Cancel: function()
                        {
                            $('#jquery-overlay').addClass('tp-display-hide');
                            $( this ).dialog( "close" );
                        }
                    }
                });
        });
        // End

        $('#gps-app-form').submit( function(event) {
            event.preventDefault();
                $( "#dialog-confirm-gps" ).dialog({
                    title: 'Confirmation',
                    resizable: false,
                    height: "auto",
                    width: 320,
                    modal: false,
                    open: function() {
                        $('#jquery-overlay').removeClass('tp-display-hide');
                        $('#confirm-content-gps').html(
                            '<span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span>' +
                            'Please confirm to complete the process, else just press cancel.'
                        );
                    },
                    buttons: {
                        "Confirm": function()
                        {
                            confirmGpsProcess();
                            $('#jquery-overlay').addClass('tp-display-hide');
                            $( this ).dialog( "close" );
                        },
                        Cancel: function()
                        {
                            $('#jquery-overlay').addClass('tp-display-hide');
                            $( this ).dialog( "close" );
                        }
                    }
                });
        });

        $('#logo-app-form').submit( function(event) {
            event.preventDefault();
            $( "#dialog-confirm-logo" ).dialog({
                title: 'Confirmation',
                resizable: false,
                height: "auto",
                width: 320,
                modal: false,
                open: function() {
                    $('#jquery-overlay').removeClass('tp-display-hide');
                    $('#confirm-content-logo').html(
                        '<span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span>' +
                        'Please confirm to complete the process, else just press cancel.'
                    );
                },
                buttons: {
                    "Confirm": function()
                    {
                        confirmLogoProcess();
                        $('#jquery-overlay').addClass('tp-display-hide');
                        $( this ).dialog( "close" );
                    },
                    Cancel: function()
                    {
                        $('#jquery-overlay').addClass('tp-display-hide');
                        $( this ).dialog( "close" );
                    }
                }
            });
        });

        function confirmLogoProcess() {

            var postParam = new FormData();
            postParam.append( "img", $('#upload')[0].files[0]);
            postParam.append( "wpid", "<?php echo get_current_user_id(); ?>");
            postParam.append( "snky", "<?php echo wp_get_session_token(); ?>");
            postParam.append( "stid", $('#logo_store_id').val());
            postParam.append( "status", $('#edit_status').val());
            postParam.append( "type", $('#type').val());
            //postParam.append( "mkey", 'datavice');

            var postUrl = '<?= TP_UIHOST . "/wp-json/datavice/v1/process/upload"; ?>';
            $.ajax({
                dataType: 'json',
                type: 'POST',
                data: postParam,
                url: postUrl,
                processData : false,
                contentType: false,
                success : function( data )
                {
                    let status;
                    if (data.status == 'failed' || data.status == 'error' || data.status == 'unknown') {
                       status = 'danger';
                    } else {
                            status = data.status;
                    }
                    loadingAppList( storeTables );

                    $('#LogoMessage').addClass('alert-'+status);
                    $('#LogoMessage').removeClass('tp-display-hide');
                    $('#LogoContent').html( data.message );
                    $('#logo-app-form').trigger("reset");
                },
                error : function(jqXHR, textStatus, errorThrown)
                {
                    console.log("" + JSON.stringify(jqXHR) + " :: " + textStatus + " :: " + errorThrown);
                }
            });
        }

        function confirmGpsProcess() {
            var latitude = $('#lat').val();
            var longitude = $('#long').val();

            var postParam = {};
                    postParam.wpid = "<?php echo get_current_user_id(); ?>";
                    postParam.snky = "<?php echo wp_get_session_token(); ?>";
                    postParam.addr = $('#address_id').val();
                    postParam.status = $('#edit_status').val();
                    postParam.lat = latitude;
                    postParam.lon = longitude;

            var postUrl = '<?= TP_UIHOST . "/wp-json/datavice/v1/coordinates/insert"; ?>';

            $.ajax({
                dataType: 'json',
                type: 'POST',
                data: postParam,
                url: postUrl,
                success : function( data )
                {
                    $('#GPSResponse').addClass('alert-'+data.status);
                    $('#GPSResponse').removeClass('tp-display-hide');
                    $('#GPSContent').html( data.message );
                    //$('#gps-app-form').trigger("reset");
                    loadingAppList( storeTables );
                },
                error : function(jqXHR, textStatus, errorThrown)
                {
                    console.log("" + JSON.stringify(jqXHR) + " :: " + textStatus + " :: " + errorThrown);
                }
            });
        }


        function confirmCommissionProcess() {
            $('#CommMessage').removeClass('alert-danger');
            $('#CommMessage').removeClass('alert-success');
            var postParam = {};
                    postParam.wpid = "<?php echo get_current_user_id(); ?>";
                    postParam.snky = "<?php echo wp_get_session_token(); ?>";
                    postParam.stid = $('#comm_store_id').val();
                    postParam.comm = $('#edit_commission').val();
            var postUrl = '<?= TP_UIHOST . "/wp-json/tindapress/v1/stores/comm"; ?>';

            $.ajax({
                dataType: 'json',
                type: 'POST',
                data: postParam,
                url: postUrl,
                success : function( data )
                {
                    let status = '';
                    if (data.status == 'failed' || data.status == 'error' || data.status == 'unknown') {
                       status = 'danger';
                    } else {
                        status = data.status;
                    }
                    $('#CommMessage').addClass('alert-'+status);
                    $('#CommMessage').removeClass('tp-display-hide');
                    $('#CommContent').html( data.message );
                    loadingAppList( storeTables );

                },
                error : function(jqXHR, textStatus, errorThrown)
                {
                    console.log("" + JSON.stringify(jqXHR) + " :: " + textStatus + " :: " + errorThrown);
                }
            });
        }


        function confirmPartnerProcess() {
            $('#IsPartnerMessage').removeClass('alert-danger');
            $('#IsPartnerMessage').removeClass('alert-success');
            var postParam = {};
                    postParam.wpid = "<?php echo get_current_user_id(); ?>";
                    postParam.snky = "<?php echo wp_get_session_token(); ?>";
                    postParam.stid = $('#ispartner_store_id').val();
                    postParam.partner = $('#edit_partner').val();
            var postUrl = '<?= TP_UIHOST . "/wp-json/tindapress/v1/stores/partner"; ?>';

            $.ajax({
                dataType: 'json',
                type: 'POST',
                data: postParam,
                url: postUrl,
                success : function( data )
                {
                    let status = '';
                    if (data.status == 'failed' || data.status == 'error' || data.status == 'unknown') {
                       status = 'danger';
                    } else {
                        status = data.status;
                    }
                    $('#IsPartnerMessage').addClass('alert-'+status);
                    $('#IsPartnerMessage').removeClass('tp-display-hide');
                    $('#IsPartnerContent').html( data.message );
                    loadingAppList( storeTables );
                    document.getElementById("ispartner-app-form").reset();
                    //$('#IsPartnerMessage').addClass('tp-display-hide');

                },
                error : function(jqXHR, textStatus, errorThrown)
                {
                    console.log("" + JSON.stringify(jqXHR) + " :: " + textStatus + " :: " + errorThrown);
                }
            });
        }

    });
</script>