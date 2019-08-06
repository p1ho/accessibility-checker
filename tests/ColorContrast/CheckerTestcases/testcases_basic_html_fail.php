<?php declare(strict_types=1);

/**
 * Test cases (Fail) for Color Contrast Checker (Basic HTML)
 *
 * These are just simple, relatively easy to read HTML test cases.
 *
 * These are general cases that should fail the Color Contrast test.
 *
 * Note: to pass test cases, the indent level of test case and
 * expected['errors']['html'] has to be the same if it spans multiple lines.
 */

$testcases_basic_html_fail = array(
//------------------------------------------------------------------------------
  // white text
  new testcase(
      '
        <p style="color: white;">white text</p>
    ',
      array('passed'=>false,'errors'=>array(
      (object) [
        'type' => 'low contrast',
        'mode' => 'AA',
        'tag' => 'p',
        'text' => 'white text',
        'html' => '<p style="color: white;">white text</p>',
        'text_is_large' => false,
        'contrast_ratio' => '1.00',
        'recommendation' => 'Contrast Ratio for this element must be at least 4.5',
      ],
    ))
  ),
//------------------------------------------------------------------------------
  // black text on black background
  new testcase(
      '
        <p style="background-color:black">black background</p>
    ',
      array('passed'=>false,'errors'=>array(
      (object) [
        'type' => 'low contrast',
        'mode' => 'AA',
        'tag' => 'p',
        'text' => 'black background',
        'html' => '<p style="background-color:black">black background</p>',
        'text_is_large' => false,
        'contrast_ratio' => '1.00',
        'recommendation' => 'Contrast Ratio for this element must be at least 4.5',
      ],
    ))
  ),
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
      (object) [
        'type' => 'low contrast',
        'mode' => 'AA',
        'tag' => 'p',
        'text' => 'black container',
        'html' => '<p>black container</p>',
        'text_is_large' => false,
        'contrast_ratio' => '1.00',
        'recommendation' => 'Contrast Ratio for this element must be at least 4.5',
      ],
    ))
  ),
//------------------------------------------------------------------------------
  // white text on white background
  new testcase(
      '
        <p style="color:white">white text</p>
    ',
      array('passed'=>false,'errors'=>array(
      (object) [
        'type' => 'low contrast',
        'mode' => 'AA',
        'tag' => 'p',
        'text' => 'white text',
        'html' => '<p style="color:white">white text</p>',
        'text_is_large' => false,
        'contrast_ratio' => '1.00',
        'recommendation' => 'Contrast Ratio for this element must be at least 4.5',
      ],
    ))
  ),
//------------------------------------------------------------------------------
  // yellow text on white background
  new testcase(
      '
        <p style="color:yellow">yellow text</p>
    ',
      array('passed'=>false,'errors'=>array(
      (object) [
        'type' => 'low contrast',
        'mode' => 'AA',
        'tag' => 'p',
        'text' => 'yellow text',
        'html' => '<p style="color:yellow">yellow text</p>',
        'text_is_large' => false,
        'contrast_ratio' => '1.07',
        'recommendation' => 'Contrast Ratio for this element must be at least 4.5',
      ],
    ))
  ),
//------------------------------------------------------------------------------
  // <mark> should override color contrast of parent
  new testcase(
      '
        <p style="color: yellow;">outside mark<mark>in mark</mark></p>
    ',
      array('passed'=>false,'errors'=>array(
      (object) [
        'type' => 'low contrast',
        'mode' => 'AA',
        'tag' => 'p',
        'text' => 'outside markin mark',
        'html' => '<p style="color: yellow;">outside mark<mark>in mark</mark></p>',
        'text_is_large' => false,
        'contrast_ratio' => '1.07',
        'recommendation' => 'Contrast Ratio for this element must be at least 4.5',
      ],
    ))
  ),
