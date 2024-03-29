<?php

/**
 * Class StringComparator
 *
 * <p>
 * This class is going to be used during the risk assessment
 * generation. It is able to calculate the distance between two
 * strings and return it as an integer value.
 * </p>
 *
 * <p>
 * A special feature is the ability to take care about so-called
 * placeholder values, which are just dynamic values that WILL
 * change but should not affect the distance at all. These placeholders
 * are denoted by surrounding "%"-signs. Also, this class is able
 * to compare whole text blocks (of different lengths), not just single words.
 * </p>
 */
class StringComparator
{
    private string $string1;
    private string $string2;
    private int $distance;
    private array $badWords;

    /**
     * Constructor.
     *
     * @param string $string1
     * @param string $string2
     */
    public function __construct(string $string1, string $string2)
    {
        $this->string1 = $string1;
        $this->string2 = $string2;
        $this->distance = PHP_INT_MAX; // infinity
        $this->badWords = array();
    }

    /**
     * Calculate the positive difference between two arrays
     *
     * @param array $a
     * @param array $b
     * @return int
     */
    private function lengthDifference(array $a, array $b): int
    {
        $a_length = count($a);
        $b_length = count($b);

        return ($a_length >= $b_length) ? $a_length - $b_length : $b_length - $a_length;
    }

    /**
     * Converts a string to its binary representation.
     *
     * @param string $word
     * @return string
     */
    private function convertStrToBinary(string $word): string
    {
        $characters = str_split($word);

        $binary = [];
        foreach ($characters as $character) {
            $data = unpack('H*', $character);
            $binary[] = base_convert($data[1], 16, 2);
        }

        return implode(' ', $binary);

    }

    /**
     * Calculates the hamming distance between two (binary) strings.
     *
     * @param string $a
     * @param string $b
     * @return int
     */
    private function hammingDistance(string $a, string $b): int
    {
        $a_bits = str_split($a);
        $b_bits = str_split($b);
        $ctr = 0;
        for ($i = 0; $i < count((count($a_bits) >= count($b_bits)) ? $a_bits : $b_bits); $i++) {
            if (!isset($a_bits[$i]) || !isset($b_bits[$i])) {
                $ctr++;
                continue;
            };
            if ($a_bits[$i] != $b_bits[$i]) {
                $ctr++;
            }
        }
        return $ctr;
    }

    /**
     * Update the comparative and compared.
     *
     * @param string $newComparative
     * @param string $newCompared
     * @return void
     */
    public function update(string $newComparative, string $newCompared): void
    {
        $this->string1 = $newComparative;
        $this->string2 = $newCompared;
    }

    /**
     * Does the comparison for two strings.
     * This function also works for entire text blocks,
     * with a long series of occurring words. If diff = 0,
     * both texts are identical (ignoring the placeholders).
     *
     * @return StringComparator
     */
    public function compare(): StringComparator
    {
        $a = $this->string1;
        $b = $this->string2;

        $a_words = preg_split("/[\n\r, ]+/", $a); //explode(" ", $a);
        $b_words = preg_split("/[\n\r, ]+/", $b); //explode(" ", $b);

        //var_dump($a);
        //var_dump($b_words);

        $accumulator = 0;
        for ($i = 0; $i < count(
            (count($a_words) <= count($b_words))
                ? $a_words
                : $b_words
        ); $i++) {
            // don't take placeholders into account
            if ((isset(trim($a_words[$i])[0]) && trim($a_words[$i])[0] === "%")
                || (isset(trim($b_words[$i])[0]) && trim($b_words[$i])[0] === "%")) {
                continue;
            }

            // accumulate differences over all blocks
            $hDist = $this->hammingDistance(
                $this->convertStrToBinary(trim($a_words[$i])),
                $this->convertStrToBinary(trim($b_words[$i]))
            );
            $accumulator = $accumulator + $hDist;
            if ($hDist > 0) $this->badWords[] = [$a_words[$i] => $b_words[$i]];
        }

        // total diff of arrays is also text difference
        // one char difference results in 8 distance, so we divide by that factor
        $this->distance = (int)($accumulator / 8) + $this->lengthDifference($a_words, $b_words);
        return $this;
    }

    /**
     * Returns the (modified) hamming distance
     *
     * @return int
     */
    public function getDistance(): int
    {
        return $this->distance;
    }

    /**
     * Returns all bad words
     *
     * @return array
     */
    public function getBadWords(): array
    {
        return $this->badWords;
    }
}