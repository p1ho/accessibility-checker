<?php declare(strict_types=1);

/**
 * hex2rgb function. Takes in a hex string and return rgb array.
 *
 * @param  string $s [hex string]
 * @return array
 * array(
 *  "r" => int
 *  "g" => int
 *  "b" => int
 * )
 */
function hex2rgb(string $s): array
{
    if (strlen($s) === 3) {
        return [
            'r' => hexdec($s[0] . $s[0]),
            'g' => hexdec($s[1] . $s[1]),
            'b' => hexdec($s[2] . $s[2])];
    } elseif (strlen($s) === 6) {
        return [
            'r' => hexdec(substr($s, 0, 2)),
            'g' => hexdec(substr($s, 2, 2)),
            'b' => hexdec(substr($s, 4, 2))];
    } else {
        throw new Exception("Invalid Hex Color Value passed. Expected a String with 3 or 6 characters");
    }
}
