<?php

/**
 * Class Routes
 *
 * <p>
 * This class is here to serve as a "Route" Manager.
 * It is very easy now to manage existing pages because of this class.
 * Simply use the "registerPages()" method in "App" to add new pages and placeholders,
 * which should be replaced during rendering procedure.
 * </p>
 *
 * <p>
 * Do not confuse: This micro-framework does not support
 * any "routes" in classical meaning. Pages are requested by GET parameters,
 * defining a "Route", which can look like follows:
 * localhost:8080/index.php?page=PAGE_NAME
 * </p>
 *
 * @author David Dewes <hello@david-dewes.de>
 */
class Routes
{
    private static ?Routes $instance = NULL;
    private array $routes;

    /**
     * Returns instance
     *
     * @return Routes
     */
    public static function getInstance(): Routes
    {
        if (self::$instance === NULL) {
            self::$instance = new Routes();
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
     * Adds a new route
     *
     * @param string $name
     * @param closure $callback
     */
    public function add(string $name, closure $callback): void
    {
        $this->routes[strtolower($name)] = $callback;
    }

    /**
     * Public getter for the routes
     * (the only publicly exposed method)
     *
     * @param string $name
     * @return closure|NULL
     */
    public function get(string $name): ?closure
    {
        return  $this->routes[strtolower($name)]
                ?? NULL;
    }
}