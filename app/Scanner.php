<?php

/**
 * Class Scanner
 *
 * <p>
 * Implements a Runnable. A Scanner is used to wrap
 * the python runner in an abstract PHP based model we can
 * extend and work with. It can be seen as a String Builder for
 * the final shell_exec call.
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

    private string $name;
    private string $creator;
    private string $creatorURL;
    private string $description;
    private string $version;
    private array $fileData;

    // RUNNABLE METHODS

    /**
     * Defines the running engine used
     * as interpreter by the python runner
     *
     * @param string $engine
     * @return Scanner
     */
    public function viaEngine(string $engine): Scanner {
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
    public function useCWD(string $cwd): Scanner {
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
    public function atPath(string $appPath): Scanner {
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
    public function withArguments(string $cmdLineString): Scanner {
        $this->cmdline = $cmdLineString;
        return $this;
    }

    /**
     * Defines the scanner application id which
     * then will be used to identify the result report
     * later on
     *
     * @param string  $id
     * @return Scanner
     */
    public function identifiedBy(string $id): Scanner {
        $this->id = $id;
        return $this;
    }

    /**
     * Runs the python runner and sends the final
     * result as HTTP response. This is because the javascript
     * runtime script needs to know the termination status.
     * This is secure because no user input can be provided.
     *
     * @return bool
     */
    public function run(): bool {
        return shell_exec("python3 " . $this->cwd . "/app/tools/runner.py " .
            $this->engine . " " . $this->cwd . "/app/tools/" . $this->path .
            " " . $this->cmdline . " " . $this->id) !== NULL;
    }

    // INTEGRABLE METHODS

    /**
     * Defines the scanner name
     *
     * @param string $name
     * @return Scanner
     */
    public function hasName(string $name): Scanner {
        $this->name = $name;
        return $this;
    }

    /**
     * Defines the creator's name
     *
     * @param string $creator
     * @return Scanner
     */
    public function fromCreator(string $creator): Scanner {
        $this->creator = $creator;
        return $this;
    }

    /**
     * Defines the creator's reference url
     *
     * @param string $url
     * @return Scanner
     */
    public function setCreatorURL(string $url): Scanner {
        $this->creatorURL = $url;
        return $this;
    }


    /**
     * Defines the tool description
     *
     * @param string $description
     * @return Scanner
     */
    public function describedBy(string $description): Scanner {
        $this->description = $description;
        return $this;
    }

    /**
     * Defines the POST file data
     *
     * @param array $data
     * @return Scanner
     */
    public function fileData(array $data): Scanner {
        $this->fileData = $data;
        return $this;
    }

    /**
     * Defines the version the tool is currently in
     *
     * @param string $version
     * @return Scanner
     */
    public function inVersion(string $version): Scanner {
       $this->version = $version;
       return $this;
    }

    /**
     * Performs the final integration
     *
     * @return bool
     */
    public function integrate(): bool {
        // TODO implement this
        var_dump($this);
        return true;
    }
}