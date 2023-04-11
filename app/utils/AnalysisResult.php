<?php

/**
 * Class AnalysisResult
 *
 * <p>
 * An analysis result is obtained by running the Analyzer
 * on a scan report of a tool. The Analyzer utilizes the StringComparator
 * to calculate the actual difference between a "good" reference report and
 * the actually obtained scanning result for the defined target URL. The Analyzer
 * then keeps track of that distance and decides which AnalysisResult should be thrown.
 * </p>
 *
 * <p>
 * AnalysisResults contain the following information:
 * - A result token
 * - The actual distance
 * - Some debug information
 * </p>
 *
 * @author David Dewes <hello@david-dewes.de>
 */
class AnalysisResult
{
    /**
     * Constants for Result Tokens
     */
    const RESULT_OK = "ANALYZER_RESULT_OK";
    const RESULT_ERROR = "ANALYZER_RESULT_ERROR";

    /**
     * The result token
     *
     * @var string
     */
    private string $resultToken;

    /**
     * The obtained distance
     *
     * @var int
     */
    private int $returnValue;

    /**
     * The used comparator
     *
     * @var StringComparator
     */
    private StringComparator $comparator;

    public function __construct(string $resultToken, StringComparator $comparator)
    {
        $this->comparator = $comparator;
        $this->returnValue = $comparator->getDistance();
        $this->resultToken = $resultToken;
    }

    /**
     * Returns the result token
     *
     * @return string
     */
    public function resultToken(): string
    {
        return $this->resultToken;
    }

    /**
     * Returns the returned value
     *
     * @return int
     */
    public function returnValue(): int
    {
        return $this->returnValue;
    }

    /**
     * Checks whether the result token is OK
     *
     * @return bool
     */
    public function isOk(): bool
    {
        return $this->resultToken === self::RESULT_OK;
    }
}