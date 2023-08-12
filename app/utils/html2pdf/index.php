<?php
    // read passed data...

    if(!isset($_GET["data"])) die("[html2pdf] Invalid data!");

    // try to decode data...

    try {
        $data = json_decode($_GET["data"], true);
    } catch (Exception $ex) {
        die("[html2pdf] Whoops! Couldn't parse your data!");
    }

    // parsing data...

    if (!isset($data["target_url"]) || !isset($data["scanner_results"])
        || !isset($data["our_offers"]) || !isset($data["bad_words"]) || !isset($data["ref_token"])) {
        die("[html2pdf] Sorry, you provided the wrong data format!");
    }

    $targetUrl = $data["target_url"];
    $scannerResults = $data["scanner_results"];
    $ourOffers = $data["our_offers"];
    $badWords = $data["bad_words"];
    $refToken = $data["ref_token"];
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>

    <!-- General Style -->
    <style>
        body {
            font-family: Arial;
            padding:20px;
        }

        .container {
            position: relative;
        }

        .card {
            box-sizing: content-box;
            width: 100%;
            height: 100%;
            padding: 30px;
            border: 1px solid black;
            background-color: #f0f0f0;
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
            font-family:'Courier New', Courier, monospace;
            margin-top:10px;
            margin-bottom: 0!important;
        }
    </style>

    <!-- Table Style -->
    <style type="text/css">
        .tg  {border-collapse:collapse;border-spacing:0;}
        .tg td{border-color:black;border-style:solid;border-width:1px;font-family:Arial, sans-serif;font-size:14px;overflow:hidden;padding:10px 5px;word-break:normal;}
        .tg th{border-color:black;border-style:solid;border-width:1px;font-family:Arial, sans-serif;font-size:14px;font-weight:normal;overflow:hidden;padding:10px 5px;word-break:normal;}
        .tg .tg-c3ow{border-color:inherit;text-align:center;vertical-align:top}
        .tg .tg-fymr{border-color:inherit;font-weight:bold;text-align:left;vertical-align:top}
        thead {background: #b5b5b5;}
    </style>
</head>

<body>
<div class="container">
    <center><button class="btn btn-primary btn-lg mb-2" id="button"><i class="fa fa-cogs"></i> Generate PDF</button></center>
    <div class="card" id="makepdf">
        <img class="logo" src="https://etage-4.de/etage4_wp/wp-content/uploads/2020/05/Logo_3000_dark.png" />

        <p class="imprint">
            <strong>Imprint</strong><br>
            This scan was created by:<br>
            ETAGE 4 UG (haftungsbeschr&auml;nkt)<br>
            Fl&auml;chenbachstra&#xDF;e 18<br>
            66606 Bliesen<br>
        </p>

        <h1>Risk Assessment</h1>

        &mdash;

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
                if(count($scannerResults) === 0) {
                    echo "<tr>
                            <td contenteditable class=\"tg-c3ow\">&mdash;</td>
                            <td contenteditable class=\"tg-c3ow\">&mdash;</td>
                            <td contenteditable class=\"tg-c3ow\">&mdash;</td>
                          </tr>";;
                } else {
                    foreach ($scannerResults as $result) {
                        echo "<tr>
                                <td contenteditable class=\"tg-c3ow\">{$result["testName"]}</td>
                                <td contenteditable class=\"tg-c3ow\">{$result["distance"]}</td>
                                <td contenteditable class=\"tg-c3ow\">{$result["normalized"]}%</td>
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
        <textarea rows="10" class="txt-comment">Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</textarea>

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
    let editStyle = {border: "1px solid black", backgroundColor: "#f0f0f0"};
    let blendStyle = {border: "none", backgroundColor: "#ffffff"};
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

    function blendPDFForPrint() {
        makepdf.style.border = blendStyle.border;
        makepdf.style.backgroundColor = blendStyle.backgroundColor;
    }

    function revertBlendForEdit() {
        makepdf.style.border = editStyle.border;
        makepdf.style.backgroundColor = editStyle.backgroundColor;
    }

    button.addEventListener("click", async function () {
        await blendPDFForPrint();
        await html2pdf().set(options).from(makepdf).save();
        await revertBlendForEdit();
    });
</script>
</body>
</html>
