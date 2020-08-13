
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
        <?php if(isset($_GET['status'])) { ?>
            $("#set_status").val('<?php echo (int)$_GET['status'] > 2 ? 0 : $_GET['status']; ?>');
        <?php } ?>

        <?php if(isset($_GET['type'])) { ?>
            $("#set_type").val('<?php echo (int)$_GET['type'] > 2 ? 0 : $_GET['type']; ?>');
        <?php } ?>

        $("#filter").click(() => {
            window.location.href = '<?php echo TP_Globals::wp_admin_url().TP_MENU_CATEGORY."&type="; ?>' + $('#set_type').val() + '&status=' + $('#set_status').val();
        }); 
        
        //THIS ARE ALL THE PUBLIC VARIABLES.
        var activeTimeout = 'undefined';

        //#region Page = APPLICATION LIST 
            //GET THE REFERENCE OF THE CURRENT PAGE DATTABLES.
            var categoryTables = $('#categories-datatables');

            //SET INTERVAL DRAW UPDATE.
            loadingAppList( categoryTables );
        
            //LOAD APPLIST WITH AJAX.
            var tptables = 'undefined';

            var activeItem = 'undefined';
            function loadingAppList( categoryTables )
            {
                if( categoryTables.length != 0 )
                {
                    if( $('#stores-notification').hasClass('tp-display-hide') )
                    {
                        $('#stores-notification').removeClass('tp-display-hide');
                    }

                    var postParam = {
                            "wpid": "<?php echo get_current_user_id(); ?>",
                            "snky": "<?php echo wp_get_session_token(); ?>"
                        };
                        postParam.status = $('#set_status').val();
                        postParam.type = $('#set_type').val();
                        postParam.stid = 0;
                        postParam.type = 0;
                        <?php if( isset($_GET['stid']) ) { ?>
                            postParam.stid = <?= (int)$_GET['stid'] ?>;
                        <?php } ?>
                    var postUrl = '<?php echo site_url() . "/wp-json/tindapress/v1/category/list"; ?>';
                    
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
                    { "sTitle": "ID",   "mData": "ID" },
                    { "sTitle": "NAME",   "mData": "title" },
                    { "sTitle": "DESCRIPTION",   "mData": "info" },
                    <?php if( !isset($_GET['stid']) ) { ?>
                        { "sTitle": "TYPES",   "mData": "types" },
                        { "sTitle": "STORES",   "mData": "total" },
                    <?php } else { ?> 
                        { "sTitle": "PRODUCTS",   "mData": "total" },
                    <?php } ?> 
                    { "sTitle": "STATUS",   "mData": "status" },
                    {"sTitle": "Action", "mRender": function(data, type, item)
                        {
                            return '' + 

                                '<div class="btn-group" role="group" aria-label="Basic example">' +

                                    <?php if( isset($_GET['stid']) && isset($_GET['stname']) ) { ?>
                                        '<button type="button" class="btn btn-success btn-sm"' +
                                        ' onclick="window.location.href = `<?php echo TP_Globals::wp_admin_url().TP_MENU_PRODUCT."&id="; ?>' + '<?= $_GET['stid'] ?>' + 
                                        '&name=' +'<?= $_GET['stname'] ?>'+ '&catid=' +item.ID+ '&catname=' +item.title+ '`;" ' +
                                        ' title="Click this to navigate to variant list of this project."' + 
                                        ' >PRODUCTS</button>' +

                                    <?php } else { ?>
                                        '<button type="button" class="btn btn-success btn-sm"' +
                                        ' onclick="window.location.href = `<?php echo TP_Globals::wp_admin_url().TP_MENU_STORE."&id="; ?>' + item.ID + '&name=' +item.title+ '`;" ' +
                                        ' title="Click this to navigate to variant list of this project."' + 
                                        ' >Stores</button>' +
                                    <?php } ?>                                    
                                        
                                '</div>'; 
                        }
                    }
                ];

                //Displaying data on datatables.
                tptables = $('#categories-datatables').DataTable({
                    select: {
                        style: 'os',
                        blurable: true,
                        style: 'single'
                    },
                    destroy: true,
                    searching: true,
                    dom: 'Bfrtip',
                    buttons: [
                        {
                            text: 'Create',
                            action: function ( e, dt, node, config ) {
                                $('#CreateNewApp').modal('show');
                            }
                        },
                        {
                            text: 'Edit',
                            state: false,
                            init: function ( dt, node, config ) {
                                this.disable();
                            },
                            action: function ( e, dt, node, config ) {
                                $('#EditAppOption').modal('show');
                            }
                        },
                        {
                            text: 'Refresh',
                            action: function ( e, dt, node, config ) {
                                loadingAppList( categoryTables );
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
                    "columnDefs": [
                        {
                            "targets": [ 0 ],
                            "visible": false,
                            "searchable": true
                        }
                    ],
                });
                tptables.on( 'select', function ( e, dt, type, indexes ) {
                        var rowData = tptables.rows( indexes ).data().toArray()[0];
                        activeItem = rowData; //console.log("Selected: " + JSON.stringify( rowData.ID ));
                        tptables.button( 1 ).enable();
                    });
                tptables.on( 'deselect', function ( e, dt, type, indexes ) {
                        activeItem = 'undefined';
                        tptables.button( 1 ).disable();
                    } );
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
                            confirmCreateProcess();
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

            function confirmCreateProcess()
            {
                $('#create-app-btn').addClass('disabled');

                //From native form object to json object.
                var postParam = {};
                    postParam.wpid = "<?php echo get_current_user_id(); ?>";
                    postParam.snky = "<?php echo wp_get_session_token(); ?>";
                    postParam.title = $('#new_title').val();
                    postParam.info = $('#new_info').val();
                    <?php if( isset($_GET['stid']) ) { ?>
                        postParam.stid = '<?= $_GET['stid'] ?>';
                        postParam.types = 'product';
                    <?php } ?>

                // This will be handled by create-app.php.
                $.ajax({
                    dataType: 'json',
                    type: 'POST', 
                    data: postParam,
                    url: '<?php echo site_url() . "/wp-json/tindapress/v1/category/insert"; ?>',
                    success : function( data )
                    {
                        if( data.status == 'success' ) {
                            $('#new_title').val('');
                            $('#new_info').val('');
                            $('#new_category').val('');
                        }

                        $('#CNAMessage').addClass('alert-'+data.status);
                        $('#CNAMessage').removeClass('tp-display-hide');
                        $('#CNAMcontent').text( data.message );

                        loadingAppList( categoryTables );
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
                // $('#appsta_create').val( 'Active' ); //TODO: Before appear modal, set input to empty.
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
                console.log($(this).find("button[type=submit]:focus").is('[disabled=disabled]'));
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
                    postParam.catid = $('#edit_id').val();
                var postUrl = '';
                
                if( clickedBtnId === 'delete-app-btn' ) {
                    postUrl = '<?= site_url() . "/wp-json/tindapress/v1/category/delete" ?>';
                } else {
                    postUrl = '<?= site_url() . "/wp-json/tindapress/v1/category/update" ?>';
                    postParam.title = $('#edit_title').val();
                    postParam.info = $('#edit_info').val();
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
                            $('#edit_title').val('');
                            $('#edit_info').val('');
                        } else {
                            $('#delete-app-btn').removeClass('disabled');
                            $('#update-app-btn').removeClass('disabled');
                        }
                        
                        $('#DFAMessage').addClass('alert-'+data.status);
                        $('#DFAMessage').removeClass('tp-display-hide');
                        $('#DFAMcontent').text( data.message );

                        loadingAppList( categoryTables );
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
                $('#edit_id').val( activeItem.id ); 
                $('#edit_title').val( activeItem.title ); 
                $('#edit_info').val( activeItem.info ); 

                if(activeItem.status == 'Inactive') {
                    $('#delete-app-btn').addClass('disabled');
                }   

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