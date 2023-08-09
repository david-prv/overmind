<?php

/**
 * Include all composer requirements
 */
require __DIR__ . '/vendor/autoload.php';

/**
 * Include framework autoloader
 */
require __DIR__ . '/app/core/Autoloader.php';

/**
 * Include main application
 */
require __DIR__ . '/app/App.php';

$app = new App();
$app->run();