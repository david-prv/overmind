<?php

/**
 * Class Scanner
 *
 * <p>
 * Implements a Runnable and Integrable. A Scanner is used to wrap
 * the python runner in an abstract PHP based model we can
 * extend and work with.
 * </p>
 *
 * @author David Dewes <hello@david-dewes.de>
 */
class Scanner implements Runnable, Integrable
{
    private string $engine;
    private string $path;
    private string $cmdline;
    private string $id;
    private string $cwd;
    private string $target;

    private string $name;
    private string $creator;
    private string $creatorURL;
    private string $description;
    private string $version;
    private string $keywords;
    private string $reference;
    private array $interactions;
    private array $fileData;

    //////////////////////
    // RUNNABLE METHODS //
    //////////////////////

    /**
     * Actual execution with respect to
     * errors and timeouts, which may occur
     *
     * @param string $cmd
     * @param int $timeout
     * @return ScanResult
     */
    private function runWithTimeout(string $cmd, int $timeout): ScanResult
    {
        $st = microtime(true);

        $retVal = shell_exec(escapeshellcmd($cmd));

        $et = microtime(true);
        $dt = $et - $st;

        if ($retVal === false) return new ScanResult(ScanResult::RESULT_ERROR, false);
        if ((int)$dt >= $timeout) return new ScanResult(ScanResult::RESULT_TIMEOUT, $retVal);
        return new ScanResult(ScanResult::RESULT_OK, $retVal);
    }

    /**
     * Defines the target
     *
     * @param string $url
     * @return Runnable
     */
    public function target(string $url): Runnable
    {
        $this->target = $url;
        return $this;
    }

    /**
     * Defines the running engine used
     * as interpreter by the python runner
     *
     * @param string $engine
     * @return Scanner
     */
    public function viaEngine(string $engine): Scanner
    {
        $this->engine = $engine;
        return $this;
    }

    /**
     * Defines the current working directory
     * (or short CWD) for the execution
     *
     * @param string $cwd
     * @return Scanner
     */
    public function useCWD(string $cwd): Scanner
    {
        $this->cwd = $cwd;
        return $this;
    }

    /**
     * Defines, after the CWD, where exactly
     * the tool is located in the projects structure
     *
     * @param string $appPath
     * @return Scanner
     */
    public function atPath(string $appPath): Scanner
    {
        $this->path = $appPath;
        return $this;
    }

    /**
     * Defines which start-up arguments will be
     * used by the python runner
     *
     * @param string $cmdLineString
     * @return Scanner
     */
    public function withArguments(string $cmdLineString): Scanner
    {
        $this->cmdline = $cmdLineString;
        return $this;
    }

    /**
     * Defines the scanner application id which
     * then will be used to identify the result report
     * later on
     *
     * @param string $id
     * @return Scanner
     */
    public function identifiedBy(string $id): Scanner
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Runs the python runner and sends the final
     * result as HTTP response. This is because the javascript
     * runtime script needs to know the termination status.
     * This is secure because no user input can be provided.
     *
     * Example: (Run from root dir)
     * python3 ./app/tools/interactive.py python3
     * C:\Users\david\PhpstormProjects\scanner-bundle\scanner-bundle/app/tools/InteractiveTest/app.py
     * "https://example.com" 3 https://example.com
     *
     * @param int $timeout
     * @return bool
     */
    public function run(int $timeout = 20): bool
    {
        if (Schedule::isPresent($this->cwd, $this->id)) {
            return ($this->runWithTimeout("python3 " . $this->cwd . "/app/tools/interactive.py " .
                $this->engine . " " . $this->cwd . "/app/tools/" . $this->path .
                " " . $this->cmdline . " " . $this->id . " " . $this->target, $timeout))->isOk();
        }
        return ($this->runWithTimeout("python3 " . $this->cwd . "/app/tools/runner.py " .
            $this->engine . " " . $this->cwd . "/app/tools/" . $this->path .
            " " . $this->cmdline . " " . $this->id . " " . $this->target, $timeout))->isOk();
    }

    ////////////////////////
    // INTEGRABLE METHODS //
    ////////////////////////

    /**
     * Defines the scanner name
     *
     * @param string $name
     * @return Scanner
     */
    public function hasName(string $name): Scanner
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Defines the creator's name
     *
     * @param string $creator
     * @return Scanner
     */
    public function fromCreator(string $creator): Scanner
    {
        $this->creator = $creator;
        return $this;
    }

    /**
     * Defines the creator's reference url
     *
     * @param string $url
     * @return Scanner
     */
    public function setCreatorURL(string $url): Scanner
    {
        $this->creatorURL = $url;
        return $this;
    }


