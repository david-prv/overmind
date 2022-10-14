<?php


class Runner implements Runnable
{
    private String $engine;
    private String $path;
    private String $cmdline;
    private String $id;
    private String $cwd;

    public function viaEngine($engine): Runner {
        $this->engine = $engine;
        return $this;
    }

    public function useCWD($cwd): Runner {
        $this->cwd = $cwd;
        return $this;
    }

    public function atPath($appPath): Runner {
        $this->path = $appPath;
        return $this;
    }

    public function withArguments($cmdLineString): Runner {
        $this->cmdline = $cmdLineString;
        return $this;
    }

    public function identifiedBy($id): Runner {
        $this->id = $id;
        return $this;
    }

    public function run(): bool {
        return shell_exec("python3 " . $this->cwd . "/app/tools/runner.py " .
            $this->engine . " " . $this->cwd . "/app/tools/" . $this->path .
            " " . $this->cmdline . " " . $this->id) !== NULL;
    }
}