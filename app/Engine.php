<?php

abstract class Engine {
    const Python3 = "python3";
    const Python2 = "python2";
    const PHP = "php";
    const PHPInline = "php -r";
    const Default = "";

    public static function fromString($value): String {
        switch(strtolower($value)) {
            case "py3":
                return Engine::Python3;
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