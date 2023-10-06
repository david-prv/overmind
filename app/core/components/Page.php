<?php

/**
 * Class Page
 *
 * <p>
 * This class handles the proper building of a view.
 * A view is an abstraction layer for UI generation.
 * It can contain all essential ingredients a normal html page can have.
 * </p>
 *
 * @author David Dewes <hello@david-dewes.de>
 */
class Page
{
    private string $filename;
    private string $viewPath;
    private array $placeholders;
    private bool $error;
    private bool $noScript;

    /**
     * Page constructor.
     *
     * @param string $viewPath
     * @param bool $noScript
     */
    function __construct(string $viewPath, bool $noScript = false)
    {
        $this->viewPath = $viewPath;
        $this->noScript = $noScript;
        $this->error = false;
    }

    /**
     * Renders a Page object
     *
     * @param Page $view
     * @return bool
     */
    public static function render(Page $view): bool
    {
        if (!$view->isComplete()) {
            die("Page could not be constructed");
        }

        $html = file_get_contents($view->getViewPath() . "/" . $view->getFileName());

        foreach ($view->getPlaceholders() as $key => $value) {
            $placeholder = (string)$value[0];
            $realValue = (string)$value[1];
            $html = str_replace($placeholder, $realValue, $html);
        }

        if ($view->requiresNoScript()) $view->_injectNoScript();
        print_r($html);

        return $html != false;
    }

    /**
     * Defines which template file should be used
     *
     * @param string $template
     * @return Page
     */
    public function setTemplate(string $template): Page
    {
        $this->filename = strtolower($template) . ".htm";
        return $this;
    }

    /**
     * Defines the two-dimensional array of placeholders
     * and their corresponding values. Placeholders can be expressed in a template
     * with a percentage symbol, followed by the placeholder name and then
     * closed with another percentage symbol
     *
     * @param array $placeholders
     * @return Page
     */
    public function setPlaceholders(array $placeholders): Page
    {
        $this->placeholders = $placeholders;
        return $this;
    }

    /**
     * Indicates that an error occurred in the caller.
     * Prevents the render method to perform
     *
     * @param bool $value
     * @return Page
     */
    public function setError(bool $value): Page
    {
        $this->error = $value;
        return $this;
    }

    /**
     * Returns the location of the views
     *
     * @return string
     */
    public function getViewPath(): string
    {
        return $this->viewPath;
    }

    /**
     * Returns the placeholders
     *
     * @return array
     */
    public function getPlaceholders(): array
    {
        return $this->placeholders;
    }

    /**
     * Returns the concrete filename of the
     * used template
     *
     * @return string
     */
    public function getFileName(): string
    {
        return $this->filename;
    }

    /**
     * Returns whether an error occurred in the caller
     * or not, what means analogously if the view was constructed
     * successfully or not
     *
     * @return bool
     */
    public function isComplete(): bool
    {
        return !$this->error;
    }

    /**
     * Returns true if the rendered view does have a
     * certain priority/use, for which a noscript alert
     * is crucial (e.g. main view). For some other pages, this
     * might not be the case.
     *
     * @return bool
     */
    public function requiresNoScript(): bool
    {
        return $this->noScript;
    }

    /**
     * Injects a static noscript notice,
     * if needed or requested
     *
     * @return void
     */
    public function _injectNoScript(): void
    {
        $noScript = <<<HTML
            <noscript>
                <style>
                    body {
                        position: relative;
                        overflow: hidden!important;
                    }
                    .no-script {
                        padding-top:10%!important;
                        height:100vh;
                        width:100vw;
                        background:white;
                        position: absolute!important;
                        z-index: 3!important;
                        margin: auto;
                        text-align: center;
                    }
                </style>
                <div class="no-script">
                    <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor" class="bi bi-exclamation-octagon-fill" viewBox="0 0 16 16">
                      <path d="M11.46.146A.5.5 0 0 0 11.107 0H4.893a.5.5 0 0 0-.353.146L.146 4.54A.5.5 0 0 0 0 4.893v6.214a.5.5 0 0 0 .146.353l4.394 4.394a.5.5 0 0 0 .353.146h6.214a.5.5 0 0 0 .353-.146l4.394-4.394a.5.5 0 0 0 .146-.353V4.893a.5.5 0 0 0-.146-.353L11.46.146zM8 4c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995A.905.905 0 0 1 8 4zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                    </svg>
                    <p></p>
                    <h2>Framework not functional</h2>
                    <p>Please enable JavaScript to run framework!</p>
                </div>
            </noscript>
        HTML;

        print_r($noScript);
    }
}