    /**
     * Defines the tool description
     *
     * @param string $description
     * @return Scanner
     */
    public function describedBy(string $description): Scanner
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Defines the POST file data
     *
     * @param array $data
     * @return Scanner
     */
    public function fileData(array $data): Scanner
    {
        $this->fileData = $data;
        return $this;
    }

    /**
     * Defines the version the tool is currently in
     *
     * @param string $version
     * @return Scanner
     */
    public function inVersion(string $version): Scanner
    {
        $this->version = $version;
        return $this;
    }

    /**
     * Defines the order of interactions
     *
     * @param array $interactions
     * @return Scanner
     */
    public function withInteractions(array $interactions): Scanner
    {
        $this->interactions = $interactions;
        return $this;
    }

    /**
     * Defines by which keywords this tool can be found
     *
     * @param string $keywords
     * @return Scanner
     */
    public function searchKeywords(string $keywords): Scanner
    {
        $this->keywords = $keywords;
        return $this;
    }

    /**
     * Defines the reference used for risk
     * assessment later during the vulnerability scans
     *
     * @param string $reference
     * @return Integrable
     */
    public function withReference(string $reference): Integrable
    {
        $this->reference = $reference;
        return $this;
    }

    /**
     * Searches for a specific tool in a json object
     * and returns its index
     *
     * @param array $json
     * @param int $id
     * @return int
     */
    private function getToolIndexById(array $json, int $id): int
    {
        $index = 0;
        foreach ($json as $element) {
            if ((int)$element->id === (int)$id) {
                return $index;
            } else $index++;
        }
        return -1;
    }

    /**
     * Searches for a specific tool in a json object
     * and returns its currently saved path (relative to cwd)
     *
     * @param array $json
     * @param int $id
     * @return string
     */
    private function getToolPathById(array $json, int $id): string
    {
        foreach ($json as $element) {
            if ((int)$element->id === (int)$id) {
                $indexExplode = explode("/", (string)$element->index);
                if (!$indexExplode) return "";
                else return $indexExplode[0];
            }
        }
        return "";
    }

