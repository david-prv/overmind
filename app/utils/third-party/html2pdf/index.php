<?php
// read passed data...

if (!isset($_GET["data"])) die("Invalid data provided!");

// try to decode data...

$data = json_decode(urldecode($_GET["data"]), true);
if (is_null($data)) die("Whoops! Couldn't parse your data!");

// parsing data...

if (!isset($data["target_url"]) || !isset($data["scanner_results"])
    || !isset($data["our_offers"]) || !isset($data["bad_words"]) || !isset($data["ref_token"])
    || !isset($data["status_img"]) || !isset($data["overview_img"])
    || !isset($data["offer_comment"])) {
    die("Sorry, you provided the wrong data format!");
}

$targetUrl = $data["target_url"];
$scannerResults = $data["scanner_results"];
$ourOffers = $data["our_offers"];
$badWords = $data["bad_words"];
$refToken = $data["ref_token"];
$statusImg = $data["status_img"];
$overviewImg = $data["overview_img"];
$offerComment = $data["offer_comment"];

?>

<!--
    A html2pdf Frontend

    Author: David Dewes
    Website: https://github.com/david-prv
-->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>

    <!-- General Style -->
    <style type="text/css">
        body {
            padding: 20px;
            overflow:hidden!important;
        }

        .loader {
            width: 50px;
            height: 50px;
            border: 5px solid #000000;
            border-bottom-color: transparent;
            border-radius: 50%;
            display: inline-block;
            box-sizing: border-box;
            animation: rotation 1s linear infinite;
        }

        .loader-h1 {
            margin-bottom: 100px;
            margin-left: 120px;
            margin-top: 60px;
        }

        @keyframes rotation {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }

        .print-container {
            position: relative;
        }

        .card {
            box-sizing: content-box;
            width: 100%;
            height: 100%;
            padding: 30px;
            border:none!important;
            background-color: white;
            border-radius:0!important;
        }

        h2 {
            text-align: center;
            color: #24650b;
        }

        .logo {
            width: 200px;
        }

        .imprint {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 300px;
            text-align: right;
            font-size: 15px;
        }

        .bottom {
            font-family: 'Courier New', Courier, monospace;
            margin-top: 10px;
            margin-bottom: 0 !important;
        }

        #makepdf {
            font-family: Arial, serif;
        }
    </style>

    <!-- Table Style -->
    <style type="text/css">
        .tg {
            border-collapse: collapse;
            border-spacing: 0;
        }

        .tg td {
            border-color: black;
            border-style: solid;
            border-width: 1px;
            font-family: Arial, sans-serif;
            font-size: 14px;
            overflow: hidden;
            padding: 10px 5px;
            word-break: normal;
        }

        .tg th {
            border-color: black;
            border-style: solid;
            border-width: 1px;
            font-family: Arial, sans-serif;
            font-size: 14px;
            font-weight: normal;
            overflow: hidden;
            padding: 10px 5px;
            word-break: normal;
        }

        .tg .tg-c3ow {
            border-color: inherit;
            text-align: center;
            vertical-align: top
        }

        .tg .tg-fymr {
            border-color: inherit;
            font-weight: bold;
            text-align: left;
            vertical-align: top
        }

        thead {
            background: #b5b5b5;
        }
    </style>
</head>

