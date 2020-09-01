
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
            if(isset($_GET['stid']) && isset($_GET['stname'])) {
                $store_group = "&id=".$_GET['stid']."&name=".$_GET['stname'];
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

                        <?php if( isset($_GET['stid']) ) { ?>
                            postParam.stid = "<?= $_GET['stid'] ?>";
                        <?php } ?>
                        <?php if( isset($_GET['catid']) ) { ?>
                            postParam.catid = "<?= (int)$_GET['catid'] ?>";
                        <?php } ?>
                        <?php if( isset($_GET['status']) ) { ?>
                            postParam.status = "<?= (int)$_GET['status'] ?>";
                        <?php } ?>
                    var postUrl = "<?= TP_UIHOST . '/wp-json/tindapress/v1/products/list' ?>";
                    
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
                    { "sTitle": "NAME",   "mData": "product_name" },
                    { "sTitle": "DESCRIPTION",   "mData": "short_info" },
                    { "sTitle": "PRICE",   "mData": "price" },
                    { "sTitle": "VARIANTS",   "mData": "total" },
                    { "sTitle": "STATUS",   "mData": "status" },
                    <?php if(!isset($_GET['catid']) && !isset($_GET['catname'])) { ?>
                    { "sTitle": "CATEGORY",   "mData": "cat_name" },
                    { "sTitle": "STORE",   "mData": "store_name" },
                    <?php } else { ?>
                    {"sTitle": "Action", "mRender": function(data, type, item)
                        {
                            return '' + 

                                '<div class="btn-group" role="group" aria-label="Basic example">' +
                                    <?php if( isset($_GET['stid']) && isset($_GET['stname']) && isset($_GET['catid']) && isset($_GET['catname'])) { ?>
                                    '<button type="button" class="btn btn-primary btn-sm"' +
                                        ' data-toggle="modal" data-target="#EditAppOption"' +
                                        ' title="Click this to modified or delete this project."' +
                                        ' data-id="' + item.ID + '"' +  
                                        ' data-title="' + item.title + '"' +  
                                        ' data-sinfo="' + item.short_info + '"' + 
                                        ' data-price="' + item.price + '"' +  
                                        ' data-stid="' + <?= $_GET['stid'] ?> + '"' + 
                                        ' data-stname="' + '<?= $_GET['stname'] ?>' + '"' + 
                                        ' data-catid="' + <?= $_GET['catid'] ?> + '"' + 
                                        ' data-catname="' + '<?= $_GET['catname'] ?>' + '"' + 
                                        ' data-pdid="' + item.ID + '"' + 
                                        ' data-pdname="' + item.product_name + '"' + 
                                        ' >Modify</button>' +

                                        // '<button type="button" class="btn btn-secondary btn-sm appkey-' + item.ID + '"' +
                                        // ' data-clipboard-text="' + item.ID + '"' +
                                        // ' onclick="copyFromId(\'CategoryID-' + item.ID + '\')" ' +
                                        // ' title="Click this to copy the ID to your clipboard."' +
                                        // '>Copy ID</button>' +  

                                        '<button type="button" class="btn btn-success btn-sm"' +
                                        ' onclick="window.location.href = `<?php echo TP_Globals::wp_admin_url().TP_MENU_VARIANT; ?>' + 
                                        '&stid=' + '<?= $_GET['stid'] ?>' + '&stname=' + '<?= $_GET['stname'] ?>' + 
                                        '&catid=' + '<?= $_GET['catid'] ?>' + '&catname=' + '<?= $_GET['catname'] ?>' + 
                                        '&pdid=' + item.ID + '&pdname=' + item.product_name + 
                                        '`;" ' + ' title="Click this to navigate to variant list of this project."' + 
                                        ' >Variants</button>' +

                                        '<button type="button" class="btn btn-info btn-sm"' +
                                        ' data-toggle="modal" data-target="#AddProductLogo"' +
                                        ' title="Click this to add product logo."' +
                                        ' data-id="' + item.ID + '"' +  
                                        ' data-stid="' + item.stid+ '"' + 
                                        ' data-pdid="' + item.ID + '"' + 
                                        ' data-status="' + item.status + '"' + 
                                        ' data-logo="' + item.preview + '"' + 
                                        ' >Logo</button>' +


                                    <?php } ?>
              
                                '</div>'; 
                        }
                    }
                    <?php } ?>
                ];

                //Displaying data on datatables.
                tptables = $('#products-datatables').DataTable({
                    select: {
                        style: 'os',
                        blurable: true,
                        style: 'single'
                    },
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
                                    $store_group = "&stid=".$_GET['stid']."&stname=".$_GET['stname']."&type=2";
                                }
                                ?>
                                window.location.href = '<?php echo TP_Globals::wp_admin_url().TP_MENU_CATEGORY.$store_group; ?>';
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
                            text: 'View ID',
                            state: false,
                            init: function ( dt, node, config ) {
                                this.disable();
                            },
                            action: function ( e, dt, node, config ) {
                                var selData = tptables.row('.selected').data();
                                alert('ID: ' + selData.ID);
                            }
                        },
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
                tptables.on( 'select', function ( e, dt, type, indexes ) {
                        var rowData = tptables.rows( indexes ).data().toArray()[0];
                        // console.log("Selected: " + JSON.stringify( rowData.ID ));
                        tptables.button( 2 ).enable();
                });
                tptables.on( 'deselect', function ( e, dt, type, indexes ) {
                        tptables.button( 2 ).disable();
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
                    url: '<?php echo TP_UIHOST . "/wp-json/tindapress/v1/products/insert"; ?>',
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
                    postUrl = '<?php echo TP_UIHOST . "/wp-json/tindapress/v1/products/delete"; ?>';
                    postParam.pid = $('#edit_id').val();
                }

                else
                {
                    postUrl = '<?php echo TP_UIHOST . "/wp-json/tindapress/v1/products/update"; ?>';
                    postParam.catid = $('#edit_category').val();
                    postParam.stid = $('#edit_store').val();
                    postParam.pdid = $('#edit_id').val();
                    postParam.title = $('#edit_title').val();
                    postParam.short_info = $('#edit_info').val();
                    postParam.long_info = "None";
                    postParam.price = $('#edit_price').val();
                    postParam.sku = "None";
                    postParam.weight = "None";
                    postParam.dimension = "None";

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
                $('#edit_id').val( data.pdid );
                $('#edit_title').val( data.pdname );
                $('#edit_info').val( data.sinfo );
                $('#edit_price').val( data.price );
                // $('#edit_store').val( data.stname );
                // $('#edit_category').val( data.catname );

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

            $('#AddProductLogo').on('show.bs.modal', function(e) {
                    var data = e.relatedTarget.dataset;
                    $('#product_id').val( data.pdid );
                    $('#store_id').val( data.stid );
                    $('#product_status').val( data.status == 'Active' ? 1 : 0 );

                    if ( (typeof null === data.logo && !null) || data.logo === 'None' || data.logo === null || !data.logo ) {
                        $('#productImageResult').attr('src', 'https://mdbootstrap.com/img/Photos/Others/placeholder.jpg' );
                        
                    } else {
                        $('#productImageResult').attr('src', data.logo );

                    }
            });

            
            $('#AddProductLogo').on('hide.bs.modal', function(e) {
                    if( typeof activeTimeout !== 'undefined' )
                    {
                        clearTimeout( activeTimeout );
                    }

                    if( !$('#ProductLogoMessage').hasClass('tp-display-hide') )
                    {
                        $('#ProductLogoMessage').addClass('tp-display-hide');
                    }
            });

            $('#product-logo-app-form').submit( function(event) {
                event.preventDefault();
                $( "#dialog-confirm-product-logo" ).dialog({
                    title: 'Confirmation',
                    resizable: false,
                    height: "auto",
                    width: 320,
                    modal: false,
                    open: function() {
                        $('#jquery-overlay').removeClass('tp-display-hide');
                        $('#confirm-content-product-logo').html(
                            '<span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span>' +
                            'Please confirm to complete the process, else just press cancel.'
                        );
                    },
                    buttons: {
                        "Confirm": function() 
                        {
                            confirmProductLogoProcess();
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

            function confirmProductLogoProcess() {
                
                var postParam = new FormData();
                postParam.append( "img", $('#upload')[0].files[0]);
                postParam.append( "wpid", "<?php echo get_current_user_id(); ?>");
                postParam.append( "snky", "<?php echo wp_get_session_token(); ?>");
                postParam.append( "stid", $('#store_id').val());
                postParam.append( "pdid", $('#product_id').val());
                postParam.append( "status", $('#product_status').val());
                postParam.append( "type", 'logo');
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
                        $('#ProductLogoMessage').addClass('alert-'+status);
                        $('#ProductLogoMessage').removeClass('tp-display-hide');
                        $('#ProductLogoContent').html( data.message );
                        $('#product-logo-app-form').trigger("reset");
                    },
                    error : function(jqXHR, textStatus, errorThrown) 
                    {
                        console.log("" + JSON.stringify(jqXHR) + " :: " + textStatus + " :: " + errorThrown);
                    }
                });
            }
    });
</script>