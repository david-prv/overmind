<?php

/**
 * Class Pages
 *
 * <p>
 * This class is here to serve as a "Route" Manager.
 * It is very easy now to manage existing pages because of this class.
 * Simply use the "registerPages()" method in "App" to add new pages and placeholders,
 * which should be replaced during rendering procedure.
 * </p>
 *
 * @author David Dewes <hello@david-dewes.de>
 */
class Pages
{
    private static ?Pages $instance = NULL;
    private array $pages;
    private array $placeholders;
    private string $viewPath;

    /**
     * Pages constructor.
     */
    private function __construct()
    {
        $this->viewPath = getcwd() . "/app/views";
        $this->pages = array();
        $this->placeholders = array();
    }

    /**
     * Returns instance
     *
     * @return Pages
     */
    public static function getInstance(): Pages
    {
       if (self::$instance === NULL) {
           self::$instance = new Pages();
       }
       return self::$instance;
    }

    /**
     * Adds a new page to local arrays
     *
     * @param string $name
     * @param array $placeholders
     */
    public function add(string $name, array $placeholders): void
    {
        $this->placeholders[strtoupper($name)] = $placeholders;
        array_push($this->pages, strtoupper($name));
    }

    /**
     * Public getter for the views
     * (the only publicly exposed method)
     *
     * @param string $name
     * @return View
     */
    public function get(string $name): View
    {
        return in_array(strtoupper($name), $this->pages)
            ? (((new View($this->viewPath))
                ->setTemplate(strtolower($name)))
                ->setPlaceholders($this->placeholders[strtoupper($name)]))
            : ((new View($this->viewPath))
                ->setError(true));
    }
}