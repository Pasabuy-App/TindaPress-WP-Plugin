// Copy value to clipboard by class of btn.
// btn should have data-clipboard-text="valuehere"
function copyFromId( elemId ) 
{
    var clipboard = new ClipboardJS('.'+elemId);
    console.log("Copied: " + elemId);
}


//Get file and pass it to html img
function readURL(input) {
    
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            jQuery('#imageResult')
                .attr('src', e.target.result);
        };
        reader.readAsDataURL(input.files[0]);
    }
}


