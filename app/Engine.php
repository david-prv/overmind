<?php

/**
 * Class Engine
 *
 * <p>
 * This abstract class has the purpose to simplify
 * the way we handle different Engine strings. Engines, basically,
 * are just strings we use in the command line right before the app path.
 * To get some kind of standard, I have decided to put that into an abstract class
 * with constants and public static methods to make it accessible everywhere.
 * </p>
 *
 * @author David Dewes <hello@david-dewes.de>
 */
abstract class Engine
{

    const Python3 = "python3";
    const Python2 = "python2";
    const PHP = "php";
    const PHPInline = "php -r";
    const Default = "";

    /**
     * Can be used to ensure a valid running engine.
     * All engines will be defined here
     *
     * @param $value
     * @return String
     */
    public static function valueOf($value): string
    {
        switch (strtolower($value)) {
            case "python3":
            case "py3":
                return Engine::Python3;
            case "python2":
            case "py2":
                return Engine::Python2;
            case "php":
                return Engine::PHP;
            case "php-inline":
                return Engine::PHPInline;
            default:
                return Engine::Default;
        }
    }
}