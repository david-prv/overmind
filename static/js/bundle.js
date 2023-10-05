/**
 *  Bundle of all main functions and handlers
 *  this application needs during runtime
 *
 *  Version: 1.0.0
 *  Author: David Dewes <hello@david-dewes.de>
 */

// temp variable declarations
var counter = 0;
var counterS = 0;
var finishedIDs = [];
var temp = [];
var lastTarget = "";
var lastTargetDiff = [];

var g_status = {success: 0, cancelled: 0};
var g_overview = {ok: 0, suspicious: 0, critical: 0, unverified: 0};

// main view preparation
(function () {
    /*
     * Registration of all necessary
     * EventListeners if in main view
     */
    if (window.location.search !== "") return;

    let launchAll = document.getElementById("launchAll");
    launchAll.addEventListener("click", function (event) {
        event.stopImmediatePropagation();
        event.stopPropagation();
        event.preventDefault();
    });

    let searchBarInput = document.getElementById("search-bar-input");
    searchBarInput.addEventListener("keypress", function (event) {
        if (event.keyCode && event.keyCode == 13) selectSearch();
    });

    let launchSelectedOption = document.getElementById("launch-selected");
    launchSelectedOption.addEventListener("click", prepareSelectedModal);

    let launchModal = document.getElementById("launchModal");

    let selectedModal = document.getElementById("selectedModal");

    let resultModal = document.getElementById("resModal");
    resultModal.addEventListener('hidden.bs.modal', resetStatesAndOffers);

    $('#launch-all').on("click", () => {
        (new bootstrap.Modal(launchModal, {
            backdrop: 'static',
            keyboard: false
        })).show();
    });

    $('#launch-selected').on("click", () => {
        (new bootstrap.Modal(selectedModal, {
            backdrop: 'static',
            keyboard: false
        })).show();
    });

    showToolListAnimated();
})();

// renders graphs
async function renderGraphs() {
    let chart = new CanvasJS.Chart("status-chart", {
        animationEnabled: true,
        width: 300,
        height: 300,
        data: [{
            type: "doughnut",
            startAngle: 320,
            indexLabel: " #percent %",
            indexLabelFontColor: "black",
            indexLabelPlacement: "outside",
            indexLabelWrap: true,
            toolTipContent: "<b>{label}:</b> {y} (#percent%)",
            dataPoints: [
                {y: g_status.success, label: "Success", color: "#198754"},
                {y: g_status.cancelled, label: "Cancelled", color: "#dc3545"}
            ]
        }]
    });
    await chart.render();

    let chart2 = new CanvasJS.Chart("distance-chart", {
        animationEnabled: true,
        width: 300,
        height: 300,
        data: [{
            type: "doughnut",
            startAngle: 320,
            indexLabel: " #percent %",
            indexLabelFontColor: "black",
            indexLabelPlacement: "outside",
            indexLabelWrap: true,
            toolTipContent: "<b>{label}:</b> {y} (#percent%)",
            dataPoints: [
                {y: g_overview.ok, label: "OK", color: "#198754"},
                {y: g_overview.suspicious, label: "Suspicious", color: "#fd7e14"},
                {y: g_overview.critical, label: "Critical", color: "#dc3545"},
                {y: g_overview.unverified, label: "Unverified", color: "#adb5bd"}
            ]
        }]
    });
    await chart2.render();

    //renderGrades();

    resetGraphData();
}

