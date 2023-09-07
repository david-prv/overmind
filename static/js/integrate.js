/**
 * Script for integrating tools
 *
 * Version: 1.0.0
 * Author: David Dewes <hello@david-dewes.de>
 */

const submitButton = $('#submit');
const ghostSubmitButton = $('#ghost-submit');
const form = $('#integration');

function submitIntegration(event) {
    event.stopImmediatePropagation();
    event.stopPropagation();
    event.preventDefault();

    if (!_validateForm()) {
        alertError("Please fill in all fields!");
        return;
    }

    _submitForm();

    /*_submitFiles(function(html) {
        alertSuccess("Submitted files for investigation!");
        $(submitButton).html(html);
        setTimeout(_submitForm, 1000);
    });*/
}

function _validateForm() {
    let formIsValid = true;

    $(form).find( 'input[type!="hidden"],textarea' ).each(function () {
        if($(this).hasClass("is-valid")) $(this).removeClass("is-valid");
        if($(this).hasClass("is-invalid")) $(this).removeClass("is-invalid");

        if ( ! $(this).val() ) {
            $(this).addClass("is-invalid");
            formIsValid = false;
        } else {
            $(this).addClass("is-valid");
        }
    });

    return formIsValid;
}

function _submitForm() {
    $(ghostSubmitButton).click();
}

function _submitFiles(callback) {
    // TODO
    let html = $(submitButton).html();
    $(submitButton).html("<i class=\"fa fa-circle-o-notch fa-spin\"></i> Uploading for analysis...");
    setTimeout(function(x = html) { callback(x); }, 1500);
}