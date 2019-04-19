<?php declare(strict_types=1);

/**
 * Test cases for testing color conversion.
 *
 * Only tests: hex, hsv, hsl
 *
 * color names are not tested because they are already hardcoded and thus
 * is pointless to test (testing hardcoded values against hardcoded values)
 */

$testcases_ColorConversion = array(
//------------------------------------------------------------------------------
  // hex: consulted https://www.rapidtables.com/convert/color/rgb-to-hex.html
  // from table values
  array(
    array('r'=>0, 'g'=>0, 'b'=>0),
    '#000000',
  ),
  array(
    array('r'=>255, 'g'=>255, 'b'=>255),
    '#FFFFFF',
  ),
  array(
    array('r'=>255, 'g'=>0, 'b'=>0),
    '#FF0000',
  ),
  array(
    array('r'=>0, 'g'=>255, 'b'=>0),
    '#00FF00',
  ),
  array(
    array('r'=>0, 'g'=>0, 'b'=>255),
    '#0000FF',
  ),
  array(
    array('r'=>255, 'g'=>255, 'b'=>0),
    '#FFFF00',
  ),
  array(
    array('r'=>0, 'g'=>255, 'b'=>255),
    '#00FFFF',
  ),
  array(
    array('r'=>255, 'g'=>0, 'b'=>255),
    '#FF00FF',
  ),
  array(
    array('r'=>192, 'g'=>192, 'b'=>192),
    '#C0C0C0',
  ),
  array(
    array('r'=>128, 'g'=>128, 'b'=>128),
    '#808080',
  ),
  array(
    array('r'=>128, 'g'=>0, 'b'=>0),
    '#800000',
  ),
  array(
    array('r'=>128, 'g'=>128, 'b'=>0),
    '#808000',
  ),
  array(
    array('r'=>0, 'g'=>128, 'b'=>0),
    '#008000',
  ),
  array(
    array('r'=>128, 'g'=>0, 'b'=>128),
    '#800080',
  ),
  array(
    array('r'=>0, 'g'=>128, 'b'=>128),
    '#008080',
  ),
  array(
    array('r'=>0, 'g'=>0, 'b'=>128),
    '#000080',
  ),
  // random generated values
  array(
    array('r'=>142, 'g'=>20, 'b'=>185),
    '#8E14B9',
  ),
  array(
    array('r'=>35, 'g'=>120, 'b'=>7),
    '#237807',
  ),
  array(
    array('r'=>214, 'g'=>25, 'b'=>177),
    '#D619B1',
  ),
  array(
    array('r'=>45, 'g'=>80, 'b'=>136),
    '#2D5088',
  ),
  array(
    array('r'=>240, 'g'=>234, 'b'=>228),
    '#F0EAE4',
  ),
  array(
    array('r'=>2, 'g'=>8, 'b'=>10),
    '#02080A',
  ),
  array(
    array('r'=>102, 'g'=>148, 'b'=>164),
    '#6694A4',
  ),
  array(
    array('r'=>200, 'g'=>100, 'b'=>0),
    '#C86400',
  ),
  array(
    array('r'=>55, 'g'=>255, 'b'=>155),
    '#37FF9B',
  ),
  array(
    array('r'=>255, 'g'=>100, 'b'=>125),
    '#FF647D',
  ),
//------------------------------------------------------------------------------
  // hsv: consulted https://www.rapidtables.com/convert/color/rgb-to-hsv.html
  // from table values
  array(
    array('r'=>0, 'g'=>0, 'b'=>0),
    array('h'=>0, 's'=>0, 'v'=>0),
  ),
  array(
    array('r'=>255, 'g'=>255, 'b'=>255),
    array('h'=>0, 's'=>0, 'v'=>100),
  ),
  array(
    array('r'=>255, 'g'=>0, 'b'=>0),
    array('h'=>0, 's'=>100, 'v'=>100),
  ),
  array(
    array('r'=>0, 'g'=>255, 'b'=>0),
    array('h'=>120, 's'=>100, 'v'=>100),
  ),
  array(
    array('r'=>0, 'g'=>0, 'b'=>255),
    array('h'=>240, 's'=>100, 'v'=>100),
  ),
  array(
    array('r'=>255, 'g'=>255, 'b'=>0),
    array('h'=>60, 's'=>100, 'v'=>100),
  ),
  array(
    array('r'=>0, 'g'=>255, 'b'=>255),
    array('h'=>180, 's'=>100, 'v'=>100),
  ),
  array(
    array('r'=>255, 'g'=>0, 'b'=>255),
    array('h'=>300, 's'=>100, 'v'=>100),
  ),
  array(
    array('r'=>192, 'g'=>192, 'b'=>192),
    array('h'=>0, 's'=>0, 'v'=>75),
  ),
  array(
    array('r'=>128, 'g'=>128, 'b'=>128),
    array('h'=>0, 's'=>0, 'v'=>50),
  ),
  array(
    array('r'=>128, 'g'=>0, 'b'=>0),
    array('h'=>0, 's'=>100, 'v'=>50),
  ),
  array(
    array('r'=>128, 'g'=>128, 'b'=>0),
    array('h'=>60, 's'=>100, 'v'=>50),
  ),
  array(
    array('r'=>0, 'g'=>128, 'b'=>0),
    array('h'=>120, 's'=>100, 'v'=>50),
  ),
  array(
    array('r'=>128, 'g'=>0, 'b'=>128),
    array('h'=>300, 's'=>100, 'v'=>50),
  ),
  array(
    array('r'=>0, 'g'=>128, 'b'=>128),
    array('h'=>180, 's'=>100, 'v'=>50),
  ),
  array(
    array('r'=>0, 'g'=>0, 'b'=>128),
    array('h'=>240, 's'=>100, 'v'=>50),
  ),
  // random generated values
  array(
    array('r'=>142, 'g'=>20, 'b'=>185),
    array('h'=>284.36, 's'=>89.19, 'v'=>72.55),
  ),
  array(
    array('r'=>35, 'g'=>120, 'b'=>7),
    array('h'=>105.13, 's'=>94.17, 'v'=>47.06),
  ),
  array(
    array('r'=>214, 'g'=>25, 'b'=>177),
    array('h'=>311.75, 's'=>88.32, 'v'=>83.92),
  ),
  array(
    array('r'=>45, 'g'=>80, 'b'=>136),
    array('h'=>216.92, 's'=>66.91, 'v'=>53.33),
  ),
  array(
    array('r'=>240, 'g'=>234, 'b'=>228),
    array('h'=>30, 's'=>5, 'v'=>94.12),
  ),
  array(
    array('r'=>2, 'g'=>8, 'b'=>10),
    array('h'=>195, 's'=>80, 'v'=>3.92),
  ),
  array(
    array('r'=>102, 'g'=>148, 'b'=>164),
    array('h'=>195.48, 's'=>37.8, 'v'=>64.31),
  ),
  array(
    array('r'=>200, 'g'=>100, 'b'=>0),
    array('h'=>30, 's'=>100, 'v'=>78.43),
  ),
  array(
    array('r'=>55, 'g'=>255, 'b'=>155),
    array('h'=>150, 's'=>78.43, 'v'=>100),
  ),
  array(
    array('r'=>255, 'g'=>100, 'b'=>125),
    array('h'=>350.32, 's'=>60.78, 'v'=>100),
  ),
//------------------------------------------------------------------------------
  // hsl: consulted https://www.rapidtables.com/convert/color/rgb-to-hsl.html
  // from table values
  array(
    array('r'=>0, 'g'=>0, 'b'=>0),
    array('h'=>0, 's'=>0, 'l'=>0),
  ),
  array(
    array('r'=>255, 'g'=>255, 'b'=>255),
    array('h'=>0, 's'=>0, 'l'=>100),
  ),
  array(
    array('r'=>255, 'g'=>0, 'b'=>0),
    array('h'=>0, 's'=>100, 'l'=>50),
  ),
  array(
    array('r'=>0, 'g'=>255, 'b'=>0),
    array('h'=>120, 's'=>100, 'l'=>50),
  ),
  array(
    array('r'=>0, 'g'=>0, 'b'=>255),
    array('h'=>240, 's'=>100, 'l'=>50),
  ),
  array(
    array('r'=>255, 'g'=>255, 'b'=>0),
    array('h'=>60, 's'=>100, 'l'=>50),
  ),
  array(
    array('r'=>0, 'g'=>255, 'b'=>255),
    array('h'=>180, 's'=>100, 'l'=>50),
  ),
  array(
    array('r'=>255, 'g'=>0, 'b'=>255),
    array('h'=>300, 's'=>100, 'l'=>50),
  ),
  array(
    array('r'=>192, 'g'=>192, 'b'=>192),
    array('h'=>0, 's'=>0, 'l'=>75),
  ),
  array(
    array('r'=>128, 'g'=>128, 'b'=>128),
    array('h'=>0, 's'=>0, 'l'=>50),
  ),
  array(
    array('r'=>128, 'g'=>0, 'b'=>0),
    array('h'=>0, 's'=>100, 'l'=>25),
  ),
  array(
    array('r'=>128, 'g'=>128, 'b'=>0),
    array('h'=>60, 's'=>100, 'l'=>25),
  ),
  array(
    array('r'=>0, 'g'=>128, 'b'=>0),
    array('h'=>120, 's'=>100, 'l'=>25),
  ),
  array(
    array('r'=>128, 'g'=>0, 'b'=>128),
    array('h'=>300, 's'=>100, 'l'=>25),
  ),
  array(
    array('r'=>0, 'g'=>128, 'b'=>128),
    array('h'=>180, 's'=>100, 'l'=>25),
  ),
  array(
    array('r'=>0, 'g'=>0, 'b'=>128),
    array('h'=>240, 's'=>100, 'l'=>25),
  ),
  // random generated values
  array(
    array('r'=>142, 'g'=>20, 'b'=>185),
    array('h'=>284.36, 's'=>80.49, 'l'=>40.2),
  ),
  array(
    array('r'=>35, 'g'=>120, 'b'=>7),
    array('h'=>105.13, 's'=>88.98, 'l'=>24.9),
  ),
  array(
    array('r'=>214, 'g'=>25, 'b'=>177),
    array('h'=>311.75, 's'=>79.08, 'l'=>46.86),
  ),
  array(
    array('r'=>45, 'g'=>80, 'b'=>136),
    array('h'=>216.92, 's'=>50.28, 'l'=>35.49),
  ),
  array(
    array('r'=>240, 'g'=>234, 'b'=>228),
    array('h'=>30, 's'=>28.57, 'l'=>91.76),
  ),
  array(
    array('r'=>2, 'g'=>8, 'b'=>10),
    array('h'=>195, 's'=>66.67, 'l'=>2.35),
  ),
  array(
    array('r'=>102, 'g'=>148, 'b'=>164),
    array('h'=>195.48, 's'=>25.41, 'l'=>52.16),
  ),
  array(
    array('r'=>200, 'g'=>100, 'b'=>0),
    array('h'=>30, 's'=>100, 'l'=>39.22),
  ),
  array(
    array('r'=>55, 'g'=>255, 'b'=>155),
    array('h'=>150, 's'=>100, 'l'=>60.78),
  ),
  array(
    array('r'=>255, 'g'=>100, 'b'=>125),
    array('h'=>350.32, 's'=>100, 'l'=>69.61),
  ),
//------------------------------------------------------------------------------
);
