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
    let launchModal = document.getElementById("launchModal");
    launchModal.addEventListener('hidden.bs.modal', invokeLaunchAll)

})();

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

function finished(index, max) {
    console.log("[INFO] Finished task " + (index+1) + " out of " + max);

    if(index === max-1) {
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
    }
}

function getText(id) {
    // read text from URL location
    var request = new XMLHttpRequest();
    request.open('GET', 'http://localhost:8080/report_' + id + '.txt', true);
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