function renderGrades() {
    let grade1 = document.getElementById("grade-1");
    let grade2 = document.getElementById("grade-2");

    // status
    let total1 = g_status.success + g_status.cancelled;
    let f_rate1 = g_status.cancelled / total1;

    // overview
    let total2 = g_overview.ok + g_overview.suspicious + g_overview.critical + g_overview.unverified;
    let f_rate2 = (g_overview.suspicious + g_overview.critical) / total2;

    switch (true) {
        case f_rate1 <= 0.2:
            grade1.innerHTML = "<span style='color:#198754'>A</span>";
            break;
        case f_rate1 <= 0.5:
            grade1.innerHTML = "<span style='color:#fd7e14'>B</span>";
            break;
        case f_rate1 <= 0.7:
            grade1.innerHTML = "<span style='color:darkorange'>C</span>";
            break;
        default:
        case f_rate1 <= 1.0:
            grade1.innerHTML = "<span style='color:#dc3545'>D</span>";
            break;
    }

    switch (true) {
        case f_rate2 <= 0.2:
            grade2.innerHTML = "<span style='color:#198754'>A</span>";
            break;
        case f_rate2 <= 0.5:
            grade2.innerHTML = "<span style='color:#fd7e14'>B</span>";
            break;
        case f_rate2 <= 0.7:
            grade2.innerHTML = "<span style='color:darkorange'>C</span>";
            break;
        default:
        case f_rate2 <= 1.0:
            grade2.innerHTML = "<span style='color:#dc3545'>D</span>";
            break;
    }
}

// helper to reset displayed graph data
function resetGraphData() {
    g_overview.ok = 0;
    g_overview.suspicious = 0;
    g_overview.critical = 0;
    g_overview.unverified = 0;

    g_status.success = 0;
    g_status.cancelled = 0;
}

// helper to show tools list with fade-in animation
function showToolListAnimated() {
    let tools = $('#tool-list').children();
    for (let i = 0; i < tools.length; i++) {
        setTimeout(
            (c = i) => {
                tools[c].classList.add("animated-show");
            },
            i * 100);
    }
}

// helper method to get the right index from json map
function getToolIndexById(id) {
    for (let i = 0; i < DATA.length; i++) {
        if (DATA[i]["id"] === id.toString()) {
            return i;
        }
    }
    return -1;
}

// handler for tool deletion button
function deleteTool(id, debug = false) {
    let route = (debug === true) ? "" : "delete&id=";
    $.get('index.php?' + route + id, function (data) {
        if (data === "done" || debug) {
            $('#tool-' + id).remove();
            alertSuccess("Tool with ID=" + id + " was deleted successfully!");
            if (($('#tool-list').children()).length <= 0) {
                // re-enable buttons
                $('#launchAll, #launchOptions').prop('disabled', (i, v) => !v);

                // write empty message
                $('#tool-list').html("<h2 class='text-muted text-center'>No tools found</h2>\n" +
                    "                                                          <a class='no-cursor' title='Vector by https://vecteezy.com'>\n" +
                    "                                                            <img class='img-center' src='/static/img/sleep.jpg' />\n" +
                    "                                                          </a>");
            }
        } else {
            alertError("Could not delete tool!");
        }
    });
}

// handler for tool edit button
function editTool(id) {
    let tool = getToolIndexById(id);
    if (tool === -1) {
        console.error("[ERROR] Could not find tool in map.");
        alertError("Unknown tool ID!");
        return;
    }

    $('#edit-id').val(id);
    $('#edit-name').val(DATA[tool]["name"]);
    $('#edit-creator').val(DATA[tool]["author"]);
    $('#edit-url').val(DATA[tool]["url"]);
    $('#edit-version').val(DATA[tool]["version"]);
    $('#edit-engine').val(DATA[tool]["engine"]);
    $('#edit-index').val(DATA[tool]["index"]);
    $('#edit-args').val(DATA[tool]["args"]);
    $('#edit-description').val(DATA[tool]["description"]);
    $('#edit-keywords').val(DATA[tool]["keywords"]);
}

