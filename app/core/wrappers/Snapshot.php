<?php

/**
 * Class Snapshot
 *
 * The Snapshot class is responsible for preparing and running the
 * snapshot creation script. It will produce a zip-archive, which
 * can be re-integrated with the integration bot.
 *
 * @author David Dewes <hello@david-dewes.de>
 */
abstract class Snapshot
{
    /**
     * Creates a snapshot file and places it into
     * project's root directory
     *
     * @param string $cwd
     * @param string $author
     * @param string $description
     * @return bool
     */
    public static function create(string $cwd = "", string $author = "Scanner-Bundle Framework",
                                  string $description = "Snapshot created by the framework."): bool
    {
        $_author = ($author !== NULL && $author !== "") ? "\"$author\"" : "";
        $_description = ($description !== NULL && $description !== "") ? "\"$description\"" : "";

        $result = shell_exec("python3 $cwd/app/tools/snapshot.py $cwd $_author $_description");
        return !($result === false || $result === null);
    }
}