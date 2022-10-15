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
class Scanner implements Runnable
{
    private String $engine;
    private String $path;
    private String $cmdline;
    private String $id;
    private String $cwd;

    /**
     * Defines the running engine used
     * as interpreter by the python runner
     *
     * @param $engine
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
     * @param $cwd
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
     * @param $appPath
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
     * @param $cmdLineString
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
     * @param $id
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
}