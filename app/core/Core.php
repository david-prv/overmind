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
class Core
{
    private static ?Core $instance = NULL;

    private ?array $argv = NULL;
    private $TOOLS_OBJECT;

    private string $APP_PATH;
    private string $TOOLS_PATH;

    private Pages $pages;
    private Analyzer $analyzer;

    private string $PROJECT_NAME = "Scanner Bundle";
    private string $PROJECT_AUTHOR = "David Dewes";
    private string $PROJECT_VERSION = "1.2.4";
    private string $PROJECT_DESCRIPTION =
        "A small collection of open-source tools out there to " .
        "inspect and scan any kind of web pages.";
    private string $PROJECT_LOGO = "<img style='transform: translateY(-2px);' src='/static/img/etage4-logo.png' width='35'>";

    ////////////////////
    // HELPER METHODS //
    ////////////////////

    /**
     * Checks the common pre-condition for all
     * core-methods
     */
    private function preCondition(): void
    {
        if ($this->argv === NULL) App::finishWithError("no arguments provided");
    }

    /**
     * Checks if any provided argument is NULL
     *
     * @param mixed ...$args
     */
    private function verifyArgs(...$args): void
    {
        foreach ($args as $arg) {
            if (is_null($arg)) {
                App::finishWithError("invalid arguments or incomplete arg set");
            }
        }
    }


    ///////////////////////
    // SINGLETON METHODS //
    ///////////////////////

    /**
     * Core constructor.
     *
     * @param string|null $tp
     * @param string|null $tip
     */
    private function __construct(?string $tp = NULL, ?string $tip = NULL)
    {
        $this->APP_PATH = getcwd();
        $this->TOOLS_PATH = ($tp === NULL) ? $this->APP_PATH . "/app/tools" : $tp;
        $this->TOOLS_OBJECT = json_decode(file_get_contents(($tip === NULL)
            ? $this->APP_PATH . "/app/tools/map.json"
            : $tip), false);

        $this->pages = Pages::getInstance();
        $this->analyzer = Analyzer::getInstance();

        foreach ($this->TOOLS_OBJECT as $key => $value) {
            if ($value->ignore) unset($this->TOOLS_OBJECT[$key]);
        }

    }

    /**
     * Creates an Instance
     *
     * @return Core
     */
    public static function getInstance(): Core
    {
        if (self::$instance === NULL) {
            self::$instance = new Core();
        }
        return self::$instance;
    }

    ////////////////////
    // SETTER METHODS //
    ////////////////////

