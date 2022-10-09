<?php

/**
 * Autoloader
 *
 * This file is responsible for rendering the app view
 * of this project.
 *
 * Index.php is considered to be the entry point of every
 * following dynamic action.
 *
 * @author David Dewes <hello@david-dewes.de>
 */


include_once "./app/core.php";

if (isset($_GET["page"]))
    // process GET information
    Core::getInstance()->withParams($_GET)->render();
else
    // pre-defined render template
    Core::getInstance()->render("base");
