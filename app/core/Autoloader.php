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
     * This array contains all possible class locations
     * that can occur in this framework. In case you add any new
     * classes, which are not present yet, and also add a new folder
     * to the dir structure, please remember to adjust this array too.
     *
     * @var array|string[]
     */
    private static array $locations = ["./app/", "./app/core/", "./app/core/lib/"];

    /**
     * Autoloader constructor.
     */
    private function __construct()
    {
        $this->loader = function ($class_name) {
            foreach (Autoloader::$locations as $location) {
                $abs_path = $location . "{$class_name}.php";
                if (file_exists($abs_path)) {
                    include $abs_path . "";
                }
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
    public function getLoader(): closure
    {
        return $this->loader;
    }
}