<?php

/**
 * Class Visualization
 *
 * <p>
 * This class is a small tool collection for the main thread. While the whole procedure
 * from adding/executing/analysing scans and their results belongs to the framework,
 * rendering a static summary appeared to be much easier and less complicated without the
 * framework's logic. The framework has not the purpose of being a general-use web-framework
 * but to work for that very specific use-case very well and easy.
 * </p>
 *
 * <p>
 * So, Visualization is an alternative view helper, supporting the application to transport
 * information from the scanning context to the top-level view context without any issue. This is
 * done via Cookies and GET-parameters. Also, the HTML view is rendered completely sovereign
 * rendered by this class, not the scanner-bundle framework.
 * </p>
 *
 * @author David Dewes <hello@david-dewes.de>
 */
class Visualization
{
    public function __construct()
    {
    }
}