    /**
     * Deletes a folder and all of its subdirectories
     * and containing files
     *
     * @param $dir
     * @return bool
     */
    private function _deleteToolFolder($dir): bool
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir . "/" . $object) == "dir")
                        $this->_deleteToolFolder($dir . "/" . $object);
                    else unlink($dir . "/" . $object);
                }
            }
            return rmdir($dir);
        } else return false;
    }

    /**
     * Delete the (maybe existing) tool
     * references from the filesystem
     *
     * @param string $id
     * @return bool
     */
    private function _deleteToolReference(string $id): bool
    {
        $refPath = $this->cwd . "/../../refs/ref_$id.txt";

        if (!is_file($refPath)) return true;
        return unlink($refPath);
    }

    /**
     * Delete the (maybe existing) tool
     * reports from the filesystem
     *
     * @param string $id
     * @return bool
     */
    private function _deleteToolReport(string $id): bool
    {
        $refPath = $this->cwd . "/../../reports/report_$id.txt";

        if (!is_file($refPath)) return true;
        return unlink($refPath);
    }

    /**
     * Removes a specific tool from map.
     * Selection uses the tool's ID
     *
     * @param array $map
     * @param string $id
     * @return array
     */
    private function _deleteToolFromMap(array $map, string $id): array
    {
        $newMap = array();
        foreach ($map as $tool) {
            if ((string)$tool->id === (string)$id) continue;
            $newMap[] = $tool;
        }
        return $newMap;
    }

    /**
     * Triggers auto-install script for tool
     *
     * @param string $namespace
     * @param string $engine
     * @return void
     */
    private function _installToolDependencies(string $namespace, string $engine): void
    {
        $cmd = "python " . $this->cwd . "/app/tools/auto-install.py \"$namespace|$engine\"";
        shell_exec(escapeshellcmd($cmd));
    }

    /**
     * Performs the final integration.
     * Returns -1 for failed creation or the ID
     * if the integration was successful
     *
     * @return int
     */
    public function create(): int
    {
        $mapPath = $this->cwd . "/map.json";

        if (!file_exists($mapPath) || $_FILES["file"]["error"] !== 0 ||
            strpos($_FILES["file"]["name"], " ") !== false) {
            return false;
        }
        $currentMap = json_decode(file_get_contents($mapPath));
        $lastTool = end($currentMap);
        $fileExploded = explode(".", $this->fileData["file"]["name"]);
        $namespace = $fileExploded[0];
        $fileType = $fileExploded[1];

        if (strtolower($fileType) !== "zip") {
            return -1;
        }

        if (!$lastTool) {
            $newID = "0";
        } else {
            $newID = (string)((int)$lastTool->id + 1);
        }

        $newTool = array(
            "id" => $newID,
            "name" => $this->name,
            "engine" => Engine::valueOf($this->engine),
            "index" => $namespace . "/" . $this->path,
            "args" => $this->cmdline,
            "description" => $this->description,
            "version" => $this->version,
            "author" => $this->creator,
            "url" => $this->creatorURL,
            "keywords" => $this->keywords,
            "ignore" => false
        );

        $currentMap[] = $newTool;

        $targetFile = $this->cwd . "/" . basename($_FILES["file"]["name"]);
        if (move_uploaded_file($this->fileData["file"]["tmp_name"], $targetFile) === false) {
            return -1;
        }

        $zip = new ZipArchive();
        $res = $zip->open($this->cwd . "/" . basename($_FILES["file"]["name"]));
        if ($res === true) {
            if (mkdir($this->cwd . "/" . $namespace . "/", 0755, true) === false) {
                return -1;
            }
            $zip->extractTo($this->cwd . "/" . $namespace . "/");
            $zip->close();
            if (!unlink($this->cwd . "/" . basename($_FILES["file"]["name"]))) {
                return -1;
            }

            if (file_put_contents($mapPath, json_encode($currentMap)) === false) {
                return -1;
            }
        } else {
            return -1;
        }

        // TODO: try to install requirements automatically with auto-install.py

        $this->_installToolDependencies($namespace, Engine::valueOf($this->engine));

        return (int)$newID;
    }

    /**
     * Deletes the given tool/scanner from the
     * bundle using the given ID
     *
     * @return bool
     */
    public function delete(): bool
    {
        $mapPath = $this->cwd . "/map.json";

        if (!file_exists($mapPath)) {
            return false;
        }

        $currentMap = json_decode(file_get_contents($mapPath));

        $mapIndex = $this->getToolIndexById($currentMap, $this->id);
        if ($mapIndex === -1) return false;

        $namespace = $this->getToolPathById($currentMap, $this->id);
        if ($namespace === "" || !is_dir($this->cwd . "/" . $namespace)) return false;

        $workspace = $this->cwd . "/" . $namespace;
        $currentMap = $this->_deleteToolFromMap($currentMap, $this->id);

        return $this->_deleteToolFolder($workspace)
            && file_put_contents($mapPath, json_encode($currentMap)) !== false
            && $this->_deleteToolReference($this->id)
            && $this->_deleteToolReport($this->id)
            && Schedule::clear($this->cwd . "/../..", $this->id);
    }

    /**
     * Updates the given fields for the given
     * tool/scanner, which is identified by the ID
     *
     * @return bool
     */
    public function update(): bool
    {
        $mapPath = $this->cwd . "/map.json";

        if (!file_exists($mapPath)) {
            return false;
        }

        $newTool = array(
            "id" => $this->id,
            "name" => $this->name,
            "engine" => Engine::valueOf($this->engine),
            "index" => $this->path,
            "args" => $this->cmdline,
            "description" => $this->description,
            "version" => $this->version,
            "author" => $this->creator,
            "url" => $this->creatorURL,
            "keywords" => $this->keywords,
            "ignore" => false
        );

        $currentMap = json_decode(file_get_contents($mapPath));
        $mapIndex = $this->getToolIndexById($currentMap, $this->id);

        if ($mapIndex === -1) return false;

        $currentMap[$mapIndex] = $newTool;

        return file_put_contents($mapPath, json_encode($currentMap));
    }

    /**
     * Stores a new interaction schedule for the given
     * tool/scanner, which is identified by the ID
     *
     * @return bool
     */
    public function schedule(): bool
    {
        $schedulePath = $this->cwd . "/interactions.json";

        if (!file_exists($schedulePath) || !isset($this->interactions) || count($this->interactions) === 0) {
            return false;
        }

        return Schedule::put($this->cwd, $this->interactions, $this->id);
    }

    /**
     * Stores a reference report for the given
     * tool/scanner, which is identified by the ID
     *
     * @return bool
     */
    public function reference(): bool
    {
        $refPath = $this->cwd . "/../../refs";

        if (!is_dir($refPath) || !isset($this->reference) || !isset($this->id)) {
            return false;
        }

        return Reference::put($refPath, $this->id, $this->reference);
    }
}