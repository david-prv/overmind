<?php

/**
 * Interface Runnable
 *
 * <p>
 * This interface designs the general look of a Runnable.
 * A Runnable is a class, which basically owns a run method and
 * returns the termination state in any way. For our purposes this will
 * happen via HTTP, since we are using JS for realtime runtime information.
 * </p>
 *
 * <p>
 * In our case, a Runnable contains some sort of building methods.
 * These methods define what specific knowledge the python runner needs
 * about the now running scanner. All methods return Scanner to make all
 * methods available in a call chain.
 * </p>
 *
 * @author David Dewes <hello@david-dewes.de>
 */
interface Runnable
{
    /**
     * Defines the running engine used
     * as interpreter by the python runner
     *
     * @param $engine
     * @return Scanner
     */
    public function viaEngine(string $engine): Scanner;

    /**
     * Defines the current working directory
     * (or short CWD) for the execution
     *
     * @param $cwd
     * @return Scanner
     */
    public function useCWD(string $cwd): Scanner;

    /**
     * Defines, after the CWD, where exactly
     * the tool is located in the projects structure
     *
     * @param $appPath
     * @return Scanner
     */
    public function atPath(string $appPath): Scanner;

    /**
     * Defines which start-up arguments will be
     * used by the python runner
     *
     * @param $cmdLineString
     * @return Scanner
     */
    public function withArguments(string $cmdLineString): Scanner;

    /**
     * Defines the scanner application id which
     * then will be used to identify the result report
     * later on
     *
     * @param $id
     * @return Scanner
     */
    public function identifiedBy(string $id): Scanner;

    /**
     * Runs the python runner and sends the final
     * result as HTTP response. This is because the javascript
     * runtime script needs to know the termination status
     *
     * @return bool
     */
    public function run(): bool;
}