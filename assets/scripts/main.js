// Copy value to clipboard by class of btn.
// btn should have data-clipboard-text="valuehere"
function copyFromId( elemId ) 
{
    var clipboard = new ClipboardJS('.'+elemId);
    console.log("Copied: " + elemId);
}