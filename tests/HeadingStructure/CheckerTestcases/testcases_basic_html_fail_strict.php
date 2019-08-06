<?php declare(strict_types=1);

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
      array('passed'=>false,'errors'=>array(
      (object) [
        'type' => 'heading too shallow',
        'tag' => 'h4',
        'text' => 'h4 not nested',
        'html' => '<h4>h4 not nested</h4>',
        'recommendation' => "Try nesting this heading deeper.",
      ],
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
      array('passed'=>false,'errors'=>array(
      (object) [
        'type' => 'heading too deep',
        'tag' => 'h6',
        'text' => 'h6 too deep',
        'html' => '<h6>h6 too deep</h6>',
        'recommendation' => "Try nesting this heading shallower.",
      ],
      (object) [
        'type' => 'heading too shallow',
        'tag' => 'h6',
        'text' => 'h6 too shallow',
        'html' => '<h6>h6 too shallow</h6>',
        'recommendation' => "Try nesting this heading deeper.",
      ],
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
      array('passed'=>false,'errors'=>array(
      (object) [
        'type' => 'heading skipped',
        'tag' => 'h5',
        'text' => 'Normal h5',
        'html' => '<h5>Normal h5</h5>',
        'recommendation' => "<h4> is expected before the placement of this heading.",
      ],
      (object) [
        'type' => 'heading too shallow',
        'tag' => 'h5',
        'text' => 'Normal h5-2',
        'html' => '<h5>Normal h5-2</h5>',
        'recommendation' => "Try nesting this heading deeper.",
      ],
      (object) [
        'type' => 'heading too shallow',
        'tag' => 'h5',
        'text' => 'Normal h5-3',
        'html' => '<h5>Normal h5-3</h5>',
        'recommendation' => "Try nesting this heading deeper.",
      ],
    ))
  ),
);
