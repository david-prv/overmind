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
    private ?StringComparator $comparator = NULL;

    /**
     * Analyzer constructor.
     */
    private function __construct()
    {
    }

    /**
     * Returns an instance
     *
     * @return Analyzer
     */
    public static function getInstance(): Analyzer
    {
        if (self::$instance === NULL) {
            self::$instance = new Analyzer();
        }
        return self::$instance;
    }

    /**
     * Constructs a StringComparator
     *
     * @param string $cwd
     * @param string $id
     * @return $this|null
     */
    public function get(string $cwd, string $id): ?Analyzer
    {
        $refFile = $cwd . "/../../refs/ref_$id.txt";
        $reportFile = $cwd . "/../../reports/report_$id.txt";

        if (!is_file($refFile) || !is_file($reportFile)) return NULL;

        $_explode = explode("|", file_get_contents($refFile));
        $content = base64_decode($_explode[0]);

        if ($content === false || !Reference::checkIntegrity($cwd . "/../../refs", $id)) return NULL;

        $actualResult = file_get_contents($reportFile);
        if ($actualResult === false) return NULL;

        $this->comparator = new StringComparator($content, $actualResult);
        return $this;
    }

    /**
     * Analysis
     *
     * @return AnalysisResult
     */
    public function analyze(): AnalysisResult
    {
        if (is_null($this->comparator))
            return new AnalysisResult(AnalysisResult::RESULT_ERROR);

        $this->comparator->compare();

        $distance = $this->comparator->getDistance();
        $bWords = $this->comparator->getBadWords();

        if ($distance === PHP_INT_MAX) return new AnalysisResult(AnalysisResult::RESULT_ERROR);
        return new AnalysisResult(AnalysisResult::RESULT_OK, $distance, $bWords);
    }
}