<?php

require_once "../rgba2rgb.php";

$bg = array(
  'r' => 128,
  'g' => 255,
  'b' => 0,
);

$fg = array(
  'r' => 60,
  'g' => 60.7,
  'b' => 120,
  'a' => .3,
);

var_dump(rgba2rgb($fg, $bg));
