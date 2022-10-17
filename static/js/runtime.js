/**
 *  Bundle of all main functions and handlers
 *  this application needs during runtime
 */

(function() {
    // register launch components
    let launchAll = document.getElementById("launchAll");
    launchAll.addEventListener("click", function(event) {
        event.stopImmediatePropagation();
        event.stopPropagation();
        event.preventDefault();
    });
    let launchSelectedOption = document.getElementById("launch-selected");
    launchSelectedOption.addEventListener("click", prepareSelectedModal);

    let launchModal = document.getElementById("launchModal");
    launchModal.addEventListener('hidden.bs.modal', invokeLaunchAll)

    let selectedModal = document.getElementById("selectedModal");
    selectedModal.addEventListener('hidden.bs.modal', invokeLaunchSelected);

    $('#launch-all').on("click", () => {
        (new bootstrap.Modal(launchModal, {})).show();
    });
    $('#launch-selected').on("click", () => {
        (new bootstrap.Modal(selectedModal, {})).show();
    });
})();

// prepares the modal for the launchSelected event
function prepareSelectedModal(event) {
    // all selected elements
    let selected = $('.selection');

    // define and clear list
    let list = document.getElementById("selection-list");
    list.innerHTML = "";

    // clean up keys
    delete(selected["length"]);
    delete(selected["prevObject"]);

    // iterate through tools
    let keys = Object.keys(selected);
    if(keys.length === 0) {
        list.innerHTML = "<li style='font-style: italic'>Empty</li>";
        $('#btn-start-selected').attr("disabled", true);
        return;
    }
    $('#btn-start-selected').removeAttr("disabled");
    for (let i = 0; i < keys.length; i++) {
        let tool = selected[keys[i]];

        let newListElement = document.createElement("li");
        newListElement.id = "list-" + $(tool).attr("id");
        newListElement.innerHTML = `<input class=\"form-check-input me-1\" type=\"checkbox\" value=\"${$(tool).attr("id").replace("tool-", "")}\" checked>
                                        ${document.getElementById('title-' + $(tool).attr("id").split("-")[1]).innerText}`;

        list.appendChild(newListElement);
    }
}

// invokes all methods needed for the launchSelected event
function invokeLaunchSelected(event) {
    let queue = [];
    let target = $("#target-url-alt").val();
    if (target === '' || target === null || target === undefined) {
        console.error("[ERROR] Missing target...");
        return;
    }
    if (target.indexOf("://") === -1) target = $("#protocol-alt").val() + "://" + target;

    $("#launchAll").html("<i class=\"fa fa-circle-o-notch fa-spin\"></i> Launching...");

    let inputs = $('#selection-list input');
    let selectedInputs = inputs
        .filter(function(index) { return $(inputs[index]).is(':checked'); });
    selectedInputs = selectedInputs.map(function(index) { return selectedInputs[index].value; });

    for(let i = 0; i < DATA.length; i++) {
        let currentTool = DATA[i];
        if(!Object.values(selectedInputs).includes(currentTool["id"])) { continue; }
        $("#state-" + i).innerText = "Waiting...";
        queue.push("?run&engine=" + currentTool["engine"] + "&index=" + currentTool["index"] + "&args=\"" + currentTool["args"].replace("%URL%", target) + "\"&id=" + currentTool["id"]);
    }

    console.log(queue);

    for(let j = 0; j < queue.length; j++) {
        $("#state-" + selectedInputs[j]).html("<span class='blinking'>Running...</span>");
        $.get("/index.php" + queue[j], function(data, status, xhr, id=selectedInputs[j], callback=finishedSelected, max=queue.length) {
            $("#state-" + selectedInputs[j]).html("<span style='color:green!important;'>Finished</span>");
            callback(id, selectedInputs);
        });
    }
}

// invokes all methods needed for the launchAll event
function invokeLaunchAll(event) {
    let queue = [];
    let target = $("#target-url").val();
    if (target === '' || target === null || target === undefined) {
        console.error("[ERROR] Missing target...");
        return;
    }
    if (target.indexOf("://") === -1) target = $("#protocol").val() + "://" + target;

    $("#launchAll").html("<i class=\"fa fa-circle-o-notch fa-spin\"></i> Launching...");

    for(let i = 0; i < DATA.length; i++) {
        let currentTool = DATA[i];
        $("#state-" + i).innerText = "Waiting...";
        queue.push("?run&engine=" + currentTool["engine"] + "&index=" + currentTool["index"] + "&args=\"" + currentTool["args"].replace("%URL%", target) + "\"&id=" + currentTool["id"]);
    }

    for(let j = 0; j < queue.length; j++) {
        $("#state-" + j).html("<span class='blinking'>Running...</span>");
        $.get("/index.php" + queue[j], function(data, status, xhr, id=j, callback=finished, max=queue.length) {
            $("#state-" + j).html("<span style='color:green!important;'>Finished</span>");
            callback(id, max);
        });
    }
}

