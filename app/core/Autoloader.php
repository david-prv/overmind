<?php

/**
 * Class Autoloader
 *
 * <p>
 * This class implements a classical autoloader class,
 * inspired by the composer's implementation. It's mission is
 * to assemble all classes which are needed by the framework to run.
 * </p>
 *
 * @author David Dewes <hello@david-dewes.de>
 */
class Autoloader
{
    private static ?Autoloader $instance = NULL;
    private ?closure $loader;

    /**
     * Autoloader constructor.
     */
    private function __construct()
    {
        $this->loader = function($class_name) {
            if (file_exists("./app/core/" . "{$class_name}.php")) {
                include "./app/core/" . "{$class_name}.php";
            } else {
                include "./app/core/lib/" . "{$class_name}.php";
            }
        };
    }

    /**
     * Creates an Instance
     *
     * @return Autoloader
     */
    public static function getInstance(): Autoloader
    {
        if (self::$instance === NULL) {
            self::$instance = new Autoloader();
        }
        return self::$instance;
    }

    /**
     * Getter for loader closure
     *
     * @return closure
     */
    public function getLoader(): closure {
        return $this->loader;
    }
}