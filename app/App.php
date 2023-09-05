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
    /**
     * Keeps track of all displayable
     * HTML pages and their placeholders
     *
     * @var Pages
     */
    private Pages $pages;

    /**
     * Keeps track of all available logical operations
     * and manages how they will be handled, e.g. render a page
     *
     * @var Logics
     */
    private Logics $logics;

    /**
     * Method for closing HTTP requests by
     * printing an error indication message
     *
     * @param string $msg
     */
    public static function finishWithError(string $msg = "error"): void
    {
        die($msg);
    }

    /**
     * Method for closing HTTP requests by
     * printing a success indicating message
     *
     * @param string $msg
     */
    public static function finishWithSuccess(string $msg = "done"): void
    {
        die($msg);
    }

    /**
     * Method for closing HTTP requests by
     * redirecting to another page.
     *
     * E.g. finishWithRedirect("page=index&edit=2");
     * would redirect to INDEX view and passing the args
     * {'edit': 2} to it.
     *
     * @param string $logicalOperator
     */
    public static function finishWithRedirect(string $logicalOperator): void
    {
        header("Location: index.php?$logicalOperator");
        die();
    }

    /**
     * App constructor.
     */
    function __construct()
    {
        spl_autoload_register(Autoloader::getInstance()->getLoader());

        $this->pages = Pages::getInstance();
        $this->logics = Logics::getInstance();

        $this->registerLogics();
        $this->registerPages();
    }

    /**
     * Updates the parameters, which are passed to the
     * Core handle closure afterwards.
     *
     * @return void
     */
    private function updateParams(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === "GET") {
            Core::getInstance()->withParams($_GET);
        } else {
            Core::getInstance()->withParams($_POST);
        }
    }

    /**
     * Manages all logical operators.
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
     *
     * @return void
     */
    private function registerLogics(): void
    {
        // Updates internal parameters (GET or POST),
        // used by all application functions
        $this->updateParams();

        // Access a page (register them below)
        $this->logics->add("page", function () {
            Core::getInstance()->render();
        });

        // Background runner for scanners
        $this->logics->add("run", function () {
            Core::getInstance()->scan();
        });

        // Background endpoint for tool upload
        $this->logics->add("upload", function () {
            Core::getInstance()->integrate();
        });

        // Background endpoint for reference creation
        $this->logics->add("reference", function () {
            Core::getInstance()->reference();
        });

        // Background endpoint for report analysis
        $this->logics->add("analyze", function () {
            Core::getInstance()->analyze();
        });

        // Background endpoint for tool removal
        $this->logics->add("delete", function () {
            Core::getInstance()->delete();
        });

        // Background endpoint for tool edition
        $this->logics->add("edit", function () {
            Core::getInstance()->edit();
        });

        // Background endpoint for interaction schedules
        $this->logics->add("schedule", function () {
            Core::getInstance()->schedule();
        });

        // Background endpoint for snapshot creation
        $this->logics->add("snapshot", function () {
            Core::getInstance()->snapshot();
        });
    }

    /**
     * Manages all pages
     *
     * <p>
     * Use this method to add new pages to the framework.
     * Please make sure, that the file in /views/, named by the page name
     * in lower-case letters, has to exist. Otherwise, the Page cannot be
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
     *
     * @return void
     */
    private function registerPages(): void
    {
        /* Index page */
        $this->pages->add("BASE", array(
            array("%TOOLS_LIST%", Core::getInstance()->renderToolsAsHtml()),
            array("%PROJECT_NAME%", Core::getInstance()->getProjectName()),
            array("%PROJECT_VERSION%", Core::getInstance()->getProjectVersion()),
            array("%PROJECT_LOGO%", Core::getInstance()->getProjectLogo()),
            array("%PROJECT_AUTHOR%", Core::getInstance()->getProjectAuthor()),
            array("%PROJECT_DESCRIPTION%", Core::getInstance()->getProjectDescription()),
            array("%TOOLS_JSON%", Core::getInstance()->getToolsJson()),
            array("%CURRENT_FINGER_PRINT%", Reference::getFingerPrint()),
            array("%PERSONAL_TOKEN%", Reference::getPersonalToken())
        ));

        /* Interactive input schedule */
        $this->pages->add("SCHEDULE", array(
            array("%PROJECT_NAME%", Core::getInstance()->getProjectName()),
            array("%INTERACTIONS_LIST%", Core::getInstance()->renderScheduleAsHtml(Core::getInstance()->getArg("edit"))),
            array("%ID%", Core::getInstance()->getArg("edit")),
            array("%SKIP_REDIRECT%", Core::getInstance()->isArgPresent("noref") ? "true" : "false")
        ));

        /* Integration of a new tool */
        $this->pages->add("INTEGRATE", array(
            array("%PROJECT_NAME%", Core::getInstance()->getProjectName())
        ));

        /* Creation of the reference report */
        $this->pages->add("REFERENCE", array(
            array("%PROJECT_NAME%", Core::getInstance()->getProjectName()),
            array("%TOOLS_JSON%", Core::getInstance()->getToolsJson()),
            array("%ID%", Core::getInstance()->getArg("edit"))
        ));

        /* Just a test page */
        $this->pages->add("TEST", array(
            array("%PROJECT_NAME%", Core::getInstance()->getProjectName())
        ));
    }

    /** @return closure */
    private function getHandle(): closure
    {
        $default = $this->logics->default();

        $key = (count(array_keys($_GET)) >= 1) ? array_keys($_GET)[0] : -1;
        return $this->logics->get(strtolower($key)) ?? $default;
    }

    /** Run */
    public function run(): void
    {
        $handle = $this->getHandle();
        $handle();
    }
}