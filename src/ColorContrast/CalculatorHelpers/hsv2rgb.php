<?php declare(strict_types=1);

/**
 * hsv2rgb function. Takes in h, s, v and return rgb array.
 * Translated from https://snook.ca/technical/colour_contrast/colour.html#fg=33FF33,bg=333333 (Code to check taken from function updateColourResults())
 *
 * @param  int|float $h Hue as a value between 0 - 360 degrees
 * @param  int|float $s Saturation as a value between 0 - 100 %
 * @param  int|float $v Value as a value between 0 - 100 %
 * @return array
 * array(
 *  "r" => int
 *  "g" => int
 *  "b" => int
 * )
 */
function hsv2rgb($h, $s, $v): array
{
    // make sure values are within range
    if ($h < 0) {
        $h = 0;
    } elseif ($h > 360) {
        $h = 360;
    }
    if ($s < 0) {
        $s = 0;
    } elseif ($s > 100) {
        $s = 100;
    }
    if ($v < 0) {
        $l = 0;
    } elseif ($v > 100) {
        $l = 100;
    }

    $s  = $s / 100;
    $v  = $v / 100;
    $hi = floor(($h / 60) % 6);
    $f  = ($h / 60) - $hi;
    $p  = $v * (1 - $s);
    $q  = $v * (1 - $f * $s);
    $t  = $v * (1 - (1 - $f) * $s);

    $rgb = [];

    switch ($hi) {
    case 0:
      $rgb['r'] = $v;
      $rgb['g'] = $t;
      $rgb['b'] = $p;
      break;
    case 1:
      $rgb['r'] = $q;
      $rgb['g'] = $v;
      $rgb['b'] = $p;
      break;
    case 2:
      $rgb['r'] = $p;
      $rgb['g'] = $v;
      $rgb['b'] = $t;
      break;
    case 3:
      $rgb['r'] = $p;
      $rgb['g'] = $q;
      $rgb['b'] = $v;
      break;
    case 4:
      $rgb['r'] = $t;
      $rgb['g'] = $p;
      $rgb['b'] = $v;
      break;
    case 5:
      $rgb['r'] = $v;
      $rgb['g'] = $p;
      $rgb['b'] = $q;
      break;
  }
    foreach (['r','g','b'] as $color) {
        $rgb[$color] = min(255, round($rgb[$color] * 256));
    }
    return $rgb;
}
