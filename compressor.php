<?php

require __DIR__ . '/vendor/autoload.php';

use MatthiasMullie\Minify;

$path = getcwd();
$js_files = array("bundle");
$css_files = array("main");

if (strpos($path, "\app")) die("ERROR: you are probably running this script from the wrong " .
    "location. Please navigate to the root directory of this project and run: php compressor.php");

// JavaScript

foreach($js_files as $js_file) {
    $fileName = "$js_file.js";
    $fileNameMinified = "$js_file.min.js";

    echo "INFO: Compressing $fileName to $fileNameMinified...\n";

    try {
        $sourcePath = $path . "/static/js/$fileName";
        $minifier = new Minify\JS($sourcePath);
        $minifier->minify($path . "/static/js/$fileNameMinified");
    } catch (Exception $ex) {
        echo "ERROR: Compression has failed\n";
    }
}

// Stylesheets

foreach($css_files as $css_file) {
    $fileName = "$css_file.css";
    $fileNameMinified = "$css_file.min.css";

    echo "INFO: Compressing $fileName to $fileNameMinified...\n";

    try {
        $sourcePath = $path . "/static/css/$fileName";
        $minifier = new Minify\CSS($sourcePath);
        $minifier->minify($path . "/static/css/$fileNameMinified");
    } catch (Exception $ex) {
        echo "ERROR: Compression has failed\n";
    }
}

// Done

echo "INFO: All files were compressed successfully.";
exit(0);