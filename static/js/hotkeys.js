/**
 * Script that handles hotkeys
 *
 * Version: 1.0.0
 * Author: David Dewes <hello@david-dewes.de>
 */

(function () {
    document.onkeydown = handle;
})();

// handles searchbar hotkey
function showSearchBar() {
    let el = $('#keyword-search-bar');
    if (!el) console.error("[ERROR] Could not find search-bar");
    else {
        if (!$(el).is(':visible')) $(el).slideDown();
        else $(el).slideUp();
    }
}

// detects the hotkeys and invokes handlers
function handle(evt) {
    if (!evt) evt = event;
    else if (evt.ctrlKey && evt.shiftKey && evt.keyCode == 83) { // Ctrl + Shift + S
        showSearchBar();
    } else if (evt.ctrlKey && evt.shiftKey && evt.keyCode == 90) { // Ctrl + Shift + Z
        deselectTools();
    } else if (evt.ctrlKey && evt.shiftKey && evt.keyCode == 65) { // Ctrl + Shift + A
        evt.preventDefault();
        selectAllTools();
    }
}