<?php


interface Runnable
{
    public function viaEngine($engine): Runner;

    public function useCWD($cwd): Runner;

    public function atPath($appPath): Runner;

    public function withArguments($cmdLineString): Runner;

    public function identifiedBy($id): Runner;

    public function run(): bool;
}