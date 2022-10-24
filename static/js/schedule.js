/**
 * Collection of all necessary handlers/functions
 * to make the schedule page functional
 *
 * Version: 1.0.0
 * Author: David Dewes <hello@david-dewes.de>
 */

var list;

// code ignition
(function() {
    list = $('#interactions');
})();

// removes an interaction from list
function removeInteraction(pos) {

}

// returns all interaction list elements
function getInteractions() {
    let listOfInteractions = $('.interaction');
    console.log(listOfInteractions);
}

// creates a new list element
function createNew() {
    let li = document.createElement("li");
    li.classList.add("list-group-item", "d-flex", "justify-content-between", "align-items-center", "interaction");
    li.innerHTML = "";
}

// stores current list to json
function store() {

}