<?php

include_once "./app/Core.php";

/**
 * Class Main
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
class Main {

    function __construct()
    {
        if (isset($_GET["page"]))
            Core::getInstance()->withParams($_GET)->render();
        elseif (isset($_GET["run"]))
            Core::getInstance()->withParams($_GET)->scan();
        else
            Core::getInstance()->render("base");
    }

}

new Main();