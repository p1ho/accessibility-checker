<?php

/**
 * hsl2rgb function. Takes in h, s, l, and return rgb array.
 * Translated https://stackoverflow.com/questions/2353211/hsl-to-rgb-color-conversion
 *
 * @param  Number $h Hue as a value between 0 - 360 degrees
 * @param  Number $s Saturation as a value between 0 - 100 %
 * @param  Number $l Lightness as a value between 0 - 100 %
 * @return array
 * array(
 *  "r" => int
 *  "g" => int
 *  "b" => int
 * )
 */
function hsl2rgb($h, $s, $l)
{
  // make sure values are within range
  if ($h < 0) {
    $h = 0;
  } else if ($h > 360) {
    $h = 360;
  }
  if ($s < 0) {
    $s = 0;
  } else if ($s > 100) {
    $s = 100;
  }
  if ($l < 0) {
    $l = 0;
  } else if ($l > 100) {
    $l = 100;
  }

  // convert to decimal
  $h = $h / 360;
  $s = $s / 100;
  $l = $l / 100;

  $rgb = array();

  // convert to rgb
  if ($s == 0) { // achromatic
    foreach (array('r', 'g', 'b') as $color) {
      $rgb[$color] = $l;
    }
  } else { //chromatic
    $q = $l < 0.5 ? $l * (1 + $s) : $l + $s - $l * $s;
    $p = 2 * $l - $q;
    $rgb['r'] = hue2rgb($p, $q, $h + 1 / 3);
    $rgb['g'] = hue2rgb($p, $q, $h);
    $rgb['b'] = hue2rgb($p, $q, $h - 1 / 3);
  }
  foreach (array('r', 'g', 'b') as $color) {
    $rgb[$color] = min(255, round($rgb[$color] * 255));
  }
  return $rgb;
}

/**
 * hue2rgb function. Helper for hsl2rgb.
 * @param  Number $p
 * @param  Number $q
 * @param  Number $t
 * @return number
 */
function hue2rgb($p, $q, $t)
{
  if ($t < 0) {
    $t++;
  }
  if ($t > 1) {
    $t--;
  }
  if ($t < 1 / 6) {
    return $p + ($q - $p) * 6 * $t;
  }
  if ($t < 1 / 2) {
    return $q;
  }
  if ($t < 2 / 3) {
    return $p + ($q - $p) * (2 / 3 - $t) * 6;
  }
  return $p;
}
