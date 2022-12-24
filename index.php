<?php

/**
 * Include all composer requirements
 */
require __DIR__ . '/vendor/autoload.php';

/**
 * Include framework autoloader
 */
require __DIR__ . '/app/core/Autoloader.php';

/**
 * Class App
 *
 * <p>
 * This class is responsible for rendering the app view
 * of this project.
 * </p>
 *
 * <p>
 * Index.php is considered to be the entry point of every
 * following dynamic action.
 * </p>
 *
 * @author David Dewes <hello@david-dewes.de>
 */
class App
{
    /**
     * App constructor.
     */
    function __construct()
    {
        spl_autoload_register(Autoloader::getInstance()->getLoader());
    }

    /**
     * Manages all possible handles.
     *
     * DO NOT TOUCH!
     *
     * Usually you don't need to modify the handles
     * array, except you know what you do (e.g adding
     * entirely new functionality to the framework).
     *
     * @return array|Closure[]
     */
    private function allHandles(): array
    {
        return [
            "page" => function () {
                Core::getInstance()->withParams($_GET)->render();
            },
            "run" => function () {
                Core::getInstance()->withParams($_GET)->scan();
            },
            "upload" => function () {
                Core::getInstance()->withParams($_POST)->integrate();
            },
            "delete" => function () {
                Core::getInstance()->withParams($_GET)->delete();
            },
            "edit" => function () {
                Core::getInstance()->withParams($_GET)->edit();
            },
            "schedule" => function () {
                Core::getInstance()->withParams($_GET)->schedule();
            },
            "pdf" => function () {
                Core::getInstance()->withParams($_GET)->pdf();
            }
        ];
    }

    /**
     * Manages the default handle
     *
     * DO NOT TOUCH!
     *
     * Even if you know what you do:
     * The default handle should never be altered.
     * It defines the home page, which is, for this framework,
     * always the same.
     *
     * @return closure
     */
    private function defaultHandle(): closure
    {
        return function () {
            Core::getInstance()->render("base");
        };
    }

    /**
     * This method is responsible for finding the correct
     * handle of the current situation. Depending on the provided
     * information from the URL, we can decide which scenario is currently
     * happening and with which configuration we should ignite our Core.
     *
     * @return closure
     */
    private function getHandle(): closure
    {
        $handles = $this->allHandles();
        $default = $this->defaultHandle();

        $key = (count(array_keys($_GET)) >= 1) ? array_keys($_GET)[0] : -1;
        return array_key_exists($key, $handles) ? $handles[$key] : $default;
    }

    /**
     * Handles Core ignition
     */
    public function ignite(): void
    {
        ($this->getHandle())();
    }
}

$app = new App();
$app->ignite();