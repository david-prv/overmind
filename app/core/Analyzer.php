<?php

/**
 * Class Analyzer
 *
 * <p>
 * The Analyzer class is responsible to collect all
 * relevant details from the generated reports of the bundle scanners.
 * The difficulty here is, that all tools have different reports,
 * without any standards. Thus, we needed to find some kind of expression
 * collection on which we can base our assumptions.
 * </p>
 *
 * <p>
 * A final analysis from this class will contain:
 * <ul>
 *      <li>the risk factor (value between 1-100)</li>
 *      <li>general information (wp version, title, ...)</li>
 *      <li>an overview of all found vulnerabilities</li>
 *      <li>an overview of all found information leakage</li>
 * </ul>
 * </p>
 *
 * @author David Dewes <hello@david-dewes.de>
 */
class Analyzer
{
    private static ?Analyzer $instance = NULL;

    private function __construct()
    {
    }

    public static function getInstance(): Analyzer
    {
        if (self::$instance === NULL) {
            self::$instance = new Analyzer();
        }
        return self::$instance;
    }

    public function prepare(string $id, string $actualResult): ?StringComparator
    {
        // 1. Read & decode reference
        // 2. Read actual result from report
        // 3. Prepare comparator

        // Can FAIL in any step --> return NULL
        return NULL;
    }

    public function analyze(string $id, StringComparator $comparator): ?AnalysisResult
    {
        // 1. Run comparator
        // 2. Return Analysis Result

        // Can FAIL in any step --> return NULL
        return NULL;
    }
}