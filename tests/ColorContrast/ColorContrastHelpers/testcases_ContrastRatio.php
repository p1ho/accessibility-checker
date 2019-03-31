<?php

/**
 * Test cases for some contrast ratios.
 */

$testcases_ContrastRatio = array(
  // from table values from https://www.rapidtables.com/convert/color/rgb-to-hex.html
  new testcase(
    array(
      '#000000',
      '#949494',
    ),
    6.92
  ),
  new testcase(
    array(
      '#FFFFFF',
      '#6E6E6E',
    ),
    5.10
  ),
  new testcase(
    array(
      '#FF0000',
      '#538A79',
    ),
    1.01
  ),
  new testcase(
    array(
      '#00FF00',
      '#A649C5',
    ),
    3.49
  ),
  new testcase(
    array(
      '#0000FF',
      '#7C8235',
    ),
    2.09
  ),
  new testcase(
    array(
      '#FFFF00',
      '#B4AEC4',
    ),
    2.00
  ),
  new testcase(
    array(
      '#00FFFF',
      '#C96530',
    ),
    3.11
  ),
  new testcase(
    array(
      '#FF00FF',
      '#1C1C1C',
    ),
    5.43
  ),
  new testcase(
    array(
      '#C0C0C0',
      '#1B6F6F',
    ),
    3.25
  ),
  new testcase(
    array(
      '#808080',
      '#B11F61',
    ),
    1.64
  ),
  new testcase(
    array(
      '#800000',
      '#D3BAD9',
    ),
    6.15
  ),
  new testcase(
    array(
      '#808000',
      '#EE6265',
    ),
    1.31
  ),
  new testcase(
    array(
      '#008000',
      '#5B633D',
    ),
    1.24
  ),
  new testcase(
    array(
      '#800080',
      '#E9ECDF',
    ),
    7.86
  ),
  new testcase(
    array(
      '#008080',
      '#F4D7DE',
    ),
    3.55
  ),
  new testcase(
    array(
      '#000080',
      '#7A5414',
    ),
    2.37
  ),
  // random generated values
  new testcase(
    array(
      '#8E14B9',
      '#346213',
    ),
    1.02
  ),
  new testcase(
    array(
      '#237807',
      '#E1F7CF',
    ),
    4.89
  ),
  new testcase(
    array(
      '#D619B1',
      '#35058D',
    ),
    2.97
  ),
  new testcase(
    array(
      '#2D5088',
      '#7B8112',
    ),
    1.91
  ),
  new testcase(
    array(
      '#F0EAE4',
      '#F518C9',
    ),
    3.00
  ),
  new testcase(
    array(
      '#02080A',
      '#C94548',
    ),
    4.25
  ),
  new testcase(
    array(
      '#7894A4',
      '#EB5C92',
    ),
    1.01
  ),
  new testcase(
    array(
      '#C86400',
      '#FFFFFF',
    ),
    3.98
  ),
  new testcase(
    array(
      '#37FF9B',
      '#000000',
    ),
    15.94
  ),
  new testcase(
    array(
      '#FF647D',
      '#707070',
    ),
    1.74
  ),
);
