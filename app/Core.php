<?php

/**
 * Class Core
 *
 * <p>
 * This file is responsible for parsing the tools,
 * preparing them to show in the frontend, verifying their
 * integrity and finally issuing scan reports for the user.
 * </p>
 *
 * @author David Dewes <hello@david-dewes.de>
 */
class Core {

    private static ?Core $instance = NULL;

    private ?Array $argv = NULL;
    private $TOOLS_OBJECT;

    private string $APP_PATH;
    private string $VIEW_PATH;
    private string $TOOLS_PATH;

    private string $PROJECT_NAME = "WP Scanner Bundle";
    private string $PROJECT_AUTHOR = "David Dewes";
    private string $PROJECT_VERSION = "1.0.0";
    private string $PROJECT_DESCRIPTION =
        "A small collection of open-source tools out there to " .
        "inspect and scan any kind of wordpress page.";

    /**
     * Core constructor.
     *
     * @param NULL $tp
     * @param NULL $tip
     */
    private function __construct($tp = NULL, $tip = NULL) {
        $this->APP_PATH = getcwd();
        $this->VIEW_PATH = $this->APP_PATH."/app/templates";
        $this->TOOLS_PATH = ($tp === NULL) ? $this->APP_PATH."/app/tools" : $tp;
        $this->TOOLS_OBJECT = json_decode(file_get_contents(($tip === NULL) ? $this->APP_PATH."/app/tools/map.json" : $tip), false);

        foreach($this->TOOLS_OBJECT as $key => $value) {
            if ($value->ignore) unset($this->TOOLS_OBJECT[$key]);
        }
    }

    /**
     * Creates an Instance
     *
     * @return Core
     */
    public static function getInstance(): Core {
        if (self::$instance === NULL) {
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
     * @return Core
     */
    public function withParams($params): Core {
        $this->argv = $params;
        return self::$instance;
    }

    /**
     * Renders a html template view and replaces
     * a list of placeholders with given values
     *
     * @param $view
     * @return void
     */
    public function render($view = NULL): void {
        if ($this->argv !== NULL && $view === NULL) {
            $view = $this->argv["page"];
        }

        $viewObj = new View($this->VIEW_PATH);

        switch(strtoupper($view)) {
            case 'BASE':
                $viewObj->setTemplate(strtolower($view));
                $placeholders = array(
                    array("%TOOLS_LIST%", $this->renderToolsAsHtml()),
                    array("%PROJECT_NAME%", $this->getProjectName()),
                    array("%PROJECT_VERSION%", $this->getProjectVersion()),
                    array("%PROJECT_AUTHOR%", $this->getProjectAuthor()),
                    array("%PROJECT_DESCRIPTION%", $this->getProjectDescription()),
                    array("%TOOLS_JSON%", $this->getToolsJson())
                );
                $viewObj->setPlaceholders($placeholders);
                break;
            case 'TEST':
                $viewObj->setTemplate(strtolower($view));
                $placeholders = array(
                    array("%PROJECT_NAME%", $this->getProjectName())
                );
                $viewObj->setPlaceholders($placeholders);
                break;
            default:
                echo "Not found";
                $viewObj->setError(true);
                break;
        }

        View::render($viewObj);
    }

    /**
     * Builds the Runnable and executes it
     *
     * @return void
     */
    public function scan(): void {
        if ($this->argv === NULL) {
            echo "no arguments provided";
            return;
        }

        $engine = (isset($this->argv["engine"])) ? $this->argv["engine"] : NULL;
        $app = (isset($this->argv["index"])) ? $this->argv["index"] : NULL;
        $args = (isset($this->argv["args"])) ? $this->argv["args"] : NULL;
        $id = (isset($this->argv["id"])) ? $this->argv["id"] : NULL;

        if (is_null($engine) || is_null($app) || is_null($args) || is_null($id)) {
            echo "invalid arguments or incomplete arg set";
            return;
        }

        $runner = (new Scanner())
            ->viaEngine(Engine::fromString($engine))
            ->useCWD($this->APP_PATH)
            ->atPath($app)
            ->withArguments($args)
            ->identifiedBy($id);

        if ($runner->run()) echo "done";
        else echo "error";

    }

    /**
     * Getter for tools object
     *
     * @return array
     */
    private function getToolsObject(): array {
        return $this->TOOLS_OBJECT;
    }

    /**
     * Getter for tools object json encoded
     *
     * @return string
     */
    private function getToolsJson(): string {
        return json_encode($this->TOOLS_OBJECT);
    }

    /**
     * Getter for project author
     *
     * @return string
     */
    private function getProjectAuthor(): string {
        return $this->PROJECT_AUTHOR;
    }

    /**
     * Getter for project name
     *
     * @return string
     */
    private function getProjectName(): string {
        return $this->PROJECT_NAME;
    }

    /**
     * Getter for project version
     *
     * @return string
     */
    private function getProjectVersion(): string {
        return $this->PROJECT_VERSION;
    }

    /**
     * Getter for project description
     *
     * @return string
     */
    private function getProjectDescription(): string {
        return $this->PROJECT_DESCRIPTION;
    }

    /**
     * Renders tools object to html
     *
     * @return string
     */
    private function renderToolsAsHtml(): string {
        $html = "";
        foreach($this->getToolsObject() as $tool) {
            if ($tool->ignore) continue;
            $engine = Engine::fromString($tool->engine);

            $html .= "<div id='tool-$tool->id' class=\"list-group-item list-group-item-action\" aria-current=\"true\">
            <div class=\"d-flex w-100 justify-content-between\">
                <h5 class=\"mb-1\">$tool->name <span class=\"badge rounded-pill bg-secondary\">$engine</span></h5>
                <small id='state-$tool->id' class='fst-italic'>Idling...</small>
            </div>
            <p class=\"mb-1\">$tool->description</p>
            <div class=\"d-flex w-100 justify-content-between\">
                <small>Author: <a href=''>$tool->author</a></small>
                <small id='report-$tool->id'></small>
            </div>
        </div>";
        }
        return $html;
    }
}
