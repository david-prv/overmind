<?php

/**
 * Class View
 *
 * <p>
 * This class handles the proper building of a view.
 * A view is an abstraction layer for UI generation.
 * It can contain all essential ingredients a normal html page can have.
 * </p>
 *
 * @author David Dewes <hello@david-dewes.de>
 */
class View
{
    private string $filename;
    private string $viewPath;
    private array $placeholders;

    /**
     * View constructor.
     *
     * @param string $viewPath
     */
    function __construct(string $viewPath)
    {
        $this->viewPath = $viewPath;
    }

    /**
     * Renders a View object
     *
     * @param View $view
     * @return bool
     */
    public static function render(View $view): bool {
        $html = file_get_contents($view->getViewPath() . "/" . $view->getFileName());

        foreach($view->getPlaceholders() as $key => $value) {
            $placeholder = (string)$value[0];
            $realValue = (string)$value[1];
            $html = str_replace($placeholder, $realValue, $html);
        }

        print_r($html);

        return $html != false && $html != NULL;
    }

    /**
     * Defines which template file should be used
     *
     * @param string $template
     */
    public function setTemplate(string $template): void {
        $this->filename = strtolower($template).".htm";
    }

    /**
     * Defines the two-dimensional array of placeholders
     * and their corresponding values. Placeholders can be expressed in a template
     * with a percentage symbol, followed by the placeholder name and then
     * closed with another percentage symbol.
     *
     * @param array $placeholders
     */
    public function setPlaceholders(array $placeholders): void {
        $this->placeholders = $placeholders;
    }

    /**
     * Returns the location of the templates
     *
     * @return string
     */
    public function getViewPath(): string {
        return $this->viewPath;
    }

    /**
     * Returns the placeholders
     *
     * @return array
     */
    public function getPlaceholders(): array {
        return $this->placeholders;
    }

    /**
     * Returns the concrete filename of the
     * used template
     *
     * @return string
     */
    public function getFileName(): string {
        return $this->filename;
    }
}