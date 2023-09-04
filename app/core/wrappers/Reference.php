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
abstract class Reference
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

        return file_put_contents("$refPath/ref_$id.txt", $reference . "|" . hash("sha256",
                $reference . "." . Reference::getFingerPrint()));
    }

    /**
     * Fetched the reference for one specific tool
     *
     * @param string $refPath
     * @param string $id
     * @return string|null
     */
    public static function get(string $refPath, string $id): ?string
    {
        if (!is_file("$refPath/ref_$id.txt")) {
            return NULL;
        }

        return file_get_contents("$refPath/$id.txt");
    }

    /**
     * Checks reference integrity for a specific tool
     *
     * @param string $refPath
     * @param string $id
     * @return bool
     */
    public static function checkIntegrity(string $refPath, string $id): bool
    {
        $reference = file_get_contents("$refPath/ref_$id.txt");
        $_explode = explode("|", $reference);

        if (count($_explode) < 2) return false;

        $hashSum = $_explode[1];
        $encodedData = $_explode[0];

        if ($hashSum === "" || $encodedData === "") return false;

        return hash('sha256', $encodedData . "." . Reference::getFingerPrint()) === $hashSum;
    }

    /**
     * Generates a fingerprint which is used as personal secret
     * for your system to ensure data integrity within the host system
     *
     * @return string
     */
    public static function getFingerPrint(): string
    {
        $fingerprint = [php_uname(), disk_total_space('.'), filectime('/'), phpversion()];
        return hash('sha256', json_encode($fingerprint));
    }

    /**
     * Returns the personal token which is used as reference ID
     * for exported reports. This way, one can draw conclusions
     * from a generated PDF who generated the respective report and
     * performed the scan (I think this could be relevant).
     *
     * This function just produces a Cyclic-Redundancy-Check (CRC)
     * of the personal secret, which is used to verify data integrity
     * within the host system. CRC's are not secure and also not meant
     * to be used as a security algorithm, thus, one should consider using a salt.
     *
     * @param string $salt
     * @return string
     */
    public static function getPersonalToken(string $salt = ""): string
    {
        $pfp = Reference::getFingerPrint();
        return hash("crc32", $pfp . $salt);
    }
}