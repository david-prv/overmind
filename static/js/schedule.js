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

// moves an element up
function moveUp(pos) {
    let interactions = getInteractions();
    let found = [];
    let index = -1;

    for (let i = 0; i < interactions.length; i++) {
        if ($(interactions[i]).attr("id").toString() === pos.toString()) {
            found.push(interactions[i]);
            index = i;
        }
    }

    if(index === -1) {
        console.log("[ERROR] Cannot find element with specified ID");
    } else {
        let wrapper = interactions[index];

        if (wrapper.previousElementSibling) {
            wrapper.parentNode.insertBefore(wrapper, wrapper.previousElementSibling);
            store();
        }
    }
}

// moves an element down
function moveDown(pos) {
    let interactions = getInteractions();
    let found = [];
    let index = -1;

    for (let i = 0; i < interactions.length; i++) {
        if ($(interactions[i]).attr("id").toString() === pos.toString()) {
            found.push(interactions[i]);
            index = i;
        }
    }

    if(index === -1) {
        console.log("[ERROR] Cannot find element with specified ID");
    } else {
        let wrapper = interactions[index];

        if (wrapper.nextElementSibling) {
            wrapper.parentNode.insertBefore(wrapper.nextElementSibling, wrapper);
            store();
        }
    }
}

// removes an interaction from list
function removeInteraction(pos) {
    let interactions = getInteractions();
    let found = [];
    let index = -1;

    for (let i = 0; i < interactions.length; i++) {
        if ($(interactions[i]).attr("id").toString() === pos.toString()) {
            found.push(interactions[i]);
            index = i;
        }
    }

    if (found.length > 1) {
        console.error("[ERROR] ID was not unique");
    } else {
        interactions.splice(index, 1);
        $(found[0]).remove();

        if ((getInteractions()).length === 0) {
            $('#interactions').html("<h2 class=\"text-muted text-center\">No interactions found</h2>");
        }

        store();
    }
}

// returns all interaction list elements
function getInteractions() {
    return $('.interaction');
}

// creates a new list element
function createNew() {
    let el = $('#interactions');
    let inpEl = $('#newInteraction');
    let interactions = getInteractions();
    let inp = $(inpEl).val();
    let li = document.createElement("li");
    li.id = interactions.length;
    $(li).attr("value", inp)
    li.classList.add("list-group-item", "d-flex", "justify-content-between", "align-items-center", "interaction");
    li.innerHTML = "#" + interactions.length + " \"" + inp + "\"" +
        "                            <span><button onclick=\"(function(event) {" +
        "                                  event.stopPropagation();" +
        "                                  removeInteraction(" + interactions.length + ")" +
        "                              })(event);\" class=\"btn btn-sm btn-outline-secondary\" type=\"button\">" +
        "                                <i class=\"fa fa-trash\"></i>" +
        "                            </button>" +
        "                            <button onclick=\"(function(event) {" +
        "                                  event.stopPropagation();" +
        "                                  moveUp(" + interactions.length + ")" +
        "                              })(event);\" class=\"btn btn-sm btn-outline-secondary\" type=\"button\">" +
        "                                <i class=\"fa fa-arrow-up\"></i>" +
        "                            </button>" +
        "                            <button onclick=\"(function(event) {" +
        "                                  event.stopPropagation();" +
        "                                  moveDown(" + interactions.length + ")" +
        "                              })(event);\" class=\"btn btn-sm btn-outline-secondary\" type=\"button\">" +
        "                                <i class=\"fa fa-arrow-down\"></i>" +
        "                            </button></span>";

    if (interactions.length === 0) {
        $(el).html("");
    }
    $(el).append(li);
    $(inpEl).val("");
    store();
}

// stores current list to json
function store() {
    let interactions = getInteractions();
    let intStr = "";
    interactions.toArray().forEach(element => {
       let v = $(element).attr("value");
       intStr += v + ",";
    });
    intStr = intStr.slice(0, -1);

    $.get('index.php?schedule&id=' + CURRENT_ID + '&interactions=' + intStr, function(data) {
        console.log(data);
    });
}