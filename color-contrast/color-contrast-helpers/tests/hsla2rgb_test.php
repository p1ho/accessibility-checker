<?php

require_once "../hsla2rgb.php";

$bg = array(
  'r' => 50,
  'g' => 150,
  'b' => 255,
);

$fg = array(
  'h' => 120,
  's' => 35,
  'l' => 75,
  'a' => .75,
);

var_dump(hsla2rgb($fg, $bg));
