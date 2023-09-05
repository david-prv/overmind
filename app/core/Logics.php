<?php

/**
 * Class Logics
 *
 * <p>
 * This class is here to serve as a Logics Manager.
 * Logics are logical operations that the framework can perform
 * which are (mostly) of non-visual nature, e.g. updating data.
 * </p>
 *
 * <p>
 * Do not confuse: This micro-framework does not support
 * "routes" in classical meaning. Logical operations are triggered
 * by GET parameters which can look like follows:
 * localhost:8080/index.php?snapshot
 * </p>
 *
 * @author David Dewes <hello@david-dewes.de>
 */
class Logics
{
    private static ?Logics $instance = NULL;
    private array $logics;

    /**
     * Returns instance
     *
     * @return Logics
     */
    public static function getInstance(): Logics
    {
        if (self::$instance === NULL) {
            self::$instance = new Logics();
        }
        return self::$instance;
    }

    /** @return closure */
    public static function default(): closure
    {
        return function () {
            Core::getInstance()->render("base");
        };
    }

    /**
     * Adds a new logic
     *
     * @param string $name
     * @param closure $callback
     */
    public function add(string $name, closure $callback): void
    {
        $this->logics[strtolower($name)] = $callback;
    }

    /**
     * Public getter for a logical operator
     * (the only publicly exposed method)
     *
     * @param string $name
     * @return closure|NULL
     */
    public function get(string $name): ?closure
    {
        return $this->logics[strtolower($name)]
            ?? NULL;
    }
}