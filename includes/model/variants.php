
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
            $prod_group ="";
            if(isset($_GET['stid']) && isset($_GET['stname'])) {
                $store_group = "&id=".$_GET['stid']."&name=".$_GET['stname'];
            }
            if(isset($_GET['catid']) && isset($_GET['catname'])) {
                $cat_group = "&catid=".$_GET['catid']."&catname=".$_GET['catname'];
            }
            if(isset($_GET['pdid']) && isset($_GET['pdname'])) {
                $prod_group = "&pdid=".$_GET['pdid']."&pdname=".$_GET['pdname'];
            }
            if(isset($_GET['vrid']) && isset($_GET['vrname'])) {
                $prod_group = "&vrid=".$_GET['vrid']."&vrname=".$_GET['vrname'];
            }
            ?>
            var catid = isNaN($('#set_cat').val()) || $('#set_cat').val() == null ? 0 : $('#set_cat').val();
            window.location.href = '<?php echo TP_Globals::wp_admin_url().TP_MENU_PRODUCT.$store_group.$cat_group.$prod_group ."&status="; ?>' + $('#set_status').val() + "&catid=" + catid;
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

                        <?php if( isset($_GET['pdid']) ) { ?>
                            postParam.pdid = "<?= $_GET['pdid'] ?>";
                        <?php } ?>
                        <?php if( isset($_GET['vrid']) ) { ?>
                            postParam.vrid = "<?= $_GET['vrid'] ?>";
                        <?php } ?>
                       
                        postParam.status = $("#set_status").val();
                    var postUrl = "<?= site_url() . '/wp-json/tindapress/v1/variants/list' ?>";
                    
                    $.ajax({
                        dataType: 'json',
                        type: 'POST', 
                        data: postParam,
                        url: postUrl,
                        success : function( data )
                        {
                            console.log(data)
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
                    { "sTitle": "NAME",   "mData": "name" },
                    <?php if(isset($_GET['vrid']) && isset($_GET['vrname'])) { ?>
                        { "sTitle": "PRICE",   "mData": "price" },
                    <?php } else { ?>
                        { "sTitle": "BASE",   "mData": "base" },
                    <?php } ?>
                    //{ "sTitle": "INFO",   "mData": "info" },
                    { "sTitle": "STATUS",   "mData": "status" },
                    {"sTitle": "Action", "mRender": function(data, type, item)
                        {
                            return '' + 

                                    '<button type="button" class="btn btn-secondary btn-sm appkey-' + item.ID + '"' +
                                        ' data-clipboard-text="' + item.ID + '"' +
                                        ' onclick="copyFromId(\'CategoryID-' + item.ID + '\')" ' +
                                        ' title="Click this to copy the ID to your clipboard."' +
                                        '>Copy ID</button>' +  
                                        
                                    <?php if(!isset($_GET['vrid']) && !isset($_GET['vrname'])) { ?>
                                    '<button type="button" class="btn btn-success btn-sm"' +
                                        ' onclick="window.location.href = `<?php echo TP_Globals::wp_admin_url().TP_MENU_VARIANT; ?>' + 
                                        <?php if(isset($_GET['stid']) && isset($_GET['stname'])) { ?>
                                        '&stid=' + '<?= $_GET['stid'] ?>' + '&stname=' + '<?= $_GET['stname'] ?>' + 
                                        <?php } ?>
                                        <?php if(isset($_GET['catid']) && isset($_GET['catname'])) { ?>
                                        '&catid=' + '<?= $_GET['catid'] ?>' + '&catname=' + '<?= $_GET['catname'] ?>' + 
                                        <?php } ?>
                                        <?php if(isset($_GET['pdid']) && isset($_GET['pdname'])) { ?>
                                        '&pdid=' + '<?= $_GET['pdid'] ?>' + '&pdname=' + '<?= $_GET['pdname'] ?>' + 
                                        <?php } ?>
                                        '&vrid=' + item.ID + '&vrname=' + item.name + 
                                        '`;" ' + ' title="Click this to navigate to variant list of this project."' + 
                                        ' >Options</button>' +
                                        <?php } ?>
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
                        <?php if( isset($_GET['stid']) ) { ?>
                        {
                            text: 'Go Back',
                            action: function ( e, dt, node, config ) {
                                <?php
                                    $store_group ="";
                                    if(isset($_GET['stid']) && isset($_GET['stname'])) {
                                        $store_group = "&stid=".$_GET['stid']."&stname=".$_GET['stname'];
                                    }

                                    $cat_group ="";
                                    if(isset($_GET['catid']) && isset($_GET['catname'])) {
                                        $cat_group = "&catid=".$_GET['catid']."&catname=".$_GET['catname'];
                                    }
                                ?>
                                <?php if(isset($_GET['pdid']) && isset($_GET['pdname'])) { ?>
                                    window.location.href = '<?php echo TP_Globals::wp_admin_url().TP_MENU_PRODUCT.$store_group.$cat_group."&status=0"; ?>';
                                <?php } else { ?>
                                    window.location.href = '<?php echo TP_Globals::wp_admin_url().TP_MENU_VARIANT.$store_group.$cat_group."&status=0"; ?>';
                                <?php } ?>
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
                    postParam.name = $('#new_title').val();
                    postParam.info = $('#new_info').val();
                    postParam.pdid = $('#new_product').val();
                    <?php if( isset($_GET['vrid']) ) { ?>
                    postParam.pid = '<?= $_GET['vrid'] ?>';
                    
                    <?php } else  {?>
                        postParam.pid = 0; 
                    <?php } ?>
                    postParam.base = $('#new_base').val();
                    postParam.price = $('#new_price').val();

                // This will be handled by create-app.php.
                $.ajax({
                    dataType: 'json',
                    type: 'POST', 
                    data: postParam,
                    url: '<?php echo site_url() . "/wp-json/tindapress/v1/variants/insert"; ?>',
                    success : function( data )
                    {
                        if( data.status == 'success' ) {
                            $('#new_title').val('');
                            $('#new_info').val('');
                            $('#new_product').val('');
                            <?php if( isset($_GET['pdid']) && isset($_GET['pdname']) ) { ?>
                            $('#new_base').val('');
                            <?php } else { ?>
                            $('#new_price').val('');
                            <?php } ?>
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
                $('#new_title').val();
                $('#new_info').val();
                $('#new_product').prop('selectedIndex',0);
                $('#new_base').prop('selectedIndex',0);
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
                var clickedBtnId = $(this).find("button[type=submit]:focus").attr('stid');
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