// reads values from form and submits them to backend
function submitEdit() {
    let name, creator, url, version, engine, index, args, description, keywords, id;

    id = $('#edit-id').val();
    name = $('#edit-name').val();
    creator = $('#edit-creator').val();
    url = $('#edit-url').val();
    version = $('#edit-version').val();
    engine = $('#edit-engine').val();
    index = $('#edit-index').val();
    args = $('#edit-args').val();
    description = $('#edit-description').val();
    keywords = $('#edit-keywords').val();


    let json = {
        "id": id,
        "name": name,
        "author": creator,
        "url": url,
        "version": version,
        "engine": engine,
        "index": index,
        "args": args,
        "description": description,
        "keywords": keywords
    }

    $.get('index.php?edit&json=' + JSON.stringify(json), function (data) {
        if (data === "done") {
            document.getElementById("edit-result").innerHTML = "<span style='color:green;'>Successfully saved.</span>";
            alertSuccess("Successfully saved edited information!");
        } else {
            document.getElementById("edit-result").innerHTML = "<span style='color:red;'>Could not be saved.</span>";
            alertError("Could not save edited information!");
        }
    });
}

// reset tool states & offers after finished run
function resetStatesAndOffers() {
    let dropZone = document.getElementById("dropzone");
    dropZone.innerHTML = "";

    let comment = document.getElementById("comment");
    $(comment).val("");

    for (let i = 0; i < DATA.length; i++) {
        let el = $(`#state-${i}`)[0];
        console.log("[DEBUG] Resetting for ID " + i + " has started...");
        if (!el) {
            console.log("[DEBUG] State Reset for ID " + i + " was skipped");
            continue;
        }
        el.innerText = "Idling...";
    }
}

// show edit tools
function editTools() {
    let alertNotice = document.getElementById("edit-mode-alert");
    if (alertNotice.classList.contains("hidden")) alertNotice.classList.remove("hidden");
    else alertNotice.classList.add("hidden");

    $('#cmd-edit').find("i").toggleClass("fa-spin");

    $('#launchAll, #launchOptions, #cmd-integrate, #cmd-exec-bot').prop('disabled', (i, v) => !v);
    for (let i = 0; i < DATA.length; i++) {
        $(`#options-tool-${DATA[i]["id"]}`).toggleClass("hidden");
        $(`#state-${DATA[i]["id"]}`).toggleClass("hidden");
    }
}