//------------------------------------------------------------------------------
  // <mark> with overriden to black should throw contrast error
  new testcase(
      '
        <p><mark style="background-color: black;">in mark</mark></p>
    ',
      array('passed'=>false,'errors'=>array(
      (object) [
        'type' => 'low contrast',
        'mode' => 'AA',
        'tag' => 'mark',
        'text' => 'in mark',
        'html' => '<mark style="background-color: black;">in mark</mark>',
        'text_is_large' => false,
        'contrast_ratio' => '1.00',
        'recommendation' => 'Contrast Ratio for this element must be at least 4.5',
      ],
    ))
  ),
//------------------------------------------------------------------------------
  // <mark> with yellow text color
  new testcase(
      '
        <mark><span style="color: yellow;">yellow text</span></mark>
    ',
      array('passed'=>false,'errors'=>array(
      (object) [
        'type' => 'low contrast',
        'mode' => 'AA',
        'tag' => 'span',
        'text' => 'yellow text',
        'html' => '<span style="color: yellow;">yellow text</span>',
        'text_is_large' => false,
        'contrast_ratio' => '1.00',
        'recommendation' => 'Contrast Ratio for this element must be at least 4.5',
      ],
    ))
  ),
//------------------------------------------------------------------------------
  // <a> with blue background
  new testcase(
      '
        <a href="" style="background-color: blue;">link</a>
    ',
      array('passed'=>false,'errors'=>array(
      (object) [
        'type' => 'low contrast',
        'mode' => 'AA',
        'tag' => 'a',
        'text' => 'link',
        'html' => '<a href="" style="background-color: blue;">link</a>',
        'text_is_large' => false,
        'contrast_ratio' => '1.00',
        'recommendation' => 'Contrast Ratio for this element must be at least 4.5',
      ],
    ))
  ),
//------------------------------------------------------------------------------
  // <a> with white text (see if color override works)
  new testcase(
      '
        <a style="color: white;">link</a>
    ',
      array('passed'=>false,'errors'=>array(
      (object) [
        'type' => 'low contrast',
        'mode' => 'AA',
        'tag' => 'a',
        'text' => 'link',
        'html' => '<a style="color: white;">link</a>',
        'text_is_large' => false,
        'contrast_ratio' => '1.00',
        'recommendation' => 'Contrast Ratio for this element must be at least 4.5',
      ],
    ))
  ),
//------------------------------------------------------------------------------
  // transparent text
  new testcase(
      '
        <p style="color: rgba(0, 0, 0, 0);">transparent text</p>
    ',
      array('passed'=>false,'errors'=>array(
      (object) [
        'type' => 'low contrast',
        'mode' => 'AA',
        'tag' => 'p',
        'text' => 'transparent text',
        'html' => '<p style="color: rgba(0, 0, 0, 0);">transparent text</p>',
        'text_is_large' => false,
        'contrast_ratio' => '1.00',
        'recommendation' => 'Contrast Ratio for this element must be at least 4.5',
      ],
    ))
  ),
//------------------------------------------------------------------------------
  // transparent background with white background
  new testcase(
      '
        <p style="background-color: rgba(0, 0, 0, .8);">transparent background</p>
    ',
      array('passed'=>false,'errors'=>array(
      (object) [
        'type' => 'low contrast',
        'mode' => 'AA',
        'tag' => 'p',
        'text' => 'transparent background',
        'html' => '<p style="background-color: rgba(0, 0, 0, .8);">transparent background</p>',
        'text_is_large' => false,
        'contrast_ratio' => '1.66',
        'recommendation' => 'Contrast Ratio for this element must be at least 4.5',
      ],
    ))
  ),
