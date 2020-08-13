
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
            $cat_group ="";
            if(isset($_GET['id']) && isset($_GET['name'])) {
                $store_group = "&id=".$_GET['id']."&name=".$_GET['name'];
            }
            if(isset($_GET['catid']) && isset($_GET['catname'])) {
                $cat_group = "&catid=".$_GET['catid']."&catname=".$_GET['catname'];
            }
            ?>
            var catid = isNaN($('#set_cat').val()) || $('#set_cat').val() == null ? 0 : $('#set_cat').val();
            window.location.href = '<?php echo TP_Globals::wp_admin_url().TP_MENU_PRODUCT.$store_group.$cat_group."&status="; ?>' + $('#set_status').val() + "&catid=" + catid;
        }); 

        //THIS ARE ALL THE PUBLIC VARIABLES.
        var activeTimeout = 'undefined';

        //#region Page = APPLICATION LIST 
            //GET THE REFERENCE OF THE CURRENT PAGE DATTABLES.
            var productTables = $('#products-datatables');

            //SET INTERVAL DRAW UPDATE.
            loadingAppList( productTables );
        
            //LOAD APPLIST WITH AJAX.
            var tptables = 'undefined';
            function loadingAppList( productTables )
            {
                if( productTables.length != 0 )
                {
                    if( $('#stores-notification').hasClass('tp-display-hide') )
                    {
                        $('#stores-notification').removeClass('tp-display-hide');
                    }

                    var postParam = {};
                        postParam.wpid = "<?php echo get_current_user_id(); ?>";
                        postParam.snky = "<?php echo wp_get_session_token(); ?>";
                        postParam.stid = 0;
                        postParam.catid = 0;
                        postParam.status = 0;

                        <?php if( isset($_GET['id']) ) { ?>
                            postParam.stid = "<?= $_GET['id'] ?>";
                        <?php } ?>
                        <?php if( isset($_GET['catid']) ) { ?>
                            postParam.catid = "<?= (int)$_GET['catid'] ?>";
                        <?php } ?>
                        <?php if( isset($_GET['status']) ) { ?>
                            postParam.status = "<?= (int)$_GET['status'] ?>";
                        <?php } ?>
                    var postUrl = "<?= site_url() . '/wp-json/tindapress/v1/products/listing' ?>";
                    
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
                    // { "sTitle": "IDENTITY",   "mData": "ID" },
                    { "sTitle": "STORE",   "mData": "store_name" },
                    { "sTitle": "CATEGORY",   "mData": "cat_name" },
                    { "sTitle": "NAME",   "mData": "product_name" },
                    { "sTitle": "DESCRIPTION",   "mData": "short_info" },
                    { "sTitle": "PRICE",   "mData": "price" },
                    { "sTitle": "STATUS",   "mData": "status" },
                    {"sTitle": "Action", "mRender": function(data, type, item)
                        {
                            return '' + 

                                '<div class="btn-group" role="group" aria-label="Basic example">' +
                                    <?php if( isset($_GET['id']) && isset($_GET['name']) && isset($_GET['catid']) && isset($_GET['catname'])) { ?>
                                    '<button type="button" class="btn btn-primary btn-sm"' +
                                        ' data-toggle="modal" data-target="#EditAppOption"' +
                                        ' title="Click this to modified or delete this project."' +
                                        ' data-id="' + item.ID + '"' +  
                                        ' data-title="' + item.title + '"' +  
                                        ' data-sinfo="' + item.short_info + '"' + 
                                        ' data-price="' + item.price + '"' +  
                                        ' data-stid="' + <?= $_GET['id'] ?> + '"' + 
                                        ' data-stname="' + '<?= $_GET['name'] ?>' + '"' + 
                                        ' data-catid="' + <?= $_GET['catid'] ?> + '"' + 
                                        ' data-catname="' + '<?= $_GET['catname'] ?>' + '"' + 
                                        ' >Options</button>' +
                                    <?php } ?>

                                    '<button type="button" class="btn btn-secondary btn-sm appkey-' + item.ID + '"' +
                                        ' data-clipboard-text="' + item.ID + '"' +
                                        ' onclick="copyFromId(\'CategoryID-' + item.ID + '\')" ' +
                                        ' title="Click this to copy the ID to your clipboard."' +
                                        '>Copy ID</button>' +  

                                    '<button type="button" class="btn btn-success btn-sm"' +
                                        ' onclick="window.location.href = `<?php echo get_home_url()."/logo.jpg"; ?>`;" ' +
                                        ' title="Click this to navigate to variant list of this project."' + 
                                        ' >Preview</button>' +
              
                                '</div>'; 
                        }
                    }
                ];

                //Displaying data on datatables.
                tptables = $('#products-datatables').DataTable({
                    destroy: true,
                    searching: true,
                    dom: 'Bfrtip',
                    buttons: [
                        <?php if( isset($_GET['id']) ) { ?>
                        {
                            text: 'Go Back',
                            action: function ( e, dt, node, config ) {
                                window.location.href = '<?php echo TP_Globals::wp_admin_url().TP_MENU_STORE; ?>' + '<?= "&id=".$_GET['catid'] ?>' + '<?= "&name=".$_GET['catname'] ?>';
                            }
                        },
                        {
                            text: 'Create',
                            action: function ( e, dt, node, config ) {
                                $('#CreateNewApp').modal('show');
                            }
                        },
                    <?php } ?>
                        {
                            text: 'Refresh',
                            action: function ( e, dt, node, config ) {
                                loadingAppList( productTables );
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

                var postParam = {};
                    postParam.wpid = "<?php echo get_current_user_id(); ?>";
                    postParam.snky = "<?php echo wp_get_session_token(); ?>";
                    postParam.catid = $('#new_category').val();
                    postParam.stid = $('#new_store').val();
                    postParam.title = $('#new_title').val();
                    postParam.short_info = $('#new_info').val();
                    postParam.long_info = "None";
                    postParam.price = $('#new_price').val();
                    postParam.sku = "None";
                    postParam.weight = "None";
                    postParam.dimension = "None";
                    postParam.preview = "None";

                // This will be handled by create-app.php.
                $.ajax({
                    dataType: 'json',
                    type: 'POST', 
                    data: postParam,
                    url: '<?php echo site_url() . "/wp-json/tindapress/v1/products/insert"; ?>',
                    success : function( data )
                    {
                        if( data.status == 'success' ) {
                            $('#new_category').val('');
                            $('#new_store').val('');
                            $('#new_title').val('');
                            $('#new_info').val('');
                            $('#new_price').val('');
                        }

                        $('#CNAMessage').addClass('alert-'+data.status);
                        $('#CNAMessage').removeClass('tp-display-hide');
                        $('#CNAMcontent').text( data.message );

                        loadingAppList( productTables );
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
                $('#new_category').val();
                $('#new_store').val();
                $('#new_title').val();
                $('#new_info').val();
                $('#new_price').val();
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

                var postUrl = '';

                //From native form object to json object.
                var postParam = {};
                    postParam.wpid = "<?php echo get_current_user_id(); ?>";
                    postParam.snky = "<?php echo wp_get_session_token(); ?>";

                if( clickedBtnId == 'delete-app-btn' )
                {
                    postUrl = '<?php echo site_url() . "/wp-json/tindapress/v1/products/delete"; ?>';
                    postParam.pid = $('#edit_id').val();
                }

                else
                {
                    postUrl = '<?php echo site_url() . "/wp-json/tindapress/v1/products/update"; ?>';
                    postParam.catid = $('#new_category').val();
                    postParam.stid = $('#new_store').val();
                    postParam.pdid = $('#new_store').val();
                    postParam.title = $('#new_title').val();
                    postParam.short_info = $('#new_info').val();
                    postParam.long_info = "None";
                    postParam.price = $('#new_price').val();
                    postParam.sku = "None";
                    postParam.weight = "None";
                    postParam.dimension = "None";
                    postParam.preview = "None";
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
                            $('#new_category').val('');
                            $('#new_store').val('');
                            $('#new_title').val('');
                            $('#new_info').val('');
                            $('#new_price').val('');
                        } else {
                            $('#delete-app-btn').removeClass('disabled');
                            $('#update-app-btn').removeClass('disabled');
                        }
                        
                        $('#DFAMessage').addClass('alert-'+data.status);
                        $('#DFAMessage').removeClass('tp-display-hide');
                        $('#DFAMcontent').text( data.message );

                        loadingAppList( productTables );
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
                $('#edit_id').val( data.aid );
                $('#edit_title').val( data.aid );
                $('#edit_info').val( data.aid );
                $('#edit_price').val( data.aid );
                $('#edit_store').val( data.aid );
                $('#edit_category').val( data.aid );

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