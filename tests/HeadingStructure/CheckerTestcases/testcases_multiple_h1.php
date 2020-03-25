<?php declare(strict_types=1);

/**
 * Test cases for Multiple H1
 */

$testcases_multiple_h1_fail = array(
//------------------------------------------------------------------------------
  // by default multiple h1 should fail
  new testcase(
      '
    <h1>First h1</h1>
    <h1>Second h1</h1>
  ',
      array('passed'=>false,'errors'=>array(
      (object) [
        'type' => 'heading unallowed',
        'tag' => 'h1',
        'text' => 'Second h1',
        'html' => '<h1>Second h1</h1>',
        'recommendation' => "Check and use only allowed headings (<h2>, <h3>, <h4>, <h5>, <h6>; multiple <h1> unallowed)."
      ]
    ))
  ),
//------------------------------------------------------------------------------
  // unallowed heading takes precedence than nesting problem
  new testcase(
      '
    <h1>First h1</h1>
    <div><h1>Second h1</h1></div>
  ',
      array('passed'=>false,'errors'=>array(
      (object) [
        'type' => 'heading unallowed',
        'tag' => 'h1',
        'text' => 'Second h1',
        'html' => '<h1>Second h1</h1>',
        'recommendation' => "Check and use only allowed headings (<h2>, <h3>, <h4>, <h5>, <h6>; multiple <h1> unallowed)."
      ]
    ))
  ),
//------------------------------------------------------------------------------
  // unallowed heading takes precedence than nesting problem (reversed)
  new testcase(
      '
    <div><h1>First h1</h1></div>
    <h1>Second h1</h1>
  ',
      array('passed'=>false,'errors'=>array(
    (object) [
      'type' => 'heading unallowed',
      'tag' => 'h1',
      'text' => 'Second h1',
      'html' => '<h1>Second h1</h1>',
      'recommendation' => "Check and use only allowed headings (<h2>, <h3>, <h4>, <h5>, <h6>; multiple <h1> unallowed)."
    ]
  ))
  ),
//------------------------------------------------------------------------------
  // throw some other headings into the mix
  new testcase(
      '
    <h1>First h1</h1>
    <h2>Some h2</h2>
    <h3>Some h3</h3>
    <h1>Second h1</h1>
  ',
      array('passed'=>false,'errors'=>array(
      (object) [
        'type' => 'heading unallowed',
        'tag' => 'h1',
        'text' => 'Second h1',
        'html' => '<h1>Second h1</h1>',
        'recommendation' => "Check and use only allowed headings (<h2>, <h3>, <h4>, <h5>, <h6>; multiple <h1> unallowed)."
      ]
    ))
  ),
//------------------------------------------------------------------------------
);


// if allow_multiple_h1 is set to true, multiple h1 should pass

$testcases_multiple_h1_pass = array(
//------------------------------------------------------------------------------
  new testcase(
      '
    <h1>First h1</h1>
    <h1>Second h1</h1>
  ',
      array('passed'=>true,'errors'=>[])
  ),
//------------------------------------------------------------------------------
  new testcase(
      '
    <h1>First h1</h1>
    <h1>Second h1</h1>
    <h1>Third h1</h1>
    <h1>Fourth h1</h1>
  ',
      array('passed'=>true,'errors'=>[])
  ),
);