    /**
     * Sets the argv attribute and thus
     * allows the Core to use parameters passed as
     * GET or POST params
     *
     * @param $params
     * @return Core
     */
    public function withParams($params): Core
    {
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
    public function render($view = NULL): void
    {
        /*
         *----------------------------------------------
         * Pages Configuration
         *----------------------------------------------
         * To add/edit/configure existing pages
         * go to Pages class. Locate the create()
         * method and follow the instructions as stated
         * in the method docs.
         *
         */

        if ($this->argv !== NULL && $view === NULL) {
            $view = $this->argv["page"];
        }

        Page::render($this->pages->get($view));
    }

    /**
     * Builds the Runnable and executes it
     *
     * @return void
     */
    public function scan(): void
    {
        $this->preCondition();

        $target = $this->getArgNullable("target");
        $engine = $this->getArgNullable("engine");
        $app = $this->getArgNullable("index");
        $args = $this->getArgNullable("args");
        $id = $this->getArgNullable("id");

        $this->verifyArgs($target, $engine, $app, $args, $id);

        $runner = (new Scanner())
            ->target($target)
            ->viaEngine(Engine::valueOf($engine))
            ->useCWD($this->APP_PATH)
            ->atPath($app)
            ->withArguments($args)
            ->identifiedBy($id);

        if ($runner->run()) App::finishWithSuccess();
        else App::finishWithError();

    }

    /**
     * Runs an analysis of the actual report and the reference
     *
     * @return void
     */
    public function analyze(): void
    {
        $this->preCondition();

        $id = $this->getArgNullable("id");

        $this->verifyArgs($id);
        $res = $this->analyzer->get($this->TOOLS_PATH, $id);

        if (is_null($res)) {
            App::finishWithError("-1|[]");
            return;
        }

        $analysisResult = $res->analyze();
        App::finishWithSuccess($analysisResult->returnValue() . "|" . json_encode($analysisResult->diff()));
    }

    /**
     * Integrates a new tool to the bundle locally
     *
     * @return void
     */
    public function integrate(): void
    {
        $this->preCondition();

        $name = $this->getArgNullable("name");
        $creator = $this->getArgNullable("author");
        $url = $this->getArgNullable("url");
        $version = $this->getArgNullable("version");
        $cmdline = $this->getArgNullable("cmdline");
        $description = $this->getArgNullable("description");
        $engine = $this->getArgNullable("engine");
        $index = $this->getArgNullable("index");
        $keywords = $this->getArgNullable("keywords");

        $this->verifyArgs($name, $creator, $url, $version, $cmdline, $description,
            $engine, $index, $keywords);

        $scanner = (new Scanner())
            ->useCWD($this->TOOLS_PATH)
            ->atPath($index)
            ->viaEngine($engine)
            ->hasName($name)
            ->fromCreator($creator)
            ->setCreatorURL($url)
            ->inVersion($version)
            ->withArguments($cmdline)
            ->searchKeywords($keywords)
            ->describedBy($description)
            ->fileData($_FILES);

        $res = $scanner->create();
        if ($res !== -1) App::finishWithRedirect("page=schedule&edit=$res");
        else App::finishWithError("<h1>Something went wrong for '$name'! Please try again.</h1>");
    }

    /**
     * Creates a reference report for a tool
     *
     * @return void
     */
    public function reference(): void
    {
        $this->preCondition();

        $id = $this->getArgNullable("id");
        $reference = $this->getArgNullable("reference");

        $this->verifyArgs($id, $reference);

        $scanner = (new Scanner())
            ->useCWD($this->TOOLS_PATH)
            ->identifiedBy($id)
            ->withReference($reference);

        if ($scanner->reference()) App::finishWithSuccess();
        else App::finishWithError();
    }

    /**
     * Deletes an existing tool from the bundle
     *
     * @return void
     */
    public function delete(): void
    {
        $this->preCondition();

        $id = $this->getArgNullable("id");

        $this->verifyArgs($id);

        $scanner = (new Scanner())->useCWD($this->TOOLS_PATH)->identifiedBy($id);

        if ($scanner->delete()) App::finishWithSuccess();
        else App::finishWithError();
    }

    /**
     * Updates an existing tool in the bundle
     *
     * @return void
     */
    public function edit(): void
    {
        $this->preCondition();

        $jsonObj = $this->getArgNullable("json");

        $this->verifyArgs($jsonObj);

        $jsonObj = json_decode($jsonObj);

        $id = (isset($jsonObj->id)) ? $jsonObj->id : NULL;
        $name = (isset($jsonObj->name)) ? $jsonObj->name : NULL;
        $creator = (isset($jsonObj->author)) ? $jsonObj->author : NULL;
        $url = (isset($jsonObj->url)) ? $jsonObj->url : NULL;
        $version = (isset($jsonObj->version)) ? $jsonObj->version : NULL;
        $cmdline = (isset($jsonObj->args)) ? $jsonObj->args : NULL;
        $description = (isset($jsonObj->description)) ? $jsonObj->description : NULL;
        $engine = (isset($jsonObj->engine)) ? $jsonObj->engine : NULL;
        $index = (isset($jsonObj->index)) ? $jsonObj->index : NULL;
        $keywords = (isset($jsonObj->keywords)) ? $jsonObj->keywords : NULL;

        $this->verifyArgs($id, $name, $creator, $url, $version, $cmdline, $description,
            $engine, $index, $keywords);

        $scanner = (new Scanner())
            ->useCWD($this->TOOLS_PATH)
            ->atPath($index)
            ->viaEngine($engine)
            ->hasName($name)
            ->fromCreator($creator)
            ->setCreatorURL($url)
            ->inVersion($version)
            ->withArguments($cmdline)
            ->searchKeywords($keywords)
            ->describedBy($description)
            ->identifiedBy($id);

        if ($scanner->update()) App::finishWithSuccess();
        else App::finishWithError();
    }

    /**
     * Schedules interactions for a tool
     *
     * @return void
     */
    public function schedule(): void
    {
        $this->preCondition();

        $interactions = $this->getArgNullable("interactions");
        $id = $this->getArgNullable("id");

        $this->verifyArgs($interactions, $id);

        $interactions = explode(",", $interactions);

        $scanner = (new Scanner())
            ->useCWD($this->APP_PATH . "/app/tools")
            ->identifiedBy($id)
            ->withInteractions($interactions);

        if ($scanner->schedule()) App::finishWithSuccess();
        else App::finishWithError();
    }

    /**
     * Creates a snapshot of the current instance
     *
     * @return void
     */
    public function snapshot(): void
    {
        $this->preCondition();

        Snapshot::create($this->APP_PATH);
        App::finishWithSuccess();
    }

    ////////////////////
    // GETTER METHODS //
    ////////////////////

    /**
     * Failsafe getter for arguments
     *
     * @param string|null $arg
     * @return mixed
     */
    public function getArg(?string $arg = NULL)
    {
        if ($arg === NULL && !is_null($this->argv)) return $this->argv;
        if (!is_null($this->argv) && isset($this->argv[$arg]))
            return $this->argv[$arg];
        return "";
    }

    /**
     * Failsafe getter for nullable arguments
     *
     * @param string|null $arg
     * @return mixed
     */
    public function getArgNullable(?string $arg)
    {
        $_resolved = $this->getArg($arg);
        if ($_resolved === "") return NULL;
        else return $_resolved;
    }

    /**
     * Checks whether there's a certain GET
     * or POST argument provided (used for optionals)
     *
     * @param string $arg
     * @return bool
     */
    public function isArgPresent(string $arg): bool
    {
        return isset($_GET[$arg]) || isset($_POST[$arg]);
    }

    /**
     * Getter for tools object
     *
     * @return array
     */
    public function getToolsObject(): array
    {
        return $this->TOOLS_OBJECT;
    }

    /**
     * Getter for tools object json encoded
     *
     * @return string
     */
    public function getToolsJson(): string
    {
        return json_encode($this->TOOLS_OBJECT);
    }

    /**
     * Getter for project author
     *
     * @return string
     */
    public function getProjectAuthor(): string
    {
        return $this->PROJECT_AUTHOR;
    }

    /**
     * Getter for project name
     *
     * @return string
     */
    public function getProjectName(): string
    {
        return $this->PROJECT_NAME;
    }

    /**
     * Getter for project version
     *
     * @return string
     */
    public function getProjectVersion(): string
    {
        return $this->PROJECT_VERSION;
    }

    /**
     * Getter for project description
     *
     * @return string
     */
    public function getProjectDescription(): string
    {
        return $this->PROJECT_DESCRIPTION;
    }

    /**
     * Getter for project logo
     *
     * @return string
     */
    public function getProjectLogo(): string
    {
        return $this->PROJECT_LOGO;
    }

    /**
     * Renders tools object to html
     *
     * @return string
     */
    public function renderToolsAsHtml(): string
    {
        $html = (count($this->getToolsObject()) === 0) ? "<h2 class='text-muted text-center'>No tools found</h2>
                                                          <a class='no-cursor' title='Vector by https://vecteezy.com'>
                                                            <img class='img-center' src='/static/img/sleep.jpg' />
                                                          </a>" : "";
        foreach ($this->getToolsObject() as $tool) {
            if ($tool->ignore) continue;
            $engine = Engine::valueOf($tool->engine);
            // $engine = Engine::toHTML($tool->engine);
            $interactive = (Schedule::isPresent($this->APP_PATH, $tool->id)) ? "<i title=\"Interactive Script\" class=\"fa fa-magic\"></i>" : "";

            $html .= "<div onclick='$(this).toggleClass(`selection`)' id='tool-$tool->id' class=\"list-group-item list-group-item-action tool\" aria-current=\"true\">
            <div class=\"d-flex w-100 justify-content-between\">
                <h5 class=\"mb-1\"><span id=\"title-$tool->id\">$tool->name</span> $interactive</h5>
                <small id='state-$tool->id' class='fst-italic tool-state'>Idling...</small>
                <div class='hidden' id='options-tool-$tool->id'>
                    <div class=\"d-grid gap-2 d-md-block\">
                     <button onclick='(function(event) {
                          event.stopPropagation();
                          window.location.href = \"index.php?page=schedule&edit=$tool->id&noref=1\";
                      })(event);' class=\"btn btn-sm btn-outline-secondary\" type=\"button\"><i class=\"fa fa-list-ul\"></i></button>
                      <button onclick='(function(event) {
                          event.stopPropagation();
                          editTool($tool->id)
                      })(event);' class=\"btn btn-sm btn-outline-secondary\" data-bs-toggle=\"modal\" data-bs-target=\"#editModal\" type=\"button\"><i class=\"fa fa-pencil\"></i></button>
                      <button onclick='(function(event) {
                          event.stopPropagation();
                          deleteTool($tool->id)
                      })(event);' class=\"btn btn-sm btn-outline-danger\" type=\"button\"><i class=\"fa fa-times\"></i></button>
                    </div>
                </div>
                </div>
                <p id='description-$tool->id' class=\"mb-1 tool-description\">$tool->description</p>
                <div class=\"d-flex w-100 justify-content-between\">
                    <small>Author: <a href='$tool->url'>$tool->author</a></small>
                    <small id='scanner-$tool->id'>ID: $tool->id</small>
                </div>
            </div>";
        }
        return $html;
    }

    /**
     * Renders scheduled interactions to html
     * for given tool ID
     *
     * @param string $id
     * @return string
     */
    public function renderScheduleAsHtml(string $id): string
    {
        return Schedule::render($this->APP_PATH, $id);
    }
}
