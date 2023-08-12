<?php

/**
 * Function to write to log that is being
 * display after execution of the PHP code,
 * documenting what happened during the pre-processing step
 *
 * @param string $msg
 * @param int $level
 * @return void
 */
function writeLog(string $msg, int $level = 0)
{
    global $logToPrint;

    $prefix = "<span style='color:cornflowerblue;'>[INFO]</span>";

    switch ($level) {
        case 1:
            $prefix = "<span style='color:darkorange;'>[WARN]</span>";
            break;
        case 2:
            $prefix = "<span style='color:red;'>[ERROR]</span>";
            break;
        case 3:
            $prefix = "<span style='color:darkred;font-weight:bolder;'>[CRITICAL]</span>";
            break;
        default:
            break;
    }
    $logToPrint .= "$prefix $msg <br/>";
}

/**
 * Unzips ZIP-Archive
 *
 * @param string $file
 * @param string $extractTo
 * @return bool
 */
function unzipArchive(string $file, string $extractTo): bool
{
    $zip = new ZipArchive();
    $res = $zip->open($file);
    if ($res) {
        $zip->extractTo($extractTo);
        $zip->close();
        return unlink($file);
    } else {
        return false;
    }
}

/**
 * Read integration file details
 *
 * @param string $tmpFolder
 * @param string $name
 * @return string
 */
function readHiddenInfoFile(string $tmpFolder, string $name): string
{
    if (!file_exists("$tmpFolder/.$name")) return "";
    return trim(file_get_contents("$tmpFolder/.$name"));
}

/**
 * Read tool info from integration file
 *
 * @param string $toolDir
 * @param string $toolName
 * @param string $name
 * @return string
 */
function readToolInfo(string $toolDir, string $toolName, string $name): string
{
    if (!file_exists("$toolDir/$toolName.$name")) return "";
    return trim(file_get_contents("$toolDir/$toolName.$name"));
}

/**
 * Checks if tool's zip exists
 *
 * @param string $toolZip
 * @return bool
 */
function checkForToolZip(string $toolZip): bool
{
    return file_exists($toolZip);
}

/**
 * Enumerates all provided tools which should
 * be integrated to the framework
 *
 * @param string $tmpFolder
 * @return array
 */
function enumerateTools(string $tmpFolder): array
{
    $scanResult = scandir($tmpFolder . "_tools/");
    if (!$scanResult) return array();

    $tools = array();
    foreach ($scanResult as $result) {
        if (is_dir($tmpFolder . "_tools/" . $result) && $result !== "." && $result !== "..") {
            $tools[] = $tmpFolder . "_tools/" . $result;
        }
    }
    return $tools;
}

function cleanUpTemporaryFiles(string $tmpFolder, string $root): bool
{
    foreach (scandir($tmpFolder) as $object) {
        if ($object !== "." && $object !== ".." && $object !== ".gitkeep") {
            writeLog("Cleaning up... $object");
            if (filetype($tmpFolder . "/" . $object) == "dir")
                cleanUpTemporaryFiles($tmpFolder . "/" . $object, $root);
            else unlink($tmpFolder . "/" . $object);
        }
    }
    if ($tmpFolder !== $root) return rmdir($tmpFolder);
    else return true;
}

/**
 * The "main" function for tool integration
 *
 * @param string $tmpFolder
 * @return bool
 */
function initIntegration(string $tmpFolder): bool
{
    $uploadEndpoint = "index.php?integrate"; // post data required
    $scheduleEndpoint = "index.php?schedule"; // get data id & interactions required
    $referenceEndpoint = "index.php?reference"; // get data id & reference (base64) required

    $author = readHiddenInfoFile($tmpFolder, "author");
    $info = readHiddenInfoFile($tmpFolder, "info");

    if ($author === "" || $info === "") return false;

    writeLog("Integration file was created by $author");
    writeLog("Description: $info");

    $enumeration = enumerateTools($tmpFolder);
    if (count($enumeration) === 0) {
        writeLog("Provided file does not contain any tools", 2);
        return false;
    }

    writeLog("Found " . count($enumeration) . " tool(s) to integrate");

    foreach ($enumeration as $tool) {
        $array = explode("/", $tool);
        $toolName = end($array);
        $toolZip = "$tool/$toolName.zip";
        $toolIsInteractive = true;

        writeLog("Integrating tool: '$toolName'...");

        $toolInfo = readToolInfo($tool, $toolName, "info");
        $toolReference = readToolInfo($tool, $toolName, "reference");
        $toolSchedule = readToolInfo($tool, $toolName, "schedule");

        if (!checkForToolZip($toolZip)) {
            writeLog("Tool '$toolName' is missing the corresponding zip archive $toolName.zip! Skipped.", 2);
            continue;
        }

        if ($toolInfo === "" || $toolReference === "") {
            writeLog("Tool '$toolName' does not contain all necessary information (info, reference)! Skipped.", 2);
            continue;
        }

        if ($toolSchedule === "") {
            writeLog("Tool '$toolName' is not interactive!");
            $toolIsInteractive = false;
        }

        if ($toolIsInteractive) {
            writeLog("Tool '$toolName' is interactive, parsing schedule...");
            $toolSchedule = array_map(function (string $input) {
                return trim($input);
            }, explode("\n", $toolSchedule));
            writeLog("Schedule: " . json_encode($toolSchedule));
        }

        writeLog("Parsing tool information...");

        $toolInfo = array_map(function (string $info) {
            return trim($info);
        }, explode("\n", $toolInfo));

        $providedFields = count($toolInfo);
        if ($providedFields !== 9) {
            writeLog("Tool '$toolName' is missing important information. There are only $providedFields, should be 9! Skipped.", 2);
        }

        $_name = $toolInfo[0];
        $_author = $toolInfo[1];
        $_url = $toolInfo[2];
        $_version = $toolInfo[3];
        $_engine = $toolInfo[4];
        $_index = $toolInfo[5];
        $_cmdLine = $toolInfo[6];
        $_description = $toolInfo[7];
        $_keywords = $toolInfo[8];

        writeLog("Parsed all tool details: " . json_encode([$_name, $_author, $_url, $_version, $_engine,
                $_index, $_cmdLine, $_description, $_keywords]));
    }

    return cleanUpTemporaryFiles($tmpFolder, $tmpFolder);
}