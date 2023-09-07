/**
 * Script for creation of reference
 * report during integration process
 *
 * Version: 1.0.0
 * Author: David Dewes <hello@david-dewes.de>
 */

// ICONS
const SPINNER_ICON = "<div class=\"spinner-border spinner-border-sm\" role=\"status\">\n" +
    "                <span class=\"sr-only\">Loading...</span>\n" +
    "            </div";
const CHECK_ICON = "<i class=\"fa fa-check\"></i>";

// BUTTON CAPTIONS
const LOADING_CAPTION = "Running... Please wait!";
const DONE_CAPTION = "Save and Continue";

// TEXT AREA PLACEHOLDER
const TXT_PLACEHOLDER = "The tool is being executed, please wait...";

// PARAMS
const TEST_TARGET = "https://etage-4.de";

(function () {
    let btn_icon = $('#button-icon')[0];
    let btn_caption = $('#button-caption')[0];
    let ref_txt = $('#reference')[0];

    window.onbeforeunload = confirmUnloadReference;

    isBeingExecuted(btn_icon, btn_caption, ref_txt);
    executeTool(btn_icon, btn_caption, ref_txt);
})();

/* Show unload warning */
function confirmUnloadReference() {
    return "The integration process is not finished yet! Are you sure?";
}

/* Show "is being executed" UI */
function isBeingExecuted(btn_icon, btn_caption, ref_txt) {
    ref_txt.readOnly = "true";
    ref_txt.placeholder = TXT_PLACEHOLDER;

    btn_icon.innerHTML = SPINNER_ICON;
    btn_caption.innerText = LOADING_CAPTION;
}

/* Show "please replace by placeholders" UI */
function pleaseReplaceNow(btn_icon, btn_caption, ref_txt) {
    ref_txt.removeAttribute("readOnly");
    ref_txt.placeholder = "";

    btn_icon.innerHTML = CHECK_ICON;
    btn_caption.innerText = DONE_CAPTION;
}

/* Execution callback */
function __callback(id, btn_icon, btn_caption, ref_txt) {
    __fetchReport(id, ref_txt);
    pleaseReplaceNow(btn_icon, btn_caption, ref_txt);
}

/* Fetch report */
function __fetchReport(id, ref_txt) {
    $.get('/reports/report_' + id + '.txt').then(function(data, status, xhr, a = ref_txt) {
       __displayReport(data, a);
    });
}

/* Insert report to textarea */
function __displayReport(data, ref_txt) {
    let lines = data.split("\n");

    for (let i = 0; i < lines.length; i++) {
        ref_txt.innerHTML = ref_txt.value + lines[i] + "\n";
    }
}

/* Execute the integrated tool */
function executeTool(btn_icon, btn_caption, ref_txt) {
    // select last added tool from DATA
    let currentTool = DATA[DATA.length - 1];
    let query = "?run&engine=" + currentTool["engine"] + "&index=" + currentTool["index"] + "&args=\""
        + currentTool["args"].replace("%URL%", TEST_TARGET) + "\"&id=" + currentTool["id"] + "&target=" + TEST_TARGET;

    console.log(query);

    $.get("/index.php" + query, function (data, status, xhr, callback = __callback, id = currentTool["id"], a = btn_icon, b = btn_caption, c = ref_txt) {
        callback(id, a, b, c);
    });
}

/* Submits the reference report and redirects */
function submitRef() {
    // POST: index.php?reference, {"id": ID, "data": FORM_DATA}
    $.post("index.php?reference", {"id": CURRENT_ID, "reference": Base64.encode($("#reference").val())}, function (data) {
        console.log(data);
        window.location.href = "index.php";
    });
}