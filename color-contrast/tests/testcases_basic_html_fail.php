<?php

/**
 * Test cases (Fail) for Color Contrast Checker (Basic HTML)
 *
 * These are just simple, relatively easy to read HTML test cases.
 *
 * These are general cases that should fail the Color Contrast test.
 */

$testcases_basic_html_fail = array(
//------------------------------------------------------------------------------
  // white text
  new testcase(
    '
    <p style="color: white;">white text</p>
    ',
    array('passed'=>False,'errors'=>array(
      "Low Contrast(AA): <p> with text 'white text'. (contrast ratio: 1.00)"
    ))
  ),
//------------------------------------------------------------------------------
  // black text on black background
  new testcase(
    '
    <p style="background-color:black">black background</p>
    ',
    array('passed'=>False,'errors'=>array(
      "Low Contrast(AA): <p> with text 'black background'. (contrast ratio: 1.00)"
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
    array('passed'=>False,'errors'=>array(
      "Low Contrast(AA): <div> with text '<p>...</p>'. (contrast ratio: 1.00)",
      "Low Contrast(AA): <p> with text 'black container'. (contrast ratio: 1.00)"
    ))
  ),
//------------------------------------------------------------------------------
  // white text on white background
  new testcase(
    '
    <p style="color:white">white text</p>
    ',
    array('passed'=>False,'errors'=>array(
      "Low Contrast(AA): <p> with text 'white text'. (contrast ratio: 1.00)"
    ))
  ),
//------------------------------------------------------------------------------
  // yellow text on white background
  new testcase(
    '
    <p style="color:yellow">yellow text</p>
    ',
    array('passed'=>False,'errors'=>array(
      "Low Contrast(AA): <p> with text 'yellow text'. (contrast ratio: 1.07)"
    ))
  ),
//------------------------------------------------------------------------------
  // <mark> should override color contrast of parent
  new testcase(
    '
    <p style="color: yellow;">outside mark<mark>in mark</mark></p>
    ',
    array('passed'=>False,'errors'=>array(
      "Low Contrast(AA): <p> with text 'outside mark<mark...'. (contrast ratio: 1.07)",
    ))
  ),
//------------------------------------------------------------------------------
  // <mark> with overriden to black should throw contrast error
  new testcase(
    '
    <p><mark style="background-color: black;">in mark</mark></p>
    ',
    array('passed'=>False,'errors'=>array(
      "Low Contrast(AA): <mark> with text 'in mark'. (contrast ratio: 1.00)",
    ))
  ),
//------------------------------------------------------------------------------
  // <mark> with yellow text color
  new testcase(
    '
    <mark><span style="color: yellow;">yellow text</span></mark>
    ',
    array('passed'=>False,'errors'=>array(
      "Low Contrast(AA): <span> with text 'yellow text'. (contrast ratio: 1.00)",
    ))
  ),
//------------------------------------------------------------------------------
  // <a> with blue background
  new testcase(
    '
    <a href="" style="background-color: blue;">link</a>
    ',
    array('passed'=>False,'errors'=>array(
      "Low Contrast(AA): <a> with text 'link'. (contrast ratio: 1.00)",
    ))
  ),
//------------------------------------------------------------------------------
  // <a> with white text (see if color override works)
  new testcase(
    '
    <a style="color: white;">link</a>
    ',
    array('passed'=>False,'errors'=>array(
      "Low Contrast(AA): <a> with text 'link'. (contrast ratio: 1.00)",
    ))
  ),
//------------------------------------------------------------------------------
  // transparent text
  new testcase(
    '
    <p style="color: rgba(0, 0, 0, 0);">transparent text</p>
    ',
    array('passed'=>False,'errors'=>array(
      "Low Contrast(AA): <p> with text 'transparent text'. (contrast ratio: 1.00)"
    ))
  ),
//------------------------------------------------------------------------------
  // transparent background with white background
  new testcase(
    '
    <p style="background-color: rgba(0, 0, 0, .8);">transparent background</p>
    ',
    array('passed'=>False,'errors'=>array(
      "Low Contrast(AA): <p> with text 'transparent backg...'. (contrast ratio: 1.66)"
    ))
  ),
//------------------------------------------------------------------------------
  // transparent background with <mark> background
  new testcase(
    '
    <mark><span style="background-color: rgba(0, 0, 0, .7);">transparent background</span></mark>
    ',
    array('passed'=>False,'errors'=>array(
      "Low Contrast(AA): <span> with text 'transparent backg...'. (contrast ratio: 2.38)"
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
    array('passed'=>False,'errors'=>array(
      "Low Contrast(AA): <div> with text 'second level<div>...'. (contrast ratio: 2.61)",
      "Low Contrast(AA): <div> with text 'third level<div>....'. (contrast ratio: 1.75)",
      "Low Contrast(AA): <div> with text 'fourth level'. (contrast ratio: 1.26)",
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
    array('passed'=>False,'errors'=>array(
      "Low Contrast(AA): <div> with text 'inherited color<d...'. (contrast ratio: 2.61)",
      "Low Contrast(AA): <div> with text 'same as parent'. (contrast ratio: 2.61)",
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
    array('passed'=>False,'errors'=>array(
      "Low Contrast(AA): <div> with text 'first level<div>....'. (contrast ratio: 2.62)",
      "Low Contrast(AA): <div> with text 'second level<div>...'. (contrast ratio: 1.75)",
      "Low Contrast(AA): <div> with text 'third level<div>....'. (contrast ratio: 1.39)",
      "Low Contrast(AA): <div> with text 'fourth level'. (contrast ratio: 1.09)",
    ))
  ),
//------------------------------------------------------------------------------
  // when contrast level only passes if text is big
  new testcase(
    '
    <div style="color:#7D7D7D;">
      <h1>large text 2</h1>
      <p style="font-size: 19px; font-weight: bold">large text 2</p>
      <p style="font-size: 14pt">small
        <b>large</b>
        <strong>large</strong>
      </p>
      <p style="font-size: 18px; font-weight: bold">not large text 1</p>
      <p>not large text 2</p>
    </div>
    ',
    array('passed'=>False,'errors'=>array(
      "Low Contrast(AA): <div> with text '<h1>...</h1><p>.....'. (contrast ratio: 4.12)",
      "Low Contrast(AA): <p> with text 'small<b>...</b><s...'. (contrast ratio: 4.12)",
      "Low Contrast(AA): <p> with text 'not large text 1'. (contrast ratio: 4.12)",
      "Low Contrast(AA): <p> with text 'not large text 2'. (contrast ratio: 4.12)",
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
    array('passed'=>False,'errors'=>array(
      "Low Contrast(AA): <div> with text '<h1>...</h1><h2>....'. (contrast ratio: 4.12)",
      "Low Contrast(AA): <h4> with text 'h4'. (contrast ratio: 4.12)",
      "Low Contrast(AA): <h5> with text 'h5'. (contrast ratio: 4.12)",
      "Low Contrast(AA): <h6> with text 'h6'. (contrast ratio: 4.12)",
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
    array('passed'=>False,'errors'=>array(
      "Low Contrast(AA): <h1> with text 'h1 level 4'. (contrast ratio: 4.12)",
      "Low Contrast(AA): <h1> with text 'h1 level 5'. (contrast ratio: 4.12)",
      "Low Contrast(AA): <h1> with text 'h1 level 6'. (contrast ratio: 4.12)",
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
    array('passed'=>False,'errors'=>array(
      "Low Contrast(AA): <div> with text '<h1>...</h1>'. (contrast ratio: 1.00)",
      "Low Contrast(AA): <h1> with large text 'h1'. (contrast ratio: 1.00)",
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
    array('passed'=>False,'errors'=>array(
      "Low Contrast(AA): <div> with text '<h1>...</h1>'. (contrast ratio: 1.00)",
    ))
  ),
//------------------------------------------------------------------------------
  // invalid background color and invalid color
  new testcase(
    '
    <p style="background-color: reddd; color:reddd">invalid colors</p>
    ',
    array('passed'=>False,'errors'=>array(
      "Invalid Color: background color from <p> with text 'invalid colors'.",
      "Invalid Color: font color from <p> with text 'invalid colors'.",
    ))
  ),
//------------------------------------------------------------------------------
  // invalid font size
  new testcase(
    '
    <p style="font-size: 12ppp">invalid font-size 1</p>
    <p style="font-size: -12px">invalid font-size 2</p>
    ',
    array('passed'=>False,'errors'=>array(
      "Invalid Font-size: <p> with text 'invalid font-size 1'.",
      "Invalid Font-size: <p> with text 'invalid font-size 2'.",
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
    array('passed'=>False,'errors'=>array(
      "Invalid Font-weight: <p> with text 'invalid font-weig...'.",
      "Invalid Font-weight: <p> with text 'invalid font-weig...'.",
      "Invalid Font-weight: <p> with text 'invalid font-weig...'.",
    ))
  ),
//------------------------------------------------------------------------------
);
