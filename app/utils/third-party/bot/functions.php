<?php

/** @var $skippedTools */
$skippedTools = [];

/** @var $lastError */
$lastError = "";

// -------------------- [ AUXILIARY FUNCTIONS ] --------------------

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
    global $logToPrint, $lastError;

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
    if ($level > 1) $lastError = $msg;
    $logToPrint .= "$prefix $msg <br/>";
    if ($level === 3) _summarize();
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
        if (!is_dir($extractTo)) {
            if (!mkdir($extractTo)) return false;
        }
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

/**
 * Final cleanup of temporary working directory
 *
 * @param string $tmpFolder
 * @param string $root
 * @return bool
 */
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

// -------------------- [ INTEGRATION STEPS ] --------------------

/**
 * The "main" function for tool integration
 *
 * @param string $tmpFolder
 * @return bool
 */
function doIntegration(string $tmpFolder): bool
{
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

    $toolsToIntegrate = [];
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
            _skipped($toolName);
            continue;
        }

        if ($toolInfo === "" || $toolReference === "") {
            writeLog("Tool '$toolName' does not contain all necessary information (info, reference)! Skipped.", 2);
            _skipped($toolName);
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
            _skipped($toolName);
            continue;
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

        writeLog("Parsed all tool details: <a style=\"cursor:pointer;text-decoration:underline;\"
                       onclick=\"alert('" . htmlentities(json_encode([$_name, $_author, $_url, $_version, $_engine,
                $_index, $_cmdLine, $_description, $_keywords])) . "')\">show</a>");

        // assemble info
        $toolData = array(
            "name" => $_name,
            "author" => $_author,
            "url" => $_url,
            "version" => $_version,
            "engine" => $_engine,
            "index" => $_index,
            "cmdline" => $_cmdLine,
            "description" => $_description,
            "keywords" => $_keywords,
            "file" => "$toolZip",
            "reference" => $toolReference,
            "schedule" => $toolSchedule,
            "interactive" => $toolIsInteractive
        );

        $toolsToIntegrate[$_name] = $toolData;
    }

    return _integrateArray($toolsToIntegrate, "cleanUpTemporaryFiles", $tmpFolder, $tmpFolder) && _summarize();
}

/**
 * Run subsequent integration tasks after
 * parsing and checking the input
 *
 * @param array $tools
 * @param callable $callback
 * @param mixed ...$callbackArgs
 * @return bool
 */
function _integrateArray(array $tools, callable $callback, ...$callbackArgs): bool
{
    $installerArgs = [];

    foreach ($tools as $tool) {
        $namespace = $tool["name"];
        $tmp_namespace = explode("/", $tool["index"])[0];

        if ($tmp_namespace !== $namespace) {
            writeLog("Tool name '$namespace' did not match namespace of index. Correcting to '$tmp_namespace'...");
            $namespace = $tmp_namespace;
        }

        $finalDestination = __DIR__ . "/../../../tools/" . $namespace;
        if (is_dir(realpath($finalDestination))) {
            writeLog("Detected duplication, this tool already exists! Skipped!", 2);
            _skipped($tool["name"]);
            continue;
        }

        // (1) unzip tool archive to ~/app/tools folder
        if (!unzipArchive($tool["file"], $finalDestination)) {
            writeLog("Could not unzip tool archive '" . $tool["name"] . "'! Skipped.", 2);
            _skipped($tool["name"]);
            continue;
        }

        writeLog("Unzipped tool archive '" . $tool["name"] . "' to: " . realpath($finalDestination));

        // (2) add tool data to map
        $toolID = _appendToMap($tool["name"], $tool["engine"], $tool["index"], $tool["cmdline"], $tool["description"],
            $tool["version"], $tool["author"], $tool["url"], $tool["keywords"]);

        if (is_null($toolID)) {
            writeLog("Could not append tool data to map! Skipped!", 2);
            _skipped($tool["name"]);
            continue;
        }

        writeLog("Successfully appended data to ~/app/tools/map.json");
        writeLog("Tool was assigned ID=$toolID");

        // (3) write tool reference
        if (!_writeReference($toolID, $tool["reference"])) {
            writeLog("Could not create and write to reference! Skipped!", 2);
            _skipped($tool["name"]);
            continue;
        }

        writeLog("Successfully wrote reference to ~/refs");

        // [ (4) if necessary, write schedule ]
        if ($tool["interactive"]) {
            if (!_writeSchedule($toolID, $tool["schedule"])) {
                writeLog("Could not register scheduled input! Skipped!", 2);
                _skipped($tool["name"]);
                continue;
            }
            writeLog("Successfully registered inputs to interaction mgr in ~/app/tools/interactions.json");
        }

        $installerArgs[] = $tool["name"] . "|" . $tool["engine"] .
            (($tool["name"] !== $namespace) ? "|$namespace" : "");
    }

    _runAutoInstaller($installerArgs);

    return call_user_func($callback, ...$callbackArgs);
}

/**
 * Calls the auto-installer.py script of the
 * scanner-bundle framework for automated and clean
 * dependency installation, if possible
 *
 * @param array $installerArgs
 * @return void
 */
function _runAutoInstaller(array $installerArgs): void
{
    writeLog("Initialized automated installation process... (len=" . count($installerArgs) . ")");
    $cmd = "python " . __DIR__ . "/../../../tools/auto-install.py " . __DIR__ . "/../../../tools/ ";

    foreach ($installerArgs as $arg) {
        $cmd .= "\"" . $arg . "\" ";
    }

    shell_exec($cmd);
}

