<?php

/**
 * Class Core
 *
 * This file is responsible for parsing the tools,
 * preparing them to show in the frontend, verifying their
 * integrity and finally issuing scan reports for the user.
 *
 * @author David Dewes <hello@david-dewes.de>
 */
class Core {

    private static $instance = null;

    private $argv;
    private $TOOLS_OBJECT;

    private $APP_PATH;
    private $VIEW_PATH;
    private $TOOLS_PATH;

    private $PROJECT_NAME = "WP Scanner Bundle";
    private $PROJECT_AUTHOR = "David Dewes";
    private $PROJECT_VERSION = "1.0.0";
    private $PROJECT_DESCRIPTION =
        "A small collection of open-source tools out there to " .
        "inspect and scan any kind of wordpress page.";

    /**
     * Private Constructor
     *
     * @param null $tp
     * @param null $tip
     */
    private function __construct($tp = null, $tip = null) {
        $this->APP_PATH = getcwd();
        $this->VIEW_PATH = $this->APP_PATH."/app/templates";
        $this->TOOLS_PATH = ($tp === null) ? $this->APP_PATH."/app/tools" : $tp;
        $this->TOOLS_OBJECT = json_decode(file_get_contents(($tip === null) ? $this->APP_PATH."/app/tools/map.json" : $tip), false);
    }

    /**
     * Creates an Instance
     *
     * @return Core|null
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Core();
        }
        return self::$instance;
    }

    /**
     * Sets the argv attribute and thus
     * allows the Core to use parameters passed as
     * GET or POST params
     *
     * @param $params
     * @return null
     */
    public function withParams($params) {
        $this->argv = $params;
        return self::$instance;
    }

    /**
     * Renders a html template view and replaces
     * a list of placeholders with given values
     *
     * @param $view
     */
    public function render($view = null) {
        if ($this->argv !== null && $view === null) {
            $view = $this->argv["page"];
        }

        switch(strtoupper($view)) {
            case 'BASE':
                $FILENAME = "base.htm";
                $PLACEHOLDERS = array(
                    array("%TOOLS_LIST%", $this->renderTools()),
                    array("%PROJECT_NAME%", $this->getProjectName()),
                    array("%PROJECT_VERSION%", $this->getProjectVersion()),
                    array("%PROJECT_AUTHOR%", $this->getProjectAuthor()),
                    array("%PROJECT_DESCRIPTION%", $this->getProjectDescription())
                );
                break;
            default:
                throw new \http\Exception\InvalidArgumentException();
                break;
        }

        $html = file_get_contents($this->VIEW_PATH . "/" . $FILENAME);

        foreach($PLACEHOLDERS as $key => $value) {
            $html = str_replace($value[0], $value[1], $html);
        }

        echo $html;
    }

    /**
     * Getter for tools object
     *
     * @return mixed
     */
    private function getToolsObject() {
        return $this->TOOLS_OBJECT;
    }

    /**
     * Getter for project author
     *
     * @return string
     */
    private function getProjectAuthor() {
        return $this->PROJECT_AUTHOR;
    }

    /**
     * Getter for project name
     *
     * @return string
     */
    private function getProjectName() {
        return $this->PROJECT_NAME;
    }

    /**
     * Getter for project version
     *
     * @return string
     */
    private function getProjectVersion() {
        return $this->PROJECT_VERSION;
    }

    /**
     * Getter for project description
     *
     * @return string
     */
    private function getProjectDescription() {
        return $this->PROJECT_DESCRIPTION;
    }

    /**
     * Renders tools object to html
     *
     * @return mixed
     */
    private function renderTools() {
        $html = "";
        foreach($this->getToolsObject() as $tool) {
            $html .= "<div class=\"list-group-item list-group-item-action\" aria-current=\"true\">
            <div class=\"d-flex w-100 justify-content-between\">
                <h5 class=\"mb-1\">$tool->name <span class=\"badge rounded-pill bg-secondary\">$tool->engine</span></h5>
                <small id='state-$tool->id' class='fst-italic'>Idling...</small>
            </div>
            <p class=\"mb-1\">$tool->description</p>
            <small>Author: <a href=''>$tool->author</a></small>
        </div>";
        }
        return $html;
    }
}
