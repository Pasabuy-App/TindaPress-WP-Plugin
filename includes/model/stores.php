
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
        //THIS ARE ALL THE PUBLIC VARIABLES.
        var activeTimeout = 'undefined';

        //#region Page = APPLICATION LIST 
            //GET THE REFERENCE OF THE CURRENT PAGE DATTABLES.
            var storeTables = $('#stores-datatables');

            //SHOW NOTIFICATION THAT WE ARE CURRENTLY LOADING APPS.

            //SET INTERVAL DRAW UPDATE.
            loadingAppList( storeTables );
            // setInterval( function()
            // { 
            //     loadingAppList( storeTables );
            // }, 10000);

            $('#RefreshAppList').click(function() {
                loadingAppList( storeTables );
            });
        
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
                    <?php 
                        $root_url = site_url() . "/wp-json/tindapress/v1/stores/";
                        if(isset($_GET['id'])) {
                            $root_url .= "category/select";
                            ?>
                            postParam.catid = '<?php echo $_GET['id']; ?>';
                            <?php
                        } else {
                            $root_url .= "list/all";
                        }
                    ?>
                    
                    $.ajax({
                        dataType: 'json',
                        type: 'POST', 
                        data: postParam,
                        url: '<?php echo $root_url; ?>', //TODO: RESTAPI FOR STORE LIST
                        success : function( data )
                        {
                            if(data.status == "success") {
                                displayingLoadedApps( data.data );
                            } else {
                                displayingLoadedApps( [] );
                            }
                            
                            if( !$('#stores-notification').hasClass('tp-display-hide') )
                            {
                                $('#stores-notification').addClass('tp-display-hide');
                            }
                        },
                        error : function(jqXHR, textStatus, errorThrown) 
                        {
                            //$('#apps-notification').text = "";
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
                    { "sTitle": "NAME",   "mData": "title" },
                    { "sTitle": "SHORT",   "mData": "short_info" },
                    { "sTitle": "Street",   "mData": "street" },
                    { "sTitle": "Barangay",   "mData": "brgy" },
                    { "sTitle": "City",   "mData": "city" },
                    { "sTitle": "province",   "mData": "province" },
                    { "sTitle": "Country",   "mData": "country" },
                    //{ "sTitle": "CATEGORY",   "mData": "category" },
                    //{ "sTitle": "CONTACT",   "mData": "contacts" },
                    //{ "sTitle": "ADDRESS",   "mData": "addresses" },
                    {"sTitle": "Action", "mRender": function(data, type, item)
                        {
                            return '' + 

                                '<div class="btn-group" role="group" aria-label="Basic example">' +

                                    '<button type="button" class="btn btn-primary btn-sm"' +
                                        ' data-toggle="modal" data-target="#EditAppOption"' +
                                        ' title="Click this to modified or delete this project."' +
                                        ' data-id="' + item.ID + '"' +  
                                        ' data-title="' + item.title + '"' +  
                                        ' data-sinfo="' + item.short_info + '"' + 
                                        ' >Options</button>' +

                                    '<button type="button" class="btn btn-secondary btn-sm appkey-' + item.ID + '"' +
                                        ' data-clipboard-text="' + item.ID + '"' +
                                        ' onclick="copyFromId(\'CategoryID-' + item.ID + '\')" ' +
                                        ' title="Click this to copy the ID to your clipboard."' +
                                        '>Copy ID</button>' +  

                                    '<button type="button" class="btn btn-success btn-sm"' +
                                        ' onclick="window.location.href = `<?php echo get_home_url()."/wp-admin/admin.php?page="."tp-product_browser"."&id="; ?>' + item.ID + '&name=' +item.title+ '`;" ' +
                                        ' title="Click this to navigate to variant list of this project."' + 
                                        ' >Products</button>' +

                                             
                                        
                                '</div>'; 
                        }
                    }
                ];

                //Displaying data on datatables.
                tptables = $('#stores-datatables').DataTable({
                    destroy: true,
                    searching: true,
                    buttons: ['copy', 'excel', 'print'],
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
                            
                            if(storeCategory == 0) {
                                $('#CNAMessage').addClass('alert-failed');
                                $('#CNAMessage').removeClass('tp-display-hide');
                                alert( "Please select category!" );

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
                    url:  '<?php echo site_url() . "/wp-json/tindapress/v1/stores/insert"; ?>',
                    success : function( data )
                    {
                        console.log(data);

                        if( data.status == 'success' ) {
                            $('#new_title').val('');
                            $('#new_info').val('');
                            $('#new_category').prop('selectedIndex',0);
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
                var data = e.relatedTarget.dataset;
                $('#create-app-btn').removeClass('disabled');
                // $('#appsta_create').val( 'Active' ); //TODO: Before appear modal, set input to empty.
                // $('#appmtcap_create').val(); //TODO: Before appear modal, set input to empty.
                // $('#appcap_create').val(); //TODO: Before appear modal, set input to empty.
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

                if( clickedBtnId == 'delete-app-btn' )
                {
                    // TODO: Contact Delete RestAPI
                    // postParam.action = 'DeleteThisApp';
                    // postParam.appid_edit = $('#appid_edit').val();
                }

                else
                {
                    // TODO: Contact Update RestAPI
                    // postParam.action = 'UpdateThisApp';
                    // postParam.appid_edit = $('#appid_edit').val();
                    // postParam.appsta_edit = $('#appsta_edit').val();
                    // postParam.appname_edit = $('#appname_edit').val();
                    // postParam.appdesc_edit = $('#appdesc_edit').val();
                    // postParam.appurl_edit = $('#appurl_edit').val();
                    // postParam.appmtcap_edit = $('#appmtcap_edit').val();
                    // postParam.appcap_edit = $('#appcap_edit').val();
                }

                // This will be handled by create-app.php.
                $.ajax({
                    dataType: 'json',
                    type: 'POST', 
                    data: postParam,
                    url: 'admin-ajax.php',
                    success : function( data )
                    {
                        if( clickedBtnId == 'delete-app-btn' ) {
                            // $('#appname_edit').val(''); //TODO: Set item input to empty.
                            // $('#appdesc_edit').val(''); //TODO: Set item input to empty.
                            // $('#appurl_edit').val(''); //TODO: Set item input to empty.
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
                // $('#appid_edit').val( data.aid ); //TODO: Set item display from data.
                // $('#appname_edit').val( data.aname ); //TODO: Set item display from data.
                // $('#appdesc_edit').val( data.ainfo ); //TODO: Set item display from data.

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
    });
</script>