// prepares the modal for the launchSelected event
function prepareSelectedModal(event) {
    // all selected elements
    let selected = $('.selection');

    // define and clear list
    let list = document.getElementById("selection-list");
    list.innerHTML = "";

    // clean up keys
    delete (selected["length"]);
    delete (selected["prevObject"]);

    // iterate through tools
    let keys = Object.keys(selected);
    if (keys.length === 0) {
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
        alertError("No target URL defined!");
        return;
    }
    if (target.indexOf("://") === -1) target = $("#protocol-alt").val() + "://" + target;
    lastTarget = target;

    let inputs = $('#selection-list input');
    let selectedInputs = inputs
        .filter(function (index) {
            return $(inputs[index]).is(':checked');
        });
    selectedInputs = selectedInputs.map(function (index) {
        return selectedInputs[index].value;
    });

    if (selectedInputs.length <= 0) {
        console.error("[ERROR] All tools unselected!");
        alertError("No tools selected!");
        return;
    }

    $("#launchAll").html("<i class=\"fa fa-circle-o-notch fa-spin\"></i> Launching...");
    $('#running-alert').removeClass("hidden");

    for (let i = 0; i < DATA.length; i++) {
        let currentTool = DATA[i];
        if (!Object.values(selectedInputs).includes(currentTool["id"])) {
            continue;
        }
        $("#state-" + i).innerText = "Waiting...";
        queue.push("?run&engine=" + currentTool["engine"] + "&index=" + currentTool["index"] + "&args=\""
            + currentTool["args"].replace("%URL%", target).replace("%RAW%", target.replace("http://", "").replace("https://", "")) + "\"&id=" + currentTool["id"] + "&target=" + target);
    }

    alertSuccess(`Running ${queue.length} scanners...`);

    for (let j = 0; j < queue.length; j++) {
        $("#state-" + selectedInputs[j]).html("<span class='blinking'>Running...</span>");
        $.get("/index.php" + queue[j], function (data, status, xhr, id = selectedInputs[j], callback = finishedSelected, max = queue.length) {
            if (data === "done") {
                $("#state-" + selectedInputs[j]).html("<span style='color:green!important;'>Finished</span>");
                g_status.success++;
            } else {
                $("#state-" + selectedInputs[j]).html("<span style='color:red!important;'>Cancelled</span>");
                let idx = getToolIndexById(selectedInputs[j]);
                let name = (idx === -1) ? "Unknown" : DATA[idx]["name"];
                alertError(`${name} was cancelled!`);
                g_status.cancelled++;
            }
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
        alertError("No target URL defined!");
        return;
    }
    if (target.indexOf("://") === -1) target = $("#protocol").val() + "://" + target;
    lastTarget = target;

    $("#launchAll").html("<i class=\"fa fa-circle-o-notch fa-spin\"></i> Launching...");
    $('#running-alert').removeClass("hidden");

    for (let i = 0; i < DATA.length; i++) {
        let currentTool = DATA[i];
        $("#state-" + i).innerText = "Waiting...";
        queue.push("?run&engine=" + currentTool["engine"] + "&index=" + currentTool["index"]
            + "&args=\"" + currentTool["args"].replace("%URL%", target).replace("%RAW%", target.replace("http://", "").replace("https://", "")) + "\"&id=" + currentTool["id"]
            + "&target=" + target);
    }

    let skip = [];
    let exclusion = $('#exclusion').val();
    let whitelist = $('#whitelist').val();
    if (exclusion.trim() === '*' && whitelist.indexOf(",") !== -1) {
        whitelist = whitelist.split(",").map(i => {
            return parseInt(i);
        });
    } else {
        whitelist = [parseInt(whitelist)];
    }

    if (exclusion.trim() === "*") {
        for (let i = 0; i < DATA.length; i++) {
            if (!whitelist.includes(parseInt(DATA[i]["id"]))) skip.push(parseInt(DATA[i]["id"]));
        }
    } else {
        if (exclusion.indexOf(",") !== -1) {
            skip = exclusion.split(",").map(i => {
                return parseInt(i);
            });
        } else {
            if (exclusion !== "" && exclusion !== undefined) {
                skip.push(parseInt(exclusion));
            }
        }
    }

    if (skip.length === queue.length) {
        console.error("[ERROR] All tools skipped...");
        alertError("No tool could be executed!");
        $("#launchAll").html("<i class=\"fa fa-forward\"></i> Launch All");
        return;
    }

    $('#exclusion').val("");
    $('#whitelist').val("");

    alertSuccess(`Running ${queue.length} scanners...`)

    for (let j = 0; j < queue.length; j++) {
        let id = DATA[j]["id"];
        let name = DATA[j]["name"];

        if (skip.includes(parseInt(id))) {
            finished(j, queue.length);
            continue;
        }

        console.log("[DEBUG] Running Tool with ID", id);

        $("#state-" + id).html("<span class='blinking'>Running...</span>");
        $.get("/index.php" + queue[j], function (data, status, xhr, identity = id, callback = finished, max = queue.length, display = name) {
            if (data === "done") {
                $("#state-" + identity).html("<span style='color:green!important;'>Finished</span>");
                g_status.success++;
            } else {
                $("#state-" + identity).html("<span style='color:red!important;'>Cancelled</span>");
                alertError(`${display} was cancelled!`)
                g_status.cancelled++;
            }
            temp.push(identity);
            callback(identity, max);
        });
    }
}

// handles the current progress state for launchSelected event
function finishedSelected(index, selected) {
    let evalProg = $('#evaluation-progress');
    counterS++;
    console.log("[INFO] Finished task (" + counterS + " / " + selected.length + ")");
    evalProg.html("(" + counterS + "/" + selected.length + ")");
    $("#launchAll").html("<i class=\"fa fa-circle-o-notch fa-spin\"></i> Launching... (" + counterS + "/" + selected.length + ")");

    if (counterS === selected.length) {
        let resContent = document.getElementById("result-content");

        let html = "<div class=\"accordion\" id=\"accordion\">";
        for (let i = 0; i < DATA.length; i++) {
            let tool = DATA[i];
            if (!Object.values(selected).includes(tool["id"])) {
                continue;
            }

            html += "<div class=\"accordion-item\">" +
                "    <h2 class=\"accordion-header\" id=\"flush-heading-" + i + "\">" +
                "      <button class=\"accordion-button collapsed\" type=\"button\" data-bs-toggle=\"collapse\" data-bs-target=\"#flush-collapse" + i + "\" aria-expanded=\"false\" aria-controls=\"flush-collapse" + i + "\">" +
                "        " + tool["name"] +
                "      <span class='distance' id='distance-" + tool["id"] + "'></span>" +
                "      </button>" +
                "    </h2>" +
                "    <div id=\"flush-collapse" + i + "\" class=\"accordion-collapse collapse\" aria-labelledby=\"flush-heading" + i + "\" data-bs-parent=\"#accordion\">" +
                //"       (<a class='mt-3 p-2' target='_blank' href='/reports/report_" + tool["id"] + ".txt'>Open Full</a>)" +
                // "       <div id='body-" + tool["id"] + "' class=\"accordion-body\"></div>" +
                "       <textarea style='height:400px;resize:none;width:100%;border:none;' id='body-" + tool["id"] + "' placeholder='Loading...'></textarea> " +
                "    </div>" +
                "  </div>";
            finishedIDs.push(tool["id"]);
        }
        html += "</div>";

        $(resContent).html(html);

        for (let j = 0; j < finishedIDs.length; j++) {
            getText(finishedIDs[j]);
            getDistance(finishedIDs[j]);
        }

        setTimeout(function () {
            renderGraphs();
        }, 500);

        let resultModal = new bootstrap.Modal(document.getElementById("resModal"), {
            backdrop: 'static',
            keyboard: false
        });
        resultModal.show();
        $("#launchAll").html("<i class=\"fa fa-forward\"></i> Launch All");
        $('#running-alert').addClass("hidden");
        evalProg.html("");
        counterS = 0;
        finishedIDs = [];
    }
}

// handles the current progress state
function finished(index, max) {
    let evalProg = $('#evaluation-progress');
    counter++;
    console.log("[INFO] Finished task (" + counter + " / " + max + ")");
    evalProg.html("(" + counter + "/" + max + ")");
    $("#launchAll").html("<i class=\"fa fa-circle-o-notch fa-spin\"></i> Launching... (" + counter + "/" + max + ")");

    if (counter === max) {
        let resContent = document.getElementById("result-content");

        let html = "<div class=\"accordion\" id=\"accordion\">";
        for (let i = 0; i < temp.length; i++) {
            let tool = DATA[getToolIndexById(temp[i])];
            html += "<div class=\"accordion-item\">" +
                "    <h2 class=\"accordion-header\" id=\"flush-heading" + i + "\">" +
                "      <button class=\"accordion-button collapsed\" type=\"button\" data-bs-toggle=\"collapse\" data-bs-target=\"#flush-collapse" + i + "\" aria-expanded=\"false\" aria-controls=\"flush-collapse" + i + "\">" +
                "        " + tool["name"] +
                "      <span class='distance' id='distance-" + tool["id"] + "'></span>" +
                "      </button>" +
                "    </h2>" +
                "    <div id=\"flush-collapse" + i + "\" class=\"accordion-collapse collapse\" aria-labelledby=\"flush-heading" + i + "\" data-bs-parent=\"#accordion\">" +
                //"       (<a class='mt-3' target='_blank' href='/reports/report_" + tool["id"] + ".txt'>Open Full</a>)" +
                // "       <div id='body-" + tool["id"] + "' class=\"accordion-body\"></div>" +
                "       <textarea style='height:400px;resize:none;width:100%;border:none;' id='body-" + tool["id"] + "' placeholder='Loading...'></textarea> " +
                "    </div>" +
                "  </div>";
        }
        html += "</div>";

        $(resContent).html(html);

        for (let j = 0; j < temp.length; j++) {
            getText(temp[j]);
            getDistance(temp[j]);
        }
        temp = [];

        setTimeout(function () {
            renderGraphs();
        }, 500);

        let resultModal = new bootstrap.Modal(document.getElementById("resModal"), {});
        resultModal.show();
        $("#launchAll").html("<i class=\"fa fa-forward\"></i> Launch All");
        $('#running-alert').addClass("hidden");
        evalProg.html("");
        counter = 0;
    }
}

// fetches text from a .txt report
function getText(id) {
    console.log("[INFO] Fetching report", id, '/reports/report_' + id + '.txt');

    // read text from URL location
    var request = new XMLHttpRequest();
    request.open('GET', '/reports/report_' + id + '.txt', true);
    request.send(null);
    request.onreadystatechange = function (event, k = id) {
        if (request.readyState === 4 && request.status === 200) {
            var type = request.getResponseHeader('Content-Type');
            if (type.indexOf("text") !== 1) {
                document.getElementById("body-" + k).value = request.responseText
                    .replace("\
                    ", "\n");
            }
        }
    }
}

// fetches analysis result
function getDistance(id) {
    console.log("[INFO] Fetching analysis result", id);

    // read text from URL location
    var request = new XMLHttpRequest();
    request.open('GET', '/index.php?analyze&id=' + id, true);
    request.send(null);
    request.onreadystatechange = function (event, k = id) {
        if (request.readyState === 4 && request.status === 200) {
            var type = request.getResponseHeader('Content-Type');
            if (type.indexOf("text") !== 1) {
                // console.log(request.responseText, k);
                let dist = request.responseText.split("|")[0];
                let bad = request.responseText.split("|")[1];

                try {
                    lastTargetDiff = JSON.parse(bad);
                } catch (e) {
                    console.info("[WARNING] Could not parse response:", e);
                    lastTargetDiff = [];
                }
                document.getElementById("distance-" + k).innerText = dist;
                console.log("[DEBUG] Updating distance coloring...");
                correctDistanceColoring(k, dist);
            }
        }
    }
}

// sets the correct color for the dist indicator
function correctDistanceColoring(id, score) {
    let el = document.getElementById("distance-" + id);
    let i_score = parseInt(score);
    switch (true) {
        case (i_score < 0):
            el.style.color = "darkgray";
            el.innerHTML = "<i title=\"Missing reference or integrity not verifiable\" " +
                "class=\"fa fa-exclamation-circle\" aria-hidden=\"true\"></i>";
            g_overview.unverified++;
            break;
        case (i_score <= 1000):
            el.style.color = "green";
            g_overview.ok++;
            break;
        case (i_score <= 5000):
            el.style.color = "orange";
            g_overview.suspicious++;
            break;
        default:
            el.style.color = "red";
            g_overview.critical++;
            break;
    }
}

// removes selection from ALL tools
function deselectTools() {
    let tools = $('#tool-list').children();
    tools.toArray().forEach(tool => {
        if ($(tool).hasClass("selection")) {
            $(tool).removeClass("selection");
        }
    });
}

// add selection to ALL tools
function selectAllTools() {
    let tools = $('#tool-list').children();
    tools.toArray().forEach(tool => {
        if (!$(tool).hasClass("selection")) {
            $(tool).addClass("selection");
        }
    });
}

// select tools by keywords
function selectSearch() {
    let searchText = $("#search-bar-input").val();

    if (searchText === null || searchText === undefined || searchText === "") return;

    deselectTools()

    for (let i = 0; i < DATA.length; i++) {
        let tool = DATA[i];
        let keywords = searchText.replace(/ /g, "").split(",");

        for (let j = 0; j < keywords.length; j++) {
            if (tool["keywords"].indexOf(keywords[j]) !== -1) {
                let toolEl = $('#tool-' + tool["id"]);
                if (!toolEl) continue;

                $(toolEl).click();

                break;
            }
        }
    }
}

// parse selected offers from html view
function parseOffers(zone) {
    let elements = zone.children;
    if (elements.length <= 0) {
        return [];
    }

    let tmp = [];
    for (let i = 0; i < elements.length; i++) {
        let identifier = elements[i].getAttribute("id");
        tmp.push({
            caption: elements[i].getAttribute("data-text"),
            price: elements[i].getAttribute("data-price"),
            comment: document.getElementById("inputComment-" + identifier).value
        });
    }
    return tmp;
}

// parse scan results from html view
function parseResults(results) {

    function bounds(l) {
        let t = [...l].map((x) => {
            return parseInt(x);
        }).sort(function (a, b) {
            return a - b
        });
        return [t[0], t[t.length - 1]];
    }

    function normalize(i, x, q = 100) {
        let bound = bounds(x);
        let min = bound[0], max = bound[1];

        if (min - max === 0) return 0;
        else return Math.abs((x[i] - min) / (max - min)) * q;
    }

    let elements = results.firstChild.children;
    if (elements.length <= 0) {
        return [];
    }

    let tmp = [];
    let dist = [];
    for (let j = 0; j < elements.length; j++) {
        let name = results.firstChild.children[j].children[0].children[0].innerText.trim().split("\n")[0];
        let distance = results.firstChild.children[j].children[0].children[0].innerText.trim().split("\n")[1];
        dist.push(distance);
        tmp.push({
            testName: name,
            distance: distance,
            normalized: -1
        });
    }

    for (let k = 0; k < elements.length; k++) {
        tmp[k].normalized = Math.round(normalize(k, dist) * 100) / 100;
    }

    return tmp;
}

// collect scan and offer information and redirect to html2pdf util
function collectInfoAndRedirect() {
    let dropZone = document.getElementById("dropzone");
    let resultContent = document.getElementById("result-content");
    let status_chart = document.getElementsByClassName("canvasjs-chart-canvas")[1];
    let overview_chart = document.getElementsByClassName("canvasjs-chart-canvas")[3];
    let comment = document.getElementById("comment");

    if (dropZone === null || resultContent === null || status_chart === null
        || overview_chart === null || comment === null) {
        console.error("[ERROR] dropzone, result-content, comment or any chart could not be found!");
        alertError("Dropzone, result content, comment or any chart is not available!");
        return -1;
    }

    let offers = parseOffers(dropZone);
    let results = parseResults(resultContent);

    let result_information = encodeURIComponent(JSON.stringify({
        target_url: lastTarget,
        scanner_results: results,
        our_offers: offers,
        bad_words: lastTargetDiff,
        ref_token: PERSONAL_REF_TOKEN,
        status_img: status_chart.toDataURL("image/jpeg"),
        overview_img: overview_chart.toDataURL("image/jpeg"),
        offer_comment: $(comment).val()
    }).escapeSpecialChars());

    let handle = window.open("/app/utils/third-party/html2pdf/index.php?data=" + result_information, 'pdf-generator', 'menubar=1,resizable=0,width=350,height=250');
    setTimeout(function (h = handle) { h.close(); }, 5000);
}

// triggers snapshot backend endpoint
function createSnapshot() {
    $.get("/index.php?snapshot", function (data, status) {
        if (status === "success" && data === "done") {
            alertSuccess("Snapshot was created successfully!");
        } else {
            alertError("Snapshot creation has failed!");
        }
    });
}