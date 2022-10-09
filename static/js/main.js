/**
 *  Bundle of all main functions and handlers
 *  this application needs during runtime
 */

(function() {
    // register launchAll button
    let launchAll = document.getElementById("launchAll");
    launchAll.addEventListener("click", invokeLaunchAll);
})();

// invokes all methods needed for the launchAll event
function invokeLaunchAll(event) {
    event.preventDefault();
    event.stopPropagation();

    $("#launchAll").html("<i class=\"fa fa-circle-o-notch fa-spin\"></i> Launching...");
}