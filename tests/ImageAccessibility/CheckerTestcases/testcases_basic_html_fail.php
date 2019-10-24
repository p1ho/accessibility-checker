<?php declare(strict_types=1);

/**
 * Test cases (Fail) for Image Accessibility Checker
 */

$testcases_basic_html_fail = array(
//------------------------------------------------------------------------------
  new testcase(
      '
    <img src="/some/source.jpg">
    ',
      array('passed'=>false,'errors'=>array(
        (object) [
          'type' => 'no alt',
          'src' => '/some/source.jpg',
          'html' => '<img src="/some/source.jpg">',
          'recommendation' => 'Add an alt attribute to the img and add a description.',
        ],
      ),'warnings'=>array())
  ),
//------------------------------------------------------------------------------
  new testcase(
      '
    <img src="/some/source.jpg" alt="">
    ',
      array('passed'=>true,'errors'=>array(),'warnings'=>array(
      (object) [
        'type' => 'empty alt',
        'src' => '/some/source.jpg',
        'html' => '<img src="/some/source.jpg" alt="">',
        'recommendation' => 'If this image is integral to the content, please add a description.',
      ],
    ))
  ),
//------------------------------------------------------------------------------
  // testing multiple images
  new testcase(
      '
    <img src="/some/source1.jpg" alt="some alt text">
    <img src="/some/source2.jpg" alt="">
    <img src="/some/source3.jpg">
    <img src="/some/source4.jpg" alt="">
    <img src="/some/source5.jpg">
    ',
      array('passed'=>false,'errors'=>array(
        (object) [
          'type' => 'no alt',
          'src' => '/some/source3.jpg',
          'html' => '<img src="/some/source3.jpg">',
          'recommendation' => 'Add an alt attribute to the img and add a description.',
        ],
        (object) [
          'type' => 'no alt',
          'src' => '/some/source5.jpg',
          'html' => '<img src="/some/source5.jpg">',
          'recommendation' => 'Add an alt attribute to the img and add a description.',
        ],
      ),'warnings'=>array(
        (object) [
          'type' => 'empty alt',
          'src' => '/some/source2.jpg',
          'html' => '<img src="/some/source2.jpg" alt="">',
          'recommendation' => 'If this image is integral to the content, please add a description.',
        ],
        (object) [
          'type' => 'empty alt',
          'src' => '/some/source4.jpg',
          'html' => '<img src="/some/source4.jpg" alt="">',
          'recommendation' => 'If this image is integral to the content, please add a description.',
        ],
      ))
  ),
//------------------------------------------------------------------------------
  // testing filenames as alt
  new testcase(
      '
    <img src="/some/source1.jpg" alt="file.jpg">
    <img src="/some/source2.jpg" alt="file.jpg.jpg">
    <img src="/some/source3.jpg" alt="file.jpeg">
    <img src="/some/source4.jpg" alt="f-i-l-e.jpgggggggggg">
    <img src="/some/source5.jpg" alt="file.gif">
    ',
      array('passed'=>false,'errors'=>array(
        (object) [
          'type' => 'filename alt',
          'src' => '/some/source1.jpg',
          'html' => '<img src="/some/source1.jpg" alt="file.jpg">',
          'recommendation' => 'Do not use image filename as the alt attribute, describe the image.',
        ],
        (object) [
          'type' => 'filename alt',
          'src' => '/some/source2.jpg',
          'html' => '<img src="/some/source2.jpg" alt="file.jpg.jpg">',
          'recommendation' => 'Do not use image filename as the alt attribute, describe the image.',
        ],
        (object) [
          'type' => 'filename alt',
          'src' => '/some/source3.jpg',
          'html' => '<img src="/some/source3.jpg" alt="file.jpeg">',
          'recommendation' => 'Do not use image filename as the alt attribute, describe the image.',
        ],
        (object) [
          'type' => 'filename alt',
          'src' => '/some/source4.jpg',
          'html' => '<img src="/some/source4.jpg" alt="f-i-l-e.jpgggggggggg">',
          'recommendation' => 'Do not use image filename as the alt attribute, describe the image.',
        ],
        (object) [
          'type' => 'filename alt',
          'src' => '/some/source5.jpg',
          'html' => '<img src="/some/source5.jpg" alt="file.gif">',
          'recommendation' => 'Do not use image filename as the alt attribute, describe the image.',
        ],
      ),'warnings'=>array())
  ),
);
