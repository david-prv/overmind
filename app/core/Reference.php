<?php

/**
 * Class Reference
 *
 * <p>
 * The Reference class is responsible for putting
 * all relevant information for the risk assessment to the
 * corresponding reference location. The ref-file is location
 * in PROJECT_ROOT/refs.
 * </p>
 *
 * @author David Dewes <hello@david-dewes.de>
 */
class Reference
{
    /**
     * Puts the reference for one specific tool
     * to the corresponding reference location
     *
     * @param string $refPath
     * @param string $id
     * @param string $reference
     * @return bool
     */
    public static function put(string $refPath, string $id, string $reference): bool
    {
        if (!is_dir($refPath)) {
            return false;
        }

        return file_put_contents("$refPath/$id.txt", $reference);
    }

    public static function get(string $refPath, string $id): ?string
    {
        if (!is_file("$refPath/$id.txt")) {
            return NULL;
        }

        return file_get_contents("$refPath/$id.txt");
    }
}