<?php declare(strict_types=1);

/**
 * Test cases (Fail) for Heading Structure Checker (Basic HTML)
 *
 * These are just simple, relatively easy to read HTML test cases.
 * Because we assume the data we pass into Heading Structure Checker is coming
 * from Drupal, the way the testcases are written here tries to adhere to the
 * same format (assumes everything's already wrapped in <body>)
 *
 * These are general cases that should fail for both strict/non-strict modes
 */

$testcases_basic_html_fail = array(
//------------------------------------------------------------------------------
  new testcase(
      '
        <h7>Invalid heading</h7>
    ',
      array('passed'=>false,'errors'=>array(
      (object) [
        'type' => 'invalid heading',
        'tag' => 'h7',
        'text' => 'Invalid heading',
        'html' => '<h7>Invalid heading</h7>',
        'recommendation' => "Use valid headings only (<h1> through <h6>).",
      ],
    ))
  ),
//------------------------------------------------------------------------------
  new testcase(
      '
        <h111>Invalid heading</h111>
    ',
      array('passed'=>false,'errors'=>array(
      (object) [
        'type' => 'invalid heading',
        'tag' => 'h111',
        'text' => 'Invalid heading',
        'html' => '<h111>Invalid heading</h111>',
        'recommendation' => "Use valid headings only (<h1> through <h6>).",
      ],
    ))
  ),
//------------------------------------------------------------------------------
  new testcase(
      '
    <h1>Unallowed h1</h1>
    ',
      array('passed'=>false,'errors'=>array(
      (object) [
        'type' => 'heading unallowed',
        'tag' => 'h1',
        'text' => 'Unallowed h1',
        'html' => '<h1>Unallowed h1</h1>',
        'recommendation' => 'Use allowed heading (<h3> to <h6>).',
      ],
    ))
  ),
//------------------------------------------------------------------------------
  new testcase(
      '
        <h6>Skipped to h6</h6>
    ',
      array('passed'=>false,'errors'=>array(
      (object) [
        'type' => 'heading skipped',
        'tag' => 'h6',
        'text' => 'Skipped to h6',
        'html' => '<h6>Skipped to h6</h6>',
        'recommendation' => '<h3> is expected before the placement of this heading.',
      ],
      (object) [
        'type' => 'heading skipped',
        'tag' => 'h6',
        'text' => 'Skipped to h6',
        'html' => '<h6>Skipped to h6</h6>',
        'recommendation' => '<h4> is expected before the placement of this heading.',
      ],
      (object) [
        'type' => 'heading skipped',
        'tag' => 'h6',
        'text' => 'Skipped to h6',
        'html' => '<h6>Skipped to h6</h6>',
        'recommendation' => '<h5> is expected before the placement of this heading.',
      ],
    ))
  ),
//------------------------------------------------------------------------------
  new testcase(
      '
        <h3>This is h3
          <h4>h4 wrapped in h3</h4>
        </h3>
    ',
      array('passed'=>false,'errors'=>array(
      (object) [
        'type' => 'heading inside heading',
        'tag' => 'h4',
        'text' => 'h4 wrapped in h3',
        'html' => '<h4>h4 wrapped in h3</h4>',
        'recommendation' => 'Do not put heading inside another heading.',
      ],
    ))
  ),
//------------------------------------------------------------------------------
  new testcase(
      '
        <div>
          <h4>h4 before h3</h4>
        </div>
        <h3>Normal h3</h3>
    ',
      array('passed'=>false,'errors'=>array(
      (object) [
        'type' => 'heading skipped',
        'tag' => 'h4',
        'text' => 'h4 before h3',
        'html' => '<h4>h4 before h3</h4>',
        'recommendation' => '<h3> is expected before the placement of this heading.',
      ],
    ))
  ),
//------------------------------------------------------------------------------
  // inconsistent heading nesting level: too deep
  new testcase(
      '
        <h3>Normal h3</h3>
        <div>
          <h4>Normal h4</h4>
        </div>
        <div>
          <div>
            <h4>h4 Nested Too Deep</h4>
          </div>
        </div>
    ',
      array('passed'=>false,'errors'=>array(
      (object) [
        'type' => 'heading too deep',
        'tag' => 'h4',
        'text' => 'h4 Nested Too Deep',
        'html' => '<h4>h4 Nested Too Deep</h4>',
        'recommendation' => 'Try nesting this heading shallower.',
      ],
    ))
  ),
//------------------------------------------------------------------------------
  // following last case, normal headings nested at that level won't raise errors
  new testcase(
      '
        <h3>Normal h3</h3>
        <div>
          <h4>Normal h4</h4>
        </div>
        <div>
          <div>
            <h4>h4 Nested Too Deep</h4>
          </div>
        </div>
        <div>
          <div>
            <h5>Normal h5</h5>
          </div>
        </div>
    ',
      array('passed'=>false,'errors'=>array(
      (object) [
        'type' => 'heading too deep',
        'tag' => 'h4',
        'text' => 'h4 Nested Too Deep',
        'html' => '<h4>h4 Nested Too Deep</h4>',
        'recommendation' => 'Try nesting this heading shallower.',
      ],
    ))
  ),
//------------------------------------------------------------------------------
  // inconsistent heading nesting level: too shallow
  new testcase(
      '
        <h3>Normal h3</h3>
        <div>
          <h4>Normal h4</h4>
        </div>
        <h4>h4 nested shallow</h4>
        <div>
          <div>
            <h5>Normal h5</h5>
          </div>
        </div>
    ',
      array('passed'=>false,'errors'=>array(
      (object) [
        'type' => 'heading too shallow',
        'tag' => 'h4',
        'text' => 'h4 nested shallow',
        'html' => '<h4>h4 nested shallow</h4>',
        'recommendation' => 'Try nesting this heading deeper.',
      ],
    ))
  ),
//------------------------------------------------------------------------------
  // skip to h6 should raise error for each heading missed
  new testcase(
      '
        <h3>Normal h3</h3>
        <div>
          <h6>Skip to h6</h6>
        </div>
    ',
      array('passed'=>false,'errors'=>array(
      (object) [
        'type' => 'heading skipped',
        'tag' => 'h6',
        'text' => 'Skip to h6',
        'html' => '<h6>Skip to h6</h6>',
        'recommendation' => '<h4> is expected before the placement of this heading.',
      ],
      (object) [
        'type' => 'heading skipped',
        'tag' => 'h6',
        'text' => 'Skip to h6',
        'html' => '<h6>Skip to h6</h6>',
        'recommendation' => '<h5> is expected before the placement of this heading.',
      ],
    ))
  ),
//------------------------------------------------------------------------------
  // The skip to second h6 should yield fewer messages because h3 is defined
  // in the second one
  new testcase(
      '
        <h6>Skip to h6</h6>
        <h3>Normal h3</h3>
        <h6>Skip to h6-2</h6>
    ',
      array('passed'=>false,'errors'=>array(
      (object) [
        'type' => 'heading skipped',
        'tag' => 'h6',
        'text' => 'Skip to h6',
        'html' => '<h6>Skip to h6</h6>',
        'recommendation' => '<h3> is expected before the placement of this heading.',
      ],
      (object) [
        'type' => 'heading skipped',
        'tag' => 'h6',
        'text' => 'Skip to h6',
        'html' => '<h6>Skip to h6</h6>',
        'recommendation' => '<h4> is expected before the placement of this heading.',
      ],
      (object) [
        'type' => 'heading skipped',
        'tag' => 'h6',
        'text' => 'Skip to h6',
        'html' => '<h6>Skip to h6</h6>',
        'recommendation' => '<h5> is expected before the placement of this heading.',
      ],
      (object) [
        'type' => 'heading skipped',
        'tag' => 'h6',
        'text' => 'Skip to h6-2',
        'html' => '<h6>Skip to h6-2</h6>',
        'recommendation' => '<h4> is expected before the placement of this heading.',
      ],
      (object) [
        'type' => 'heading skipped',
        'tag' => 'h6',
        'text' => 'Skip to h6-2',
        'html' => '<h6>Skip to h6-2</h6>',
        'recommendation' => '<h5> is expected before the placement of this heading.',
      ],
    ))
  ),
);