var counterS = 0;
var finishedIDs = [];
function finishedSelected(index, selected) {
    counterS++;
    console.log("[INFO] Finished task (" + counterS + " / " + selected.length + ")");

    if(counterS === selected.length) {
        let resContent = document.getElementById("result-content");

        let html = "<div class=\"accordion accordion-flush\" id=\"accordion\">";
        for(let i = 0; i < DATA.length; i++) {
            let tool = DATA[i];
            if(!Object.values(selected).includes(tool["id"])) { continue; }

            html += "<div class=\"accordion-item\">" +
                "    <h2 class=\"accordion-header\" id=\"flush-heading-" + i + "\">" +
                "      <button class=\"accordion-button collapsed\" type=\"button\" data-bs-toggle=\"collapse\" data-bs-target=\"#flush-collapse" + i + "\" aria-expanded=\"false\" aria-controls=\"flush-collapse" + i + "\">" +
                "        " + tool["name"] +
                "      </button>" +
                "    </h2>" +
                "    <div id=\"flush-collapse" + i + "\" class=\"accordion-collapse collapse\" aria-labelledby=\"flush-heading" + i + "\" data-bs-parent=\"#accordion\">" +
                "      <div id='body-" + tool["id"] + "' class=\"accordion-body\"></div>" +
                "    </div>" +
                "  </div>";
            finishedIDs.push(tool["id"]);
        }
        html += "</div>";

        console.log(finishedIDs);
        $(resContent).html(html);

        for(let j = 0; j < finishedIDs.length; j++) {
            getText(finishedIDs[j]);
        }

        let resultModal = new bootstrap.Modal(document.getElementById("resModal"), {});
        resultModal.show();
        $("#launchAll").html("<i class=\"fa fa-gears\"></i> Launch Scanners");
        counterS = 0;
        finishedIDs = [];
    }
}

// handles the current progress state
var counter = 0;
function finished(index, max) {
    counter++;
    console.log("[INFO] Finished task (" + counter + " / " + max + ")");

    if(counter === max) {
        let resContent = document.getElementById("result-content");

        let html = "<div class=\"accordion accordion-flush\" id=\"accordion\">";
        for(let i = 0; i < max; i++) {
            let tool = DATA[i];
            html += "<div class=\"accordion-item\">" +
                "    <h2 class=\"accordion-header\" id=\"flush-heading" + i + "\">" +
                "      <button class=\"accordion-button collapsed\" type=\"button\" data-bs-toggle=\"collapse\" data-bs-target=\"#flush-collapse" + i + "\" aria-expanded=\"false\" aria-controls=\"flush-collapse" + i + "\">" +
                "        " + tool["name"] +
                "      </button>" +
                "    </h2>" +
                "    <div id=\"flush-collapse" + i + "\" class=\"accordion-collapse collapse\" aria-labelledby=\"flush-heading" + i + "\" data-bs-parent=\"#accordion\">" +
                "      <div id='body-" + tool["id"] + "' class=\"accordion-body\"></div>" +
                "    </div>" +
                "  </div>";
        }
        html += "</div>";

        $(resContent).html(html);

        for(let j = 0; j < max; j++) {
            getText(j);
        }

        let resultModal = new bootstrap.Modal(document.getElementById("resModal"), {});
        resultModal.show();
        $("#launchAll").html("<i class=\"fa fa-gears\"></i> Launch Scanners");
        counter = 0;
    }
}

// fetches text from a .txt report
function getText(id) {

    console.log("[INFO] Fetching report", id, 'http://localhost:8080/reports/report_' + id + '.txt');

    // read text from URL location
    var request = new XMLHttpRequest();
    request.open('GET', 'http://localhost:8080/reports/report_' + id + '.txt', true);
    request.send(null);
    request.onreadystatechange = function (event, k=id) {
        if (request.readyState === 4 && request.status === 200) {
            var type = request.getResponseHeader('Content-Type');
            if (type.indexOf("text") !== 1) {
                console.log(request.responseText, k);
                document.getElementById("body-"+k).innerText = request.responseText;
            }
        }
    }
}