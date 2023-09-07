/**
 * Script that handles toasts
 *
 * Version: 1.0.0
 * Author: David Dewes <hello@david-dewes.de>
 * See: https://codeseven.github.io/toastr/
 */

(function () {
    // prepare toasts
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": true,
        "progressBar": true,
        "positionClass": "toast-bottom-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };
})()

function alertInfo(message, title="Info") {
    toastr["info"](message, title);
}

function alertSuccess(message, title="Success") {
    toastr["success"](message, title);
}

function alertError(message, title="Error") {
    toastr["error"](message, title);
}

function alertWarning(message, title="Warning") {
    toastr["warning"](message, title);
}