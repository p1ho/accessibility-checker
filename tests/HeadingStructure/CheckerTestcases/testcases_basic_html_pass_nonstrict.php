<?php declare(strict_types=1);

/**
 * Test cases (Pass) for Heading Structure Checker (Basic HTML)
 *
 * These are just simple, relatively easy to read HTML test cases.
 * Because we assume the data we pass into Heading Structure Checker is coming
 * from Drupal, the way the testcases are written here tries to adhere to the
 * same format (assumes everything's already wrapped in <body>)
 *
 * These are test cases that would only pass under non-strict mode.
 */

$testcases_basic_html_pass_nonstrict = array(
//------------------------------------------------------------------------------
  new testcase(
      '
    <h3>Normal h3</h3>
    <h4>Normal h4</h4>
    <h5>Normal h5</h5>
    <h6>Normal h6</h6>
    ',
      array('passed'=>true,'errors'=>array())
  ),
//------------------------------------------------------------------------------
  new testcase(
      '
    <h3>Normal h3</h3>
    <h4>Normal h4</h4>
    <h3>Normal h5</h3>
    <h4>Normal h4</h4>
    <h5>Normal h5</h5>
    <h6>Normal h6</h6>
    ',
      array('passed'=>true,'errors'=>array())
  ),
//------------------------------------------------------------------------------
);
