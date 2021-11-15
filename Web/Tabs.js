/*Author : Camille RABIER based on https://stackoverflow.com/a/48081117*/
/*This script is used to have the tabs only the div been displayed or hidden according to the (sub)tablink clicked*/
function openTab(evt, openTab, subTab) {
    var i, tabcontent, tablinks;

    // Get all elements with tabcontent as class name
    tabcontent = document.getElementsByClassName("tabcontent");

    // Hide all tabcontents
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }

    //Get all elements with tablinks as class name
    tablinks = document.getElementsByClassName("tablinks");

    //Remove "active" class from all tablinks
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }

    //If the tab is nested in another one, the parent tab will still be shown
    if(subTab) {
        var parent = evt.currentTarget.closest('.tabcontent');
        parent.style.display = "block";
        parent.className += " active";
    }

    //Display the current tab and add "active" class to the button that opened the tab
    document.getElementById(openTab).style.display = "block";
    evt.currentTarget.className += " active";

}