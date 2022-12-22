<?php

/**
 * Class Pages
 *
 * <p>
 * This class is here to serve as a "Route" Manager.
 * It is very easy now to manage existing pages because of this class.
 * Simply use the "create()" method to add new pages and placeholders,
 * which should be replaced during rendering procedure.
 * </p>
 *
 * <p>
 * Do not confuse: This micro-framework does not support
 * any real routes. Pages are requested by GET parameters, like that:
 * index.php?page=PAGE_NAME
 * </p>
 *
 * @author David Dewes <hello@david-dewes.de>
 */
class Pages
{
    private array $pages;
    private array $placeholders;
    private string $viewPath;
    private Core $core;

    /**
     * Pages constructor.
     *
     * @param string $viewPath
     */
    public function __construct(string $viewPath)
    {
        $this->viewPath = $viewPath;
        $this->pages = array();
        $this->placeholders = array();
        $this->core = Core::getInstance();
        $this->create();
    }

    /**
     * Creates/registers a new page entity.
     *
     * <p>
     * Use this method to add new pages to the framework.
     * Please make sure, that the file in /views/, names by the page name
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
    private function create(): void
    {
        $this->add("BASE", array(
            array("%TOOLS_LIST%", $this->core->renderToolsAsHtml()),
            array("%PROJECT_NAME%", $this->core->getProjectName()),
            array("%PROJECT_VERSION%", $this->core->getProjectVersion()),
            array("%PROJECT_AUTHOR%", $this->core->getProjectAuthor()),
            array("%PROJECT_DESCRIPTION%", $this->core->getProjectDescription()),
            array("%TOOLS_JSON%", $this->core->getToolsJson())
        ));

        $this->add("SCHEDULE", array(
            array("%PROJECT_NAME%", $this->core->getProjectName()),
            array("%INTERACTIONS_LIST%", $this->core->renderScheduleAsHtml($this->core->getArg("edit"))),
            array("%ID%", $this->core->getArg("edit"))
        ));

        $this->add("INTEGRATE", array(
            array("%PROJECT_NAME%", $this->core->getProjectName())
        ));

        $this->add("TEST", array(
            array("%PROJECT_NAME%", $this->core->getProjectName())
        ));

    }

    /**
     * Adds a new page to local arrays
     *
     * @param string $name
     * @param array $placeholders
     */
    private function add(string $name, array $placeholders): void
    {
        $this->placeholders[strtoupper($name)] = $placeholders;
        array_push($this->pages, strtoupper($name));
    }

    /**
     * Public getter for the views
     *
     * @param string $name
     * @return View
     */
    public function get(string $name): View
    {
        return in_array(strtoupper($name), $this->pages)
            ? (((new View($this->viewPath))
                ->setTemplate(strtolower($name)))
                ->setPlaceholders($this->placeholders[strtoupper($name)]))
            : ((new View($this->viewPath))
                ->setError(true));
    }
}