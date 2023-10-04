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
     * Injects a static noscript component,
     * if needed or requested
     *
     * @return void
     */
    public function _injectNoScript(): void
    {
        $noScript = <<<HTML
            <noscript>
                <style>
                    body { position: relative; overflow: hidden!important;}
                    .no-script { padding: 10px; height:100vh; width:100vw; background:white; position: absolute!important; top:0; left:0; z-index: 3!important}
                </style>
                <div class="no-script">
                    <h2><svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor" class="bi bi-cone-striped" viewBox="0 0 16 16">
                      <path d="m9.97 4.88.953 3.811C10.159 8.878 9.14 9 8 9c-1.14 0-2.158-.122-2.923-.309L6.03 4.88C6.635 4.957 7.3 5 8 5s1.365-.043 1.97-.12zm-.245-.978L8.97.88C8.718-.13 7.282-.13 7.03.88L6.275 3.9C6.8 3.965 7.382 4 8 4c.618 0 1.2-.036 1.725-.098zm4.396 8.613a.5.5 0 0 1 .037.96l-6 2a.5.5 0 0 1-.316 0l-6-2a.5.5 0 0 1 .037-.96l2.391-.598.565-2.257c.862.212 1.964.339 3.165.339s2.303-.127 3.165-.339l.565 2.257 2.391.598z"/>
                    </svg> Please enable JavaScript</h2>
                </div>
            </noscript>
        HTML;

        print_r($noScript);
    }
}