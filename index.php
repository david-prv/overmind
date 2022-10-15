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
 * Index.php is considered to be the entry point of every
 * following dynamic action.
 * </p>
 *
 * @author David Dewes <hello@david-dewes.de>
 */
class App {

    /**
     * App constructor.
     */
    function __construct()
    {
        spl_autoload_register(function ($class_name) {
            if (file_exists("{$class_name}.php")) {
                include "{$class_name}.php";
            } else {
                include "./app/" . "{$class_name}.php";
            }
        });
    }

    /**
     * Loads application core
     */
    public function load(): void
    {
        if (isset($_GET["page"]))
            Core::getInstance()->withParams($_GET)->render();
        elseif (isset($_GET["run"]))
            Core::getInstance()->withParams($_GET)->scan();
        elseif (isset($_GET["upload"]))
            Core::getInstance()->withParams($_POST)->integrate();
        else
            Core::getInstance()->render("base");
    }
}

$app = new App();
$app->load();
