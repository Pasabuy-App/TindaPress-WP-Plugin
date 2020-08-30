
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

<div id="CreateNewApp" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="vertical-alignment-helper">
        <div class="modal-dialog vertical-align-center" style="margin-top: 49px;">

            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title" style="text-align: center;">Create Store</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <form id="create-app-form">
                        <div class="form-group">
                            <label for="new_title">Name:</label>
                            <input required type="text" class="form-control" id="new_title" name="new_title" placeholder="Name of this Store.">
                        </div>
                        <div class="form-group">
                            <label for="new_info">Description:</label>
                            <textarea type="text" class="form-control" id="new_info" name="new_info" rows="3"
                                placeholder="Short description of your Store." ></textarea>
                        </div>
                        <div class="form-group">
                            <label for="new_phone">Phone:</label>
                            <input required type="phone" class="form-control" id="new_phone" name="new_phone" placeholder="Phone of this Store.">
                        </div>
                        <div class="form-group">
                            <label for="new_email">Email:</label>
                            <input required type="email" class="form-control" id="new_email" name="new_email" placeholder="Email of this Store.">
                        </div>
                        <div class="form-group">
                            <label for="new_category">CATEGORY:</label><br>
                            <select class="form-control tp-max-width" id="new_category" name="new_category" required>
                                <?php if(!isset($_GET['stid'])) { ?>
                                <script type="text/javascript">
                                    jQuery(document).ready( function ( $ ) 
                                    {   
                                        $.ajax({
                                                dataType: 'json',
                                                type: 'POST', 
                                                data: {
                                                    wpid: "<?php echo get_current_user_id(); ?>",
                                                    snky: "<?php echo wp_get_session_token(); ?>"
                                                },
                                                url: '<?php echo site_url() . "/wp-json/tindapress/v1/category/list"; ?>',
                                                success: function(data) {
                                                    
                                                    if(data.status == "success") {
                                                        var $country = $('#new_category');
                                                        $country.empty();
                                                        $country.append("<option value='0' selected='selected'>Select Category</option>");
                                                        for(var i=0; i<data.data.length; i++ ) {
                                                            $country.append('<option value=' + data.data[i].ID + '>' + data.data[i].title + '</option>');
                                                        }
                                                    } else {
                                                        $('#CNAMessage').addClass('alert-'+data.status);
                                                        $('#CNAMessage').removeClass('tp-display-hide');
                                                        $('#CNAMcontent').text( data.message );
                                                    }

                                                },
                                                error : function(){
                                                    alert('Some error occurred!');
                                                }
                                        });
                                    });
                                </script>  
                                <?php } else { ?>
                                    <option selected="selected" value="<?php echo $_GET['stid']; ?>"><?php echo $_GET['stname']; ?></option>
                                <?php } ?>                              
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="new_country">COUNTRY:</label><br>
                            <select class="form-control tp-max-width" id="new_country" name="new_country" required>
                                <script type="text/javascript">
                                    jQuery(document).ready( function ( $ ) 
                                    {   
                                        $.ajax({
                                                dataType: 'json',
                                                type: 'POST', 
                                                data: {
                                                    mkey: "<?php echo DV_Library_Config::dv_get_config('master_key', 'datavice'); ?>"
                                                },
                                                url: '<?php echo site_url() . "/wp-json/datavice/v1/location/country/active"; ?>',
                                                success: function(data) {
                                                    if(data.status == "success") {
                                                        var $country = $('#new_country');
                                                        $country.empty();
                                                        $country.append("<option value='0' selected='selected'>Select Country</option>");
                                                        for(var i=0; i<data.data.length; i++ ) {
                                                            $country.append('<option value=' + data.data[i].code + '>' + data.data[i].name + '</option>');
                                                        }
                                                    } else {
                                                        $('#CNAMessage').addClass('alert-'+data.status);
                                                        $('#CNAMessage').removeClass('tp-display-hide');
                                                        $('#CNAMcontent').text( data.message );
                                                    }

                                                },
                                                error : function(){
                                                    alert('Some error occurred!');
                                                }
                                        });
                                    });
                                </script>
                                <!-- <option selected="selected">Restaurant</option> -->
                                
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="new_province">PROVINCE:</label><br>
                            <select class="form-control tp-max-width" id="new_province" name="new_province" required>
                                <script type="text/javascript">
                                    jQuery(document).ready( function ( $ ) 
                                    {  
                                        var $country = $('#new_province');
                                        $country.empty();
                                        $country.append("<option value='0' selected='selected'>Select Country First</option>");

                                        $(".form-group").on('click', '#new_country', function() {
                                            if($(this).val() != 0) {
                                                var e = document.getElementById("new_country");
                                                var countryCode = e.options[e.selectedIndex].value;
                                                $.ajax({
                                                    dataType: 'json',
                                                    type: 'POST', 
                                                    data: {
                                                        mkey: "<?php echo DV_Library_Config::dv_get_config('master_key', 'datavice'); ?>",
                                                        country_code: countryCode
                                                    },
                                                    url: '<?php echo site_url() . "/wp-json/datavice/v1/location/province/active"; ?>',
                                                    success: function(data) {
                                                        
                                                        if(data.status == "success") {
                                                            var $country = $('#new_province');
                                                            $country.empty();
                                                            $country.append("<option value='0' selected='selected'>Select Province</option>");
                                                            for(var i=0; i<data.data.length; i++ ) {
                                                                $country.append('<option value=' + data.data[i].code + '>' + data.data[i].name + '</option>');
                                                            }
                                                        } else {
                                                            $('#CNAMessage').addClass('alert-'+data.status);
                                                            $('#CNAMessage').removeClass('tp-display-hide');
                                                            $('#CNAMcontent').text( data.message );
                                                        }

                                                    },
                                                    error : function(){
                                                        alert('Some error occurred!');
                                                    }
                                                });
                                            } else {
                                                var $country = $('#new_province');
                                                $country.empty();
                                                $country.append("<option value='0' selected='selected'>Select Country First</option>");
                                            }
                                            
                                        });
                                    });
                                </script>                                
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="new_city">CITY:</label><br>
                            <select class="form-control tp-max-width" id="new_city" name="new_city" required>
                                <script type="text/javascript">
                                    jQuery(document).ready( function ( $ ) 
                                    {  
                                        var $city = $('#new_city');
                                        $city.empty();
                                        $city.append("<option value='0' selected='selected'>Select Province First</option>");

                                        $(".form-group").on('click', '#new_province', function() {
                                            if($(this).val() != 0) {
                                                var e = document.getElementById("new_province");
                                                var provinceCode = e.options[e.selectedIndex].value;
                                                $.ajax({
                                                    dataType: 'json',
                                                    type: 'POST', 
                                                    data: {
                                                        mkey: "<?php echo DV_Library_Config::dv_get_config('master_key', 'datavice'); ?>",
                                                        prov_code: provinceCode
                                                    },
                                                    url: '<?php echo site_url() . "/wp-json/datavice/v1/location/city/active"; ?>',
                                                    success: function(data) {
                                                        
                                                        if(data.status == "success") {
                                                            var $city = $('#new_city');
                                                            $city.empty();
                                                            $city.append("<option value='0' selected='selected'>Select City</option>");
                                                            for(var i=0; i<data.data.length; i++ ) {
                                                                $city.append('<option value=' + data.data[i].code + '>' + data.data[i].name + '</option>');
                                                            }
                                                        } else {
                                                            $('#CNAMessage').addClass('alert-'+data.status);
                                                            $('#CNAMessage').removeClass('tp-display-hide');
                                                            $('#CNAMcontent').text( data.message );
                                                        }

                                                    },
                                                    error : function(){
                                                        alert('Some error occurred!');
                                                    }
                                                });
                                            } else {
                                                var $city = $('#new_city');
                                                $city.empty();
                                                $city.append("<option value='0' selected='selected'>Select Province First</option>");
                                            }
                                            
                                        });
                                    });
                                </script>                                
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="new_brgy">Barangay:</label><br>
                            <select class="form-control tp-max-width" id="new_brgy" name="new_brgy" required>
                                <script type="text/javascript">
                                    jQuery(document).ready( function ( $ ) 
                                    {  
                                        var $city = $('#new_brgy');
                                        $city.empty();
                                        $city.append("<option value='0' selected='selected'>Select City First</option>");

                                        $(".form-group").on('click', '#new_city', function() {
                                            if($(this).val() != 0) {
                                                var e = document.getElementById("new_city");
                                                var cityCode = e.options[e.selectedIndex].value;
                                                $.ajax({
                                                    dataType: 'json',
                                                    type: 'POST', 
                                                    data: {
                                                        mkey: "<?php echo DV_Library_Config::dv_get_config('master_key', 'datavice'); ?>",
                                                        city_code: cityCode
                                                    },
                                                    url: '<?php echo site_url() . "/wp-json/datavice/v1/location/brgy/active"; ?>',
                                                    success: function(data) {
                                                        
                                                        if(data.status == "success") {
                                                            var $city = $('#new_brgy');
                                                            $city.empty();
                                                            $city.append("<option value='0' selected='selected'>Select Barangay</option>");
                                                            for(var i=0; i<data.data.length; i++ ) {
                                                                $city.append('<option value=' + data.data[i].code + '>' + data.data[i].name + '</option>');
                                                            }
                                                        } else {
                                                            $('#CNAMessage').addClass('alert-'+data.status);
                                                            $('#CNAMessage').removeClass('tp-display-hide');
                                                            $('#CNAMcontent').text( data.message );
                                                        }

                                                    },
                                                    error : function(){
                                                        alert('Some error occurred!');
                                                    }
                                                });
                                            } else {
                                                var $city = $('#new_brgy');
                                                $city.empty();
                                                $city.append("<option value='0' selected='selected'>Select City First</option>");
                                            }
                                            
                                        });
                                    });
                                </script>                                
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="new_street">Street Address:</label>
                            <textarea required type="text" class="form-control" id="new_street" name="new_street" rows="3"
                                placeholder="Short description of your Store." ></textarea>
                        </div>
                        <div class="form-group">
                            <div class="alert alert-dark tp-center-item" role="alert">
                                <strong>NOTE:</strong> Before we submit your request a dialog confirmation will appear 
                                to ask for your permission to complete the task.
                            </div>
                        </div>
                        <div class="tp-center-item">
                            <button id="create-app-btn" type="submit" class="btn btn-success"> - SUBMIT - </button>
                        </div>
                        <div id="dialog-confirm-create" title="Confirmation">
                            <p id="confirm-content-create"></p>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <div id="CNAMessage" class="alert tp-fullwidth tp-center-item tp-display-hide" role="alert">
                        <p id="CNAMcontent">A simple success alert—check it out!</p>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>