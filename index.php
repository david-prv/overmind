<?php

/**
 * Autoloader.
 */
spl_autoload_register(function ($class_name) {
    if (file_exists("{$class_name}.php")) {
        include "{$class_name}.php";
    } else {
        include "./app/" . "{$class_name}.php";
    }
});

/**
 * App Main
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

    public function load()
    {
        if (isset($_GET["page"]))
            Core::getInstance()->withParams($_GET)->render();
        elseif (isset($_GET["run"]))
            Core::getInstance()->withParams($_GET)->scan();
        else
            Core::getInstance()->render("base");
    }

}

$app = new App();
$app->load();
