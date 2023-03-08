/**
 * Script for creation of reference
 * report during integration process
 *
 * Version: 1.0.0
 * Author: David Dewes <hello@david-dewes.de>
 */

const SPINNER_ICON = "<div class=\"spinner-border spinner-border-sm\" role=\"status\">\n" +
    "                <span class=\"sr-only\">Loading...</span>\n" +
    "            </div";
const CHECK_ICON = "<i class=\"fa fa-check\"></i>";

const LOADING_CAPTION = "Running... Please wait!";
const DONE_CAPTION = "Save and Continue";

const TXT_PLACEHOLDER = "The tool is being executed, please wait...";

(function() {
    let btn_icon = $('#button-icon')[0];
    let btn_caption = $('#button-caption')[0];
    let ref_txt = $('#reference')[0];

    ref_txt.readOnly = "true";
    ref_txt.placeholder = TXT_PLACEHOLDER;

    btn_icon.innerHTML = SPINNER_ICON;
    btn_caption.innerText = LOADING_CAPTION;
})();