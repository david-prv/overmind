<?php

/**
 * Class ExecResult
 *
 * <p>
 * This class stores the result of a scanner execution
 * in an abstract way. It contains a result token and the
 * concrete result of the executed application, as used in
 * Scanner.php.
 * </p>
 *
 * @author David Dewes <hello@david-dewes.de>
 */
class ExecResult
{
    /**
     * Constants for Result Tokens
     */
    const RESULT_OK = "SCANNER_RESULT_OK";
    const RESULT_TIMEOUT = "SCANNER_RESULT_TIMEOUT";
    const RESULT_ERROR = "SCANNER_RESULT_ERROR";

    /**
     * The return value of the executed
     * application
     *
     * @var bool|null
     */
    private ?bool $returnValue;

    /**
     * The result token
     *
     * @var string
     */
    private string $resultToken;

    public function __construct(string $resultToken, ?bool $returnValue)
    {
        $this->resultToken = $resultToken;
        $this->returnValue = $returnValue;
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
     * @return bool|null
     */
    public function returnValue(): ?bool
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
        return $this->resultToken === ExecResult::RESULT_OK;
    }

}