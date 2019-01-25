<?php

require_once "hex2rgb.php";
require_once "hsl2rgb.php";
require_once "hsv2rgb.php";
require_once "colorname2rgb.php";

/**
 * Strategy function that takes in a valid color
 * (one of: one of: hex string, or rgb/hsl/hsv associative arrays,
 * the keys in this array should be in lower case)
 *
 * Note: if hex is passed, it must include the '#' and has a length of either
 * 4 or 7 including the '#'.
 */

function convert2rgb($color) {
  if (gettype($color) === 'string') {
    if ($color[0] === '#') {
      if (strlen($color) === 4 || strlen($color) === 7) {
        $color_rgb = hex2rgb(substr($color, 1));
      } else {
        throw new Exception("Invalid Argument: Hex values must have a length of 4 or 7 including the leading #.");
      }
    } else {
      $color_rgb = colorname2rgb($color);
    }
  } else if (gettype($color) === 'array') {
    if (isset($color['r']) && isset($color['g']) && isset($color['b'])) {
      foreach (array('r', 'g', 'b') as $c) {
        if ($color[$c] < 0) {
          $color[$c] = 0;
        } else if ($color[$c] > 255) {
          $color[$c] = 255;
        }
      }
      return $color;
    } else if (isset($color['h']) && isset($color['s']) && isset($color['v'])) {
      $color_rgb = hsv2rgb($color['h'], $color['s'], $color['v']);
    } else if (isset($color['h']) && isset($color['s']) && isset($color['l'])) {
      $color_rgb = hsl2rgb($color['h'], $color['s'], $color['l']);
    } else {
      throw new Exception("Invalid Argument: Color array has invalid keys, you entered " . implode(',', $color_keys) . " as your keys.");
    }
  } else {
    throw new Exception("Invalid Argument: Argument not recognized. You passed a ". gettype($color) . ". Only strings and arrays are allowed as arguments.");
  }
  return $color_rgb;
}
