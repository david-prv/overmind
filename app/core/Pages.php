<?php

/**
 * Class Pages
 *
 * <p>
 * This class is here to serve as a "View" Manager.
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
    private array $dependencies;
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
     * @param bool $usesJS
     * @param bool $noMobile
     */
    public function add(string $name, array $placeholders, bool $usesJS = true, bool $noMobile = false): void
    {
        $this->placeholders[strtoupper($name)] = $placeholders;
        $this->pages[] = strtoupper($name);
        $this->dependencies[strtoupper($name)] = [
            'js' => $usesJS,
            'mobile' => $noMobile
        ];
    }

    /**
     * Public getter for the views
     * (the only publicly exposed method)
     *
     * @param string $name
     * @return Page
     */
    public function get(string $name): Page
    {
        return in_array(strtoupper($name), $this->pages)
            ? (((new Page($this->viewPath, $this->dependencies[strtoupper($name)]["js"]))
                ->setTemplate(strtolower($name)))
                ->setPlaceholders($this->placeholders[strtoupper($name)]))
            : ((new Page($this->viewPath))
                ->setError(true));
    }
}