<body>
<div class="container print-container p-3">
    <h1 class="loader-h1"><span id="loader-spinner" class="loader"></span></h1>

    <div class="card" id="makepdf">
        <img class="logo" src="https://etage-4.de/etage4_wp/wp-content/uploads/2020/05/Logo_3000_dark.png"/>

        <p class="imprint">
            <strong>Imprint</strong><br>
            This scan was created by:<br>
            ETAGE 4 UG (haftungsbeschr&auml;nkt)<br>
            Fl&auml;chenbachstra&#xDF;e 18<br>
            66606 Bliesen<br>
        </p>

        <h1>Risk Assessment</h1>

        &mdash;

        <!-- <br/>
        TODO: Add charts to report!
        <div class="container">
            <div class="row">
                <div class="col-sm">
                    <img src="<?php echo $statusImg; ?>" />
                </div>
                <div class="col-sm">
                    <img src="<?php echo $overviewImg; ?>">
                </div>
            </div>
        </div>

        <br/>
        -->

        <h5>Reference/Actual Dist.</h5>
        <table class="tg">
            <thead>
            <tr>
                <th class="tg-fymr">Component</th>
                <th class="tg-fymr">Distance</th>
                <th class="tg-fymr">Significance</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if (count($scannerResults) === 0) {
                echo "<tr>
                            <td contenteditable class=\"tg-c3ow\">&mdash;</td>
                            <td contenteditable class=\"tg-c3ow\">&mdash;</td>
                            <td contenteditable class=\"tg-c3ow\">&mdash;</td>
                          </tr>";
            } else {
                foreach ($scannerResults as $result) {
                    // prepare scanner result fields
                    $testName = (isset($result["testName"])) ? $result["testName"] : "&mdash;";
                    $testDistance = (isset($result["distance"])) ? $result["distance"] : "&mdash;";
                    $testNormalized = (isset($result["normalized"])) ? $result["normalized"] . "%" : "&mdash;";

                    echo "<tr>
                             <td contenteditable class=\"tg-c3ow\">{$testName}</td>
                             <td contenteditable class=\"tg-c3ow\">{$testDistance}</td>
                             <td contenteditable class=\"tg-c3ow\">{$testNormalized}</td>
                          </tr>";
                }
            }
            ?>
            </tbody>
        </table>

        <br>

        <h5>Recommended Action</h5>
        <table class="tg">
            <thead>
            <tr>
                <th class="tg-fymr">Offer</th>
                <th class="tg-fymr">Price</th>
                <th class="tg-fymr">Comment</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if (count($ourOffers) === 0) {
                echo "<tr>
                            <td contenteditable class=\"tg-c3ow\">&mdash;</td>
                            <td contenteditable class=\"tg-c3ow\">&mdash;</td>
                            <td contenteditable class=\"tg-c3ow\">&mdash;</td>
                         </tr>";
            } else {
                foreach ($ourOffers as $offer) {
                    $comment = (isset($offer["comment"]) && $offer["comment"] !== "") ? $offer["comment"] : "&mdash;";
                    echo "<tr>
                                <td contenteditable class=\"tg-c3ow\">{$offer["caption"]}</td>
                                <td contenteditable class=\"tg-c3ow\">{$offer["price"]}</td>
                                <td contenteditable class=\"tg-c3ow\">{$comment}</td>
                             </tr>";
                }
            }
            ?>
            </tbody>
        </table>

        <br>

        <h5>Comment</h5>
        <textarea rows="10" class="txt-comment"><?php echo $offerComment; ?></textarea>

        <p class="bottom">
            Report generated at <span id="timestamp"></span><br/>
            Target-URL: <span id="target"></span><br/>
            Ref-Token: <span id="ref"><?php echo $refToken; ?></span>
        </p>
    </div>
</div>

<script>
    let button = document.getElementById("button");
    let makepdf = document.getElementById("makepdf");
    let timestamp = document.getElementById("timestamp");
    let targetURL = document.getElementById("target");
    let spinner = document.getElementById("loader-spinner");

    let options = {
        margin: 1,
        html2canvas: {
            scale: 3,
            dpi: 300,
            letterRendering: true,
            width: 850,
            useCORS: true
        },
        jsPDF: {
            unit: 'mm',
            format: 'a4',
            orientation: 'portrait'
        },
        pagebreak: {
            mode: ['avoid-all', 'css']
        }
    }

    timestamp.innerText = Date.now().toString();
    targetURL.innerText = "<?php echo $targetUrl?>";

    window.onload = async function () {
        await html2pdf().set(options).from(makepdf).save();
        makepdf.style.display = "none";
        spinner.parentElement.innerHTML = "<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"50\" height=\"50\" fill=\"currentColor\" class=\"bi bi-check-lg\" viewBox=\"0 0 16 16\">\n" +
            "  <path d=\"M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425a.247.247 0 0 1 .02-.022Z\"/>\n" +
            "</svg>";
    };
</script>
</body>
</html>