//------------------------------------------------------------------------------
  // transparent background with <mark> background
  new testcase(
      '
        <mark><span style="background-color: rgba(0, 0, 0, .7);">transparent background</span></mark>
    ',
      array('passed'=>false,'errors'=>array(
      (object) [
        'type' => 'low contrast',
        'mode' => 'AA',
        'tag' => 'span',
        'text' => 'transparent background',
        'html' => '<span style="background-color: rgba(0, 0, 0, .7);">transparent background</span>',
        'text_is_large' => false,
        'contrast_ratio' => '2.38',
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
      (object) [
        'type' => 'low contrast',
        'mode' => 'AA',
        'tag' => 'div',
        'text' => 'third level   fourth level',
        'html' =>
        '<div style="background-color: inherit">
              third level
              <div style="background-color: rgba(0, 0, 75, .5)">
                fourth level
              </div>
            </div>',
        'text_is_large' => false,
        'contrast_ratio' => '1.75',
        'recommendation' => 'Contrast Ratio for this element must be at least 4.5',
      ],
      (object) [
        'type' => 'low contrast',
        'mode' => 'AA',
        'tag' => 'div',
        'text' => 'fourth level',
        'html' =>
        '<div style="background-color: rgba(0, 0, 75, .5)">
                fourth level
              </div>',
        'text_is_large' => false,
        'contrast_ratio' => '1.26',
        'recommendation' => 'Contrast Ratio for this element must be at least 4.5',
      ],
    ))
  ),
//------------------------------------------------------------------------------
  // testing backgroudn-color: initial;
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
      (object) [
        'type' => 'low contrast',
        'mode' => 'AA',
        'tag' => 'div',
        'text' => 'same as parent',
        'html' =>
        '<div style="background-color: initial">
              same as parent
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
        'contrast_ratio' => '1.75',
        'recommendation' => 'Contrast Ratio for this element must be at least 4.5',
      ],
      (object) [
        'type' => 'low contrast',
        'mode' => 'AA',
        'tag' => 'div',
        'text' => 'third level   fourth level',
        'html' =>
        '<div style="background-color: inherit">
              third level
              <div style="background-color: rgba(0, 0, 75, .5)">
                fourth level
              </div>
            </div>',
        'text_is_large' => false,
        'contrast_ratio' => '1.39',
        'recommendation' => 'Contrast Ratio for this element must be at least 4.5',
      ],
      (object) [
        'type' => 'low contrast',
        'mode' => 'AA',
        'tag' => 'div',
        'text' => 'fourth level',
        'html' =>
        '<div style="background-color: rgba(0, 0, 75, .5)">
                fourth level
              </div>',
        'text_is_large' => false,
        'contrast_ratio' => '1.09',
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
      (object) [
        'type' => 'low contrast',
        'mode' => 'AA',
        'tag' => 'p',
        'text' => 'small text 1 large large small text 1',
        'html' =>
        '<p style="font-size: 14pt">small text 1
            <b>large</b>
            <strong>large</strong>
            small text 1
          </p>',
        'text_is_large' => false,
        'contrast_ratio' => '4.12',
        'recommendation' => 'Contrast Ratio for this element must be at least 4.5',
      ],
      (object) [
        'type' => 'low contrast',
        'mode' => 'AA',
        'tag' => 'p',
        'text' => 'small text 2',
        'html' => '<p style="font-size: 18px; font-weight: bold">small text 2</p>',
        'text_is_large' => false,
        'contrast_ratio' => '4.12',
        'recommendation' => 'Contrast Ratio for this element must be at least 4.5',
      ],
      (object) [
        'type' => 'low contrast',
        'mode' => 'AA',
        'tag' => 'p',
        'text' => 'small text 3',
        'html' => '<p>small text 3</p>',
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
      (object) [
        'type' => 'low contrast',
        'mode' => 'AA',
        'tag' => 'h4',
        'text' => 'h4',
        'html' => '<h4>h4</h4>',
        'text_is_large' => false,
        'contrast_ratio' => '4.12',
        'recommendation' => 'Contrast Ratio for this element must be at least 4.5',
      ],
      (object) [
        'type' => 'low contrast',
        'mode' => 'AA',
        'tag' => 'h5',
        'text' => 'h5',
        'html' => '<h5>h5</h5>',
        'text_is_large' => false,
        'contrast_ratio' => '4.12',
        'recommendation' => 'Contrast Ratio for this element must be at least 4.5',
      ],
      (object) [
        'type' => 'low contrast',
        'mode' => 'AA',
        'tag' => 'h6',
        'text' => 'h6',
        'html' => '<h6>h6</h6>',
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
      (object) [
        'type' => 'low contrast',
        'mode' => 'AA',
        'tag' => 'h1',
        'text' => 'h1',
        'html' => '<h1 style="background-color: initial;">h1</h1>',
        'text_is_large' => true,
        'contrast_ratio' => '1.00',
        'recommendation' => 'Contrast Ratio for this element must be at least 3.0',
      ],
    ))
  ),
//------------------------------------------------------------------------------
  // set color to initial should change text back to black
  new testcase(
      '
        <div style="color: white;">
          <h1 style="color: initial;">h1</h1>
        </div>
    ',
      array('passed'=>false,'errors'=>array(
      (object) [
        'type' => 'low contrast',
        'mode' => 'AA',
        'tag' => 'div',
        'text' => 'h1',
        'html' =>
        '<div style="color: white;">
          <h1 style="color: initial;">h1</h1>
        </div>',
        'text_is_large' => false,
        'contrast_ratio' => '1.00',
        'recommendation' => 'Contrast Ratio for this element must be at least 4.5',
      ],
    ))
  ),
//------------------------------------------------------------------------------
  // invalid background color and invalid color
  new testcase(
      '
        <p style="background-color: reddd; color:reddd">invalid colors</p>
    ',
      array('passed'=>false,'errors'=>array(
      (object) [
        'type' => 'invalid color',
        'property' => 'background-color',
        'tag' => 'p',
        'text' => 'invalid colors',
        'html' => '<p style="background-color: reddd; color:reddd">invalid colors</p>',
        'recommendation' => 'Fix the invalid background-color.',
      ],
      (object) [
        'type' => 'invalid color',
        'property' => 'color',
        'tag' => 'p',
        'text' => 'invalid colors',
        'html' => '<p style="background-color: reddd; color:reddd">invalid colors</p>',
        'recommendation' => 'Fix the invalid color.',
      ],
    ))
  ),
//------------------------------------------------------------------------------
  // invalid font size
  new testcase(
      '
        <p style="font-size: 12ppp">invalid font-size 1</p>
        <p style="font-size: -12px">invalid font-size 2</p>
    ',
      array('passed'=>false,'errors'=>array(
      (object) [
        'type' => 'invalid size',
        'property' => 'font-size',
        'tag' => 'p',
        'text' => 'invalid font-size 1',
        'html' => '<p style="font-size: 12ppp">invalid font-size 1</p>',
        'recommendation' => 'Fix the invalid font-size.',
      ],
      (object) [
        'type' => 'invalid size',
        'property' => 'font-size',
        'tag' => 'p',
        'text' => 'invalid font-size 2',
        'html' => '<p style="font-size: -12px">invalid font-size 2</p>',
        'recommendation' => 'Fix the invalid font-size.',
      ],
    ))
  ),
//------------------------------------------------------------------------------
  // invalid font weight
  new testcase(
      '
        <p style="font-weight: ppppp">invalid font-weight 1</p>
        <p style="font-weight: 1100">invalid font-weight 2</p>
        <p style="font-weight: -1">invalid font-weight 3</p>
        <p style="font-weight: bold">valid font-weight 1</p>
        <p style="font-weight: 700">valid font-weight 2</p>
    ',
      array('passed'=>false,'errors'=>array(
      (object) [
        'type' => 'invalid weight',
        'property' => 'font-weight',
        'tag' => 'p',
        'text' => 'invalid font-weight 1',
        'html' => '<p style="font-weight: ppppp">invalid font-weight 1</p>',
        'recommendation' => 'Fix the invalid font-weight.',
      ],
      (object) [
        'type' => 'invalid weight',
        'property' => 'font-weight',
        'tag' => 'p',
        'text' => 'invalid font-weight 2',
        'html' => '<p style="font-weight: 1100">invalid font-weight 2</p>',
        'recommendation' => 'Fix the invalid font-weight.',
      ],
      (object) [
        'type' => 'invalid weight',
        'property' => 'font-weight',
        'tag' => 'p',
        'text' => 'invalid font-weight 3',
        'html' => '<p style="font-weight: -1">invalid font-weight 3</p>',
        'recommendation' => 'Fix the invalid font-weight.',
      ],
    ))
  ),
//------------------------------------------------------------------------------
);
