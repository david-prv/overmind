<?php

/**
 * Class App
 *
 * <p>
 * This class is responsible for rendering the app view
 * of this project.
 * </p>
 *
 * <p>
 * All necessary configurations can be made here.
 * No other file has to be changed.
 * </p>
 *
 * @author David Dewes <hello@david-dewes.de>
 */
class App
{
    private Core $core;
    private Pages $pages;
    private array $routes;

    /**
     * App constructor.
     */
    function __construct()
    {
        spl_autoload_register(Autoloader::getInstance()->getLoader());
        $this->core = Core::getInstance();
        $this->pages = Pages::getInstance();

        $this->registerPages();
        $this->registerRoutes();
    }

    /**
     * Manages all routes.
     *
     * <p>
     * Usually you don't need to modify the handles
     * array, except you know what you do (e.g adding
     * entirely new functionality to the framework).
     * </p>
     *
     * <p>
     * HowTo:
     * -    Add a new entry to the returned array (order does not matter)
     * -    Use the scheme: "page_name" => function() { ... }
     * -    For the handle function use $this->core, followed by all
     *      specifications you want to make
     * </p>
     */
    private function registerRoutes(): void
    {
        $this->routes = [
            "page" => function () {
                $this->core
                    ->withParams($_GET)
                    ->render();
            },
            "run" => function () {
                $this->core
                    ->withParams($_GET)
                    ->scan();
            },
            "upload" => function () {
                $this->core
                    ->withParams($_POST)
                    ->integrate();
            },
            "delete" => function () {
                $this->core
                    ->withParams($_GET)
                    ->delete();
            },
            "edit" => function () {
                $this->core
                    ->withParams($_GET)
                    ->edit();
            },
            "schedule" => function () {
                $this->core
                    ->withParams($_GET)
                    ->schedule();
            },
            "pdf" => function () {
                $this->core
                    ->withParams($_GET)
                    ->pdf();
            }
        ];
    }

    /**
     * Manages all pages
     *
     * <p>
     * Use this method to add new pages to the framework.
     * Please make sure, that the file in /views/, named by the page name
     * in lower-case letters, has to exist. Otherwise, the View cannot be
     * successfully rendered.
     * </p>
     *
     * <p>
     * HowTo:
     * -    Add a new line (order does not matter)
     * -    Write $this->add( ... );
     * -    Fill in all parameters, like name and placeholders
     * -    You can use the public programming interface from Core
     *      by using the local reference: $this->core->...
     * </p>
     */
    private function registerPages()
    {
        $this->pages->add("BASE", array(
            array("%TOOLS_LIST%", $this->core->renderToolsAsHtml()),
            array("%PROJECT_NAME%", $this->core->getProjectName()),
            array("%PROJECT_VERSION%", $this->core->getProjectVersion()),
            array("%PROJECT_AUTHOR%", $this->core->getProjectAuthor()),
            array("%PROJECT_DESCRIPTION%", $this->core->getProjectDescription()),
            array("%TOOLS_JSON%", $this->core->getToolsJson())
        ));

        $this->pages->add("SCHEDULE", array(
            array("%PROJECT_NAME%", $this->core->getProjectName()),
            array("%INTERACTIONS_LIST%", $this->core->renderScheduleAsHtml($this->core->getArg("edit"))),
            array("%ID%", $this->core->getArg("edit"))
        ));

        $this->pages->add("INTEGRATE", array(
            array("%PROJECT_NAME%", $this->core->getProjectName())
        ));

        $this->pages->add("TEST", array(
            array("%PROJECT_NAME%", $this->core->getProjectName())
        ));
    }

    /** @return closure */
    private function fallback(): closure
    {
        return function () {
            Core::getInstance()->render("base");
        };
    }

    /** @return closure */
    private function getHandle(): closure
    {
        $default = $this->fallback();

        $key = (count(array_keys($_GET)) >= 1) ? array_keys($_GET)[0] : -1;
        return array_key_exists($key, $this->routes) ? $this->routes[$key] : $default;
    }

    /** Run */
    public function run(): void
    {
        $_handle = $this->getHandle();
        $_handle();
    }
}