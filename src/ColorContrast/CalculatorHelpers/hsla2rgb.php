<?php declare(strict_types=1);

require_once "rgba2rgb.php";
require_once "hsl2rgb.php";

/**
 * hsla2rgb function.
 * Takes in a foreground color in hsla and a background in rgb.
 * Will return the actual displayed rgb value of the foreground.
 * @param array $fg
 * array(
 *  "h" => int|float
 *  "s" => int|float
 *  "l" => int|float
 *  "a" => float
 * )
 * @param string|array $bg [can take in mixed color types]
 * @return array
 * array(
 *  "r" => int
 *  "g" => int
 *  "b" => int
 * )
 */

function hsla2rgb(array $fg, $bg): array
{
    // convert background
  $bg_rgb = convert2rgb($bg); // includes validation process

  // validate foreground
    if (gettype($fg) !== 'array') {
        throw new Exception("Invalid Argument: foreground color must be an associative array.");
    } else {
        if (!isset($fg['h']) || !isset($fg['s']) || !isset($fg['l']) || !isset($fg['a'])) {
            throw new Exception("Invalid Argument: foreground color argument must have 'h', 's', 'l', 'a'.");
        } else {
            foreach ($fg as $key => $value) {
                if (!is_numeric($value)) {
                    throw new Exception("Invalid Argument: hsla values must be numeric.");
                }
            }
        }
    }

    // convert fg to rgba
    $fg_rgba = hsl2rgb($fg['h'], $fg['s'], $fg['l']);
    $fg_rgba['a'] = $fg['a'];

    return rgba2rgb($fg_rgba, $bg_rgb);
}