/**
 * Write scheduled inputs to ~/app/tools/interactions.json
 *
 * @param string $id
 * @param array $scheduledInOrder
 * @return bool
 */
function _writeSchedule(string $id, array $scheduledInOrder): bool
{
    // read and verify schedule file
    $scheduleFile = __DIR__ . "/../../../tools/interactions.json";
    if (!file_exists($scheduleFile)) {
        writeLog("Could not find schedule file!", 2);
        return false;
    }
    $scheduleContent = file_get_contents(realpath($scheduleFile));
    if (!$scheduleContent) {
        writeLog("Could not read schedule file!", 2);
        return false;
    }
    $scheduleContent = json_decode($scheduleContent, true);
    if (is_null($scheduleContent)) {
        writeLog("Could not decode schedule content!", 2);
        return false;
    }

    writeLog("Successfully read schedule content!");

    // write new schedule to file
    $scheduleContent[$id] = $scheduledInOrder;

    if (!file_put_contents(realpath($scheduleFile), json_encode($scheduleContent))) {
        writeLog("Could not update schedule file!", 2);
        return false;
    }

    writeLog("Successfully updated schedule content!");

    return true;
}

/**
 * Write reference to ~/refs
 *
 * @param string $id
 * @param string $reference
 * @return bool
 */
function _writeReference(string $id, string $reference): bool
{
    // encode and fingerprint data
    $hashSum = hash('sha256', $reference . "." . Reference::getFingerPrint());
    $dataToStore = $reference . "|" . $hashSum;

    writeLog("Calculated integrity hash: <a style=\"cursor:pointer;text-decoration:underline;\" onclick=\"alert('$hashSum');\">show</a>");

    // write ref file
    $storeLocation = __DIR__ . "/../../../../refs/ref_$id.txt";
    if (file_exists($storeLocation)) {
        writeLog("Reference file already exists! This isn't a big deal, make sure to verify it is up-to-date!", 1);
    }
    if (!file_put_contents($storeLocation, $dataToStore)) {
        writeLog("Could not write to reference file!", 2);
        return false;
    }

    return true;
}

/**
 * Append tool data to ~/app/tools/map.json to
 * make it accessible to the framework. Also,
 * fetch and return the newly assigned toolID.
 * Returns NULL on error.
 *
 * @param string $name
 * @param string $engine
 * @param string $index
 * @param string $args
 * @param string $description
 * @param string $version
 * @param string $author
 * @param string $url
 * @param string $keywords
 * @param bool $ignore
 * @return int|null
 */
function _appendToMap(string $name, string $engine, string $index, string $args, string $description, string $version,
                      string $author, string $url, string $keywords, bool $ignore = false): ?int
{
    // read and verify mapper file
    $mapFile = __DIR__ . "/../../../tools/map.json";
    if (!file_exists($mapFile)) {
        writeLog("Could not find mapper file!", 2);
        return NULL;
    }
    $mapContent = file_get_contents(realpath($mapFile));
    if (!$mapContent) {
        writeLog("Could not read mapper file!", 2);
        return NULL;
    }
    $mapContent = json_decode($mapContent, true);
    if (is_null($mapContent)) {
        writeLog("Could not decode mapper content!", 2);
        return NULL;
    }

    writeLog("Successfully read mapper content. Continuing...");

    // fetch the latest ID (they are always increasing, but not necessarily
    // one-by-one comparable with their index)
    $latestItem = (count($mapContent) > 0) ? $mapContent[count($mapContent) - 1] : ["id" => -1];
    $latestID = $latestItem["id"];
    $newID = (int)$latestID + 1;

    writeLog("Last mapped tool has ID=$latestID. $name will be assigned ID=$newID");

    // append tool to mapper file
    $mapContent[] = [
        "id" => "$newID",
        "name" => $name,
        "engine" => $engine,
        "index" => $index,
        "args" => $args,
        "description" => $description,
        "version" => $version,
        "author" => $author,
        "url" => $url,
        "keywords" => $keywords,
        "ignore" => $ignore
    ];

    // write to mapper file
    if (!file_put_contents(realpath($mapFile), json_encode($mapContent))) {
        writeLog("Could not append $name to mapper file!", 2);
        return NULL;
    }

    return $newID;
}

/**
 * Keep track of skipped tools
 *
 * @param string $toolName
 * @param string|null $reason
 * @return void
 */
function _skipped(string $toolName, ?string $reason = NULL): void
{
    global $skippedTools, $lastError;
    $skippedTools[$toolName] = $reason ?? "Maybe because: " . $lastError;
}

/**
 * Give short summary at the end
 *
 * @return void
 */
function _summarize(): bool
{
    global $logToPrint, $skippedTools;

    $logToPrint .= "<hr />
                    <small>Integration Bot has stopped!</small><br />
                    <small>Timestamp: " . time() . "</small><br />
                    <small>Tools skipped: " . count($skippedTools) . "</small><br />
                    <small>Details: ";

    foreach ($skippedTools as $tool => $reason) {
        $logToPrint .= "<a style='cursor:pointer;text-decoration:underline;' title='click for details'
                        onclick='alert(\"$reason\")'>$tool</a> ";
    }

    if (count($skippedTools) === 0) $logToPrint .= "none";

    $logToPrint .= "</small><hr/>";
    return true;
}