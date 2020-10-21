// Copy value to clipboard by class of btn.
// btn should have data-clipboard-text="valuehere"
function copyFromId( elemId )
{
    var clipboard = new ClipboardJS('.'+elemId);
    console.log("Copied: " + elemId);
}


//Get file and pass it to html img
function readURL(input) {

    var smp = jQuery('#type').val();

    switch (smp) {
        case 'none':
            alert("Please choose type of image in dropdown below.")
            break;

        case 'logo':
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    jQuery('#imageResult')
                        .attr('src', e.target.result);
                };
                reader.readAsDataURL(input.files[0]);
            }
            break;

        case 'banner':
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    jQuery('#banner')
                        .attr('src', e.target.result);
                };
                reader.readAsDataURL(input.files[0]);
            }
            break;
    }

}

function product_readURL(input) {

    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            jQuery('#productImageResult')
                .attr('src', e.target.result);
        };
        reader.readAsDataURL(input.files[0]);
    }
}


