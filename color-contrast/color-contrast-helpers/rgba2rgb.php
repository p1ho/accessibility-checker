<?php

require_once "convert2rgb.php";

/**
 * rgba2rgb function.
 * Takes in a foreground color in rgba and a background color.
 * Will return the actual displayed rgb value of the foreground.
 * @param Array $fg
 * array(
 *  "r" => number
 *  "g" => number
 *  "b" => number
 *  "a" => float
 * )
 * @param String|Array $bg [can take in mixed color types]
 * @return array
 * array(
 *  "r" => int
 *  "g" => int
 *  "b" => int
 * )
 */
function rgba2rgb($fg, $bg) {
  // convert background
  $bg_rgb = convert2rgb($bg); // includes validation process

  // validate foreground
  if (gettype($fg) !== 'array') {
    throw new Exception("Invalid Argument: foreground color must be an associative array.");
  } else {
    if (!isset($fg['r']) || !isset($fg['g']) || !isset($fg['b']) || !isset($fg['a'])) {
      throw new Exception("Invalid Argument: foreground color argument must have 'r', 'g', 'b', 'a'.");
    } else {
      foreach ($fg as $key => $value){
        if (!is_numeric($value)) {
          throw new Exception("Invalid Argument: rgba values must be numeric.");
        }
      }
    }
  }

  // correct foreground
  foreach (array('r','g','b') as $color) {
    if ($fg[$color] < 0) {
      $fg[$color] = 0;
    } else if ($fg[$color] > 255) {
      $fg[$color] = 255;
    }
  }
  if ($fg['a'] < 0) {
    $fg['a'] = 0;
  } else if ($fg['a'] > 1) {
    $fg['a'] = 1;
  }

  // convert
  $new_rgb = $bg_rgb;
  $alpha = $fg['a'];
  $new_rgb['r'] -= ($bg_rgb['r'] - $fg['r']) * $alpha;
  $new_rgb['g'] -= ($bg_rgb['g'] - $fg['g']) * $alpha;
  $new_rgb['b'] -= ($bg_rgb['b'] - $fg['b']) * $alpha;
  $new_rgb = array_map('round', $new_rgb);

  return $new_rgb;
}
