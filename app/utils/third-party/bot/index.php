<?php
require_once "functions.php";

$logToPrint = "<h1>Bot Log</h1>";
$maxFileSize = 500000;
$uploadTargetDir = "/tmp";
$uploadFileName = "integrationFile";

$fileIsPresent = isset($_FILES[$uploadFileName]);
$uploadOK = true;

// init upload code, iff present
if ($fileIsPresent) {
    $target_dir = __DIR__ . $uploadTargetDir . "/";
    $target_file = $target_dir . basename($_FILES[$uploadFileName]["name"]);
    $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $fileSize = $_FILES[$uploadFileName]["size"];
    writeLog("Received file " . basename($_FILES[$uploadFileName]["name"]));

    // check if file exists
    if (file_exists($target_file)) {
        writeLog("File already exists as temporary file! Something went really wrong here.", 2);
        $uploadOK = false;
    }

    // check file type
    if ($fileType !== "zip") {
        writeLog("Provided file is not a zip-archive!", 2);
        $uploadOK = false;
    }

    // check file size
    if ($fileSize > $maxFileSize) {
        writeLog("Provided file exceeds maximum filesize!", 2);
        $uploadOK = false;
    }

    // actually upload temporary file
    if (!$uploadOK) {
        writeLog("Upload aborted!", 3);
    } else {
        if (move_uploaded_file($_FILES[$uploadFileName]["tmp_name"], $target_file)) {
            writeLog("File passed all checks, continuing...");

            if (!unzipArchive($target_file, $target_dir)) {
                writeLog("Could not unzip archive! Abort!", 3);
            } else {

                // init actual integration process
                (doIntegration($target_dir))
                    ? writeLog("Success! Tools were integrated successfully!")
                    : writeLog("Integration process aborted! For more details see information above.", 3);

            }

        } else {
            writeLog("Sorry, upload failed. There are two reasons: Your file's name was invalid or an unknown issue occurred! Aborted!", 3);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
<div <?php if($fileIsPresent) echo "style='height:100vh;overflow-y:scroll;'"; ?> class="container mt-3">
    <?php if (!$fileIsPresent) {
        echo '<div class="p-3">
            <h1>Integration Bot</h1>
            <div>
                <p style=" border-left: 4px solid blue;padding: 0.5em;">
                    Please select a valid import-file below. Make sure, that it follows the correct folder structure. When the
                    button is pressed, the integration procedure will start automatically and give you detailled debug information once it is done.
                    The bot cannot be stopped. There is no further warning. For extreme cases, backup your <code>/app/tools</code> folder.
                </p>
            </div>
            <form style="background:whitesmoke;padding:20px;" action="index.php" method="post" enctype="multipart/form-data">
                <input type="file" accept="application/zip" id="integrationFile" name="integrationFile">
                <button type="submit" class="btn btn-primary">Run Integration Bot</button>
            </form>
        </div>';
    } else {
        echo $logToPrint;
    } ?>
</div>
</body>

</html>
