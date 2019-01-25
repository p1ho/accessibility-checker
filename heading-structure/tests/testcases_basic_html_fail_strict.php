<?php

/**
 * Test cases (Fail Strict) for Heading Structure Checker (Basic HTML)
 *
 * These are just simple, relatively easy to read HTML test cases.
 * Because we assume the data we pass into Heading Structure Checker is coming
 * from Drupal, the way the testcases are written here tries to adhere to the
 * same format (assumes everything's already wrapped in <body>)
 *
 * These are cases that would fail only under strict mode.
 */

$testcases_basic_html_fail_strict = array(
//------------------------------------------------------------------------------
  new testcase(
    '
    <h3>Normal h3</h3>
    <h4>h4 not nested</h4>
    ',
    array('passed'=>FALSE,'errors'=>array(
      "heading nested too shallow: <h4> with text 'h4 not nested'."
    ))
  ),
//------------------------------------------------------------------------------
  new testcase(
    '
    <h3>Normal h3</h3>
    <div>
      <h4>Normal h4</h4>
      <div>
        <h5>Normal h5</h5>
        <div>
          <h6>Normal h6</h6>
          <div>
            <h6>h6 too deep</h6>
          </div>
        </div>
        <h6>h6 too shallow</h6>
      </div>
    </div>
    ',
    array('passed'=>FALSE,'errors'=>array(
      "heading nested too deep: <h6> with text 'h6 too deep'.",
      "heading nested too shallow: <h6> with text 'h6 too shallow'."
    ))
  ),
//------------------------------------------------------------------------------
  new testcase(
    '
    <h3>Normal h3</h3>
    <div>
      <h5>Normal h5</h5>
      <h4>Normal h4</h4>
      <h5>Normal h5-2</h5>
      <h4>Normal h4-2</h4>
      <h5>Normal h5-3</h5>
    </div>
    ',
    array('passed'=>FALSE,'errors'=>array(
      "heading skipped: <h4> is missing before <h5> with text 'Normal h5'.",
      "heading nested too shallow: <h5> with text 'Normal h5-2'.",
      "heading nested too shallow: <h5> with text 'Normal h5-3'."
    ))
  ),
);
