<?php

/**
 * Include all composer requirements
 */
require __DIR__ . '/vendor/autoload.php';

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
            } else if (file_exists("./app/" . "{$class_name}.php")) {
                include "./app/" . "{$class_name}.php";
            } else {
                include "./app/lib/" . "{$class_name}.php";
            }
        });
    }

    /**
     * This method is responsible for finding the correct
     * handle of the current situation. Depending on the provided
     * information from the URL, we can decide which scenario is currently
     * happening and with which configuration we should ignite our Core.
     *
     * @return closure
     */
    private function getHandle(): closure {
        if (isset($_GET["page"])) {
            /* Renders a view */
            return function() {
                Core::getInstance()->withParams($_GET)->render();
            };
        } else if (isset($_GET["run"])) {
            /* Background worker for running a scan */
            return function() {
                Core::getInstance()->withParams($_GET)->scan();
            };
        } else if (isset($_GET["upload"])) {
            /* Background worker for running a tool integration */
            return function() {
                Core::getInstance()->withParams($_POST)->integrate();
            };
        } else if (isset($_GET["delete"])) {
            /* Background worker for deleting a tool */
            return function() {
                Core::getInstance()->withParams($_GET)->delete();
            };
        } else if (isset($_GET["edit"])) {
            /* Background worker for updating a tool */
            return function () {
                Core::getInstance()->withParams($_GET)->edit();
            };
        } else if (isset($_GET["schedule"])) {
            /* Background worker for registering new interactions */
            return function() {
                Core::getInstance()->withParams($_GET)->schedule();
            };
        } else if (isset($_GET["pdf"])) {
            /* PDF file stream to output general result */
            return function() {
                Core::getInstance()->withParams($_GET)->pdf();
            };
        } else {
            /* Renders the basic/default view */
            return function() {
                Core::getInstance()->render("base");
            };
        }
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
