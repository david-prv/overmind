<?php

/**
 * Main
 *
 * This file is responsible for rendering the app view
 * of this project.
 *
 * Index.php is considered to be the entry point of every
 * following dynamic action.
 *
 * @author David Dewes <hello@david-dewes.de>
 */

include_once "./app/Core.php";

if (isset($_GET["page"]))
    Core::getInstance()->withParams($_GET)->render();
elseif (isset($_GET["run"]))
    Core::getInstance()->withParams($_GET)->scan();
else
    Core::getInstance()->render("base");
