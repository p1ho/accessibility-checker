<?php declare(strict_types=1);

/**
 * Test cases (Fail) for Color Contrast Checker (Basic HTML) w/ fast fail
 *
 * These are just simple, relatively easy to read HTML test cases.
 *
 * These are general cases that should fail the Color Contrast test.
 *
 * Note: to pass test cases, the indent level of test case and
 * expected['errors']['html'] has to be the same if it spans multiple lines.
 */

$testcases_basic_html_fail_fast_fail = array(
//------------------------------------------------------------------------------
  // black text in a container with black background
  // (empty container should have error too)
  new testcase(
      '
        <div style="background-color:black">
          <p>black container</p>
        </div>
    ',
      array('passed'=>false,'errors'=>array(
      (object) [
        'type' => 'low contrast',
        'mode' => 'AA',
        'tag' => 'div',
        'text' => 'black container',
        'html' =>
        '<div style="background-color:black">
          <p>black container</p>
        </div>',
        'text_is_large' => false,
        'contrast_ratio' => '1.00',
        'recommendation' => 'Contrast Ratio for this element must be at least 4.5',
      ],
    ))
  ),
//------------------------------------------------------------------------------
  // multiple layers of transparent background (using inherit)
  new testcase(
      '
        <div style="background-color: rgba(75, 0, 0, .5)">
          first level
          <div style="background-color: inherit">
            second level
            <div style="background-color: inherit">
              third level
              <div style="background-color: rgba(0, 0, 75, .5)">
                fourth level
              </div>
            </div>
          </div>
        </div>
    ',
      array('passed'=>false,'errors'=>array(
      (object) [
        'type' => 'low contrast',
        'mode' => 'AA',
        'tag' => 'div',
        'text' => 'second level   third level   fourth level',
        'html' =>
        '<div style="background-color: inherit">
            second level
            <div style="background-color: inherit">
              third level
              <div style="background-color: rgba(0, 0, 75, .5)">
                fourth level
              </div>
            </div>
          </div>',
        'text_is_large' => false,
        'contrast_ratio' => '2.61',
        'recommendation' => 'Contrast Ratio for this element must be at least 4.5',
      ],
    ))
  ),
//------------------------------------------------------------------------------
  // testing background-color: initial;
  new testcase(
      '
        <div style="background-color: rgba(75, 0, 0, .5)">
          rgba color
          <div style="background-color: inherit">
            inherited color
            <div style="background-color: initial">
              same as parent
            </div>
          </div>
        </div>
    ',
      array('passed'=>false,'errors'=>array(
      (object) [
        'type' => 'low contrast',
        'mode' => 'AA',
        'tag' => 'div',
        'text' => 'inherited color   same as parent',
        'html' =>
        '<div style="background-color: inherit">
            inherited color
            <div style="background-color: initial">
              same as parent
            </div>
          </div>',
        'text_is_large' => false,
        'contrast_ratio' => '2.61',
        'recommendation' => 'Contrast Ratio for this element must be at least 4.5',
      ],
    ))
  ),
//------------------------------------------------------------------------------
  // transparent text on transparent background
  new testcase(
      '
        <div style="background-color: rgba(75, 0, 0, .5); color: rgba(0, 0, 75, .5)">
          first level
          <div style="background-color: inherit">
            second level
            <div style="background-color: inherit">
              third level
              <div style="background-color: rgba(0, 0, 75, .5)">
                fourth level
              </div>
            </div>
          </div>
        </div>
    ',
      array('passed'=>false,'errors'=>array(
      (object) [
        'type' => 'low contrast',
        'mode' => 'AA',
        'tag' => 'div',
        'text' => 'first level   second level   third level   fourth level',
        'html' =>
        '<div style="background-color: rgba(75, 0, 0, .5); color: rgba(0, 0, 75, .5)">
          first level
          <div style="background-color: inherit">
            second level
            <div style="background-color: inherit">
              third level
              <div style="background-color: rgba(0, 0, 75, .5)">
                fourth level
              </div>
            </div>
          </div>
        </div>',
        'text_is_large' => false,
        'contrast_ratio' => '2.62',
        'recommendation' => 'Contrast Ratio for this element must be at least 4.5',
      ],
    ))
  ),
//------------------------------------------------------------------------------
  // when contrast level only passes if text is big
  new testcase(
      '
        <div style="color:#7D7D7D;">
          <h1>large text 1</h1>
          <p style="font-size: 19px; font-weight: bold">large text 2</p>
          <p style="font-size: 14pt">small text 1
            <b>large</b>
            <strong>large</strong>
            small text 1
          </p>
          <p style="font-size: 18px; font-weight: bold">small text 2</p>
          <p>small text 3</p>
        </div>
    ',
      array('passed'=>false,'errors'=>array(
      (object) [
        'type' => 'low contrast',
        'mode' => 'AA',
        'tag' => 'div',
        'text' => 'large text 1   large text 2   small text 1 large large small text 1    small text 2   small text 3',
        'html' =>
        '<div style="color:#7D7D7D;">
          <h1>large text 1</h1>
          <p style="font-size: 19px; font-weight: bold">large text 2</p>
          <p style="font-size: 14pt">small text 1
            <b>large</b>
            <strong>large</strong>
            small text 1
          </p>
          <p style="font-size: 18px; font-weight: bold">small text 2</p>
          <p>small text 3</p>
        </div>',
        'text_is_large' => false,
        'contrast_ratio' => '4.12',
        'recommendation' => 'Contrast Ratio for this element must be at least 4.5',
      ],
    ))
  ),
//------------------------------------------------------------------------------
  // contrast level only passes if text is big (testing h1-h6)
  new testcase(
      '
        <div style="color:#7D7D7D;">
          <h1>h1</h1>
          <h2>h2</h2>
          <h3>h3</h3>
          <h4>h4</h4>
          <h5>h5</h5>
          <h6>h6</h6>
        </div>
    ',
      array('passed'=>false,'errors'=>array(
      (object) [
        'type' => 'low contrast',
        'mode' => 'AA',
        'tag' => 'div',
        'text' => 'h1   h2   h3   h4   h5   h6',
        'html' => '<div style="color:#7D7D7D;">
          <h1>h1</h1>
          <h2>h2</h2>
          <h3>h3</h3>
          <h4>h4</h4>
          <h5>h5</h5>
          <h6>h6</h6>
        </div>',
        'text_is_large' => false,
        'contrast_ratio' => '4.12',
        'recommendation' => 'Contrast Ratio for this element must be at least 4.5',
      ],
    ))
  ),
//------------------------------------------------------------------------------
  // contrast level only passes if text is big (testing h1 in nested sections)
  new testcase(
      '
        <h1 style="color:#7D7D7D;">h1 level 1</h1>
        <section>
          <h1 style="color:#7D7D7D;">h1 level 2</h1>
          <section>
            <h1 style="color:#7D7D7D;">h1 level 3</h1>
            <section>
              <h1 style="color:#7D7D7D;">h1 level 4</h1>
              <section>
                <h1 style="color:#7D7D7D;">h1 level 5</h1>
                <section>
                  <h1 style="color:#7D7D7D;">h1 level 6</h1>
                </section>
              </section>
            </section>
          </section>
        </section>
    ',
      array('passed'=>false,'errors'=>array(
      (object) [
        'type' => 'low contrast',
        'mode' => 'AA',
        'tag' => 'h1',
        'text' => 'h1 level 4',
        'html' => '<h1 style="color:#7D7D7D;">h1 level 4</h1>',
        'text_is_large' => false,
        'contrast_ratio' => '4.12',
        'recommendation' => 'Contrast Ratio for this element must be at least 4.5',
      ],
      (object) [
        'type' => 'low contrast',
        'mode' => 'AA',
        'tag' => 'h1',
        'text' => 'h1 level 5',
        'html' => '<h1 style="color:#7D7D7D;">h1 level 5</h1>',
        'text_is_large' => false,
        'contrast_ratio' => '4.12',
        'recommendation' => 'Contrast Ratio for this element must be at least 4.5',
      ],
      (object) [
        'type' => 'low contrast',
        'mode' => 'AA',
        'tag' => 'h1',
        'text' => 'h1 level 6',
        'html' => '<h1 style="color:#7D7D7D;">h1 level 6</h1>',
        'text_is_large' => false,
        'contrast_ratio' => '4.12',
        'recommendation' => 'Contrast Ratio for this element must be at least 4.5',
      ],
    ))
  ),
//------------------------------------------------------------------------------
  // set background color to initial should inherit from parents
  new testcase(
      '
        <div style="background-color: black;">
          <h1 style="background-color: initial;">h1</h1>
        </div>
    ',
      array('passed'=>false,'errors'=>array(
      (object) [
        'type' => 'low contrast',
        'mode' => 'AA',
        'tag' => 'div',
        'text' => 'h1',
        'html' =>
        '<div style="background-color: black;">
          <h1 style="background-color: initial;">h1</h1>
        </div>',
        'text_is_large' => false,
        'contrast_ratio' => '1.00',
        'recommendation' => 'Contrast Ratio for this element must be at least 4.5',
      ],
    ))
  ),
//------------------------------------------------------------------------------
);
