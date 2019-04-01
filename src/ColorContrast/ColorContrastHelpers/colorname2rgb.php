<?php

/**
 * colorname2rgb function. Takes in color name and return rgb array.
 * @param  String $color_name
 * @return array
 * array(
 *  "r" => int
 *  "g" => int
 *  "b" => int
 * )
 */
function colorname2rgb($color_name)
{
  require "colorname_mapping.php";
  require_once "hex2rgb.php";
  $color_name = strtolower($color_name);
  if (!isset($color_name_mapping[$color_name])) {
    throw new Exception("Invalid Argument: Color name '$color_name' not recognized.");
  } else {
    return hex2rgb($color_name_mapping[$color_name]);
  }
}
