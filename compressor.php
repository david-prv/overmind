<?php

require __DIR__ . '/vendor/autoload.php';

use MatthiasMullie\Minify;

/*
 *---------------------------------------------------------------
 * Compressor Configuration
 *---------------------------------------------------------------
 *
 * Include any javascript or stylesheet file, that you want to
 * be compressed by running this script.
 * Make sure that all files are located in /static in their
 * corresponding sub directories.
 *
 */

$path = getcwd();
$js_files = array("bundle", "schedule", "hotkeys");
$css_files = array("main");

/*
 *---------------------------------------------------------------
 * Main Script
 *---------------------------------------------------------------
 *
 * The script will first check if the current working
 * directory is valid. This script can only be run from the
 * project's root directory.
 *
 * Next we iterate through all javascript files and
 * apply the Minify's library method "minify" to them.
 * This will write the minified code to the same directory
 * but in a different file (can be easily kept apart due to the new
 * file extension ".min.js" instead of just ".js").
 *
 * The same happens for all CSS files, given by the script
 * configuration above. Note, that the new file extension
 * for compressed CSS files is ".min.css" and not ".css".
 *
 */

if (substr_compare($path, "scanner-bundle", -strlen("scanner-bundle")) !== 0) die("ERROR: you are probably running this script from the wrong " .
    "location. Please navigate to the root directory of this project and run: php compressor.php");

// STEP 1: JavaScript

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

// STEP 2: Stylesheets

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

// STEP 3: Done

echo "INFO: All files were compressed successfully.";
exit(0);