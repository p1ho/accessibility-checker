<?php

/**
 * Image Accessibility Checker class for Accessibility Checker
 *
 * This is NOT a HTML5 validator, it expects the DOMObject that is passed has
 * been parsed correctly based on well-formed HTML.
 *
 * This simply goes through the DOM structure, it'll assume all images are
 * integral to the page, and return:
 * 1) pass: whether there were any errors.
 * 2) errors: if the alt attribute is missing completely
 * 3) warnings: if alt attribute is empty.
 * (Will not fail test because alt="" is theoretically accessible if it's not
 * essential to the page content.)
 *
 * Consulted:
 * [1] https://webaim.org/blog/alt-text-and-linked-images/
 * [2] http://webaccess.hr.umich.edu/best/quickguide.html#alt
 *
 */

class ImageAccessibilityChecker
{

  // Keeps track of errors
  private static $errors;

  // Keeps track of warnings
  private static $warnings;

  /**
   * Check if image tags have alt tags, and if they do, whether it is empty.
   *
   * @param  DOMObject $dom [The whole parsed HTML DOM Tree]
   * @return array
   *
   */
  public static function evaluate($dom)
  {
    self::$errors = array();
    self::$warnings = array();

    $img_nodes_obj = $dom->getElementsByTagName('img');
    $img_nodes = array();
    for ($i = 0; $i < $img_nodes_obj->length; $i++) {
      $img_nodes[] = $img_nodes_obj[$i];
    }

    $eval_array = array();
    self::_eval_imgs($img_nodes);
    $eval_array['passed']   = count(self::$errors) === 0;
    $eval_array['errors']   = self::$errors;
    $eval_array['warnings'] = self::$warnings;

    return $eval_array;
  }

  /**
   * Parses all the images
   * @param  Array  $img_nodes [list of DOMElement Object with that is <img>]
   * @return void
   */
  private static function _eval_imgs($img_nodes) {
    foreach ($img_nodes as $img_node) {
      $src = $img_el->getAttribute('src');

      if (!$img_node->hasAttribute('alt')) {
        self::$errors[] = (object) [
          'type' => 'no alt',
          'src'  => $src,
          'recommendation' => 'Add an alt attribute to the img and add a description',
        ];
      } else {
        $alt = $dom_el->getAttribute('alt');
        if (trim($alt) === "") {
          self::$warnings[] = (object) [
            'type' => 'empty alt',
            'src'  => $src,
            'recommendation' => 'If this image is integral to the content, please add a description',
          ];
        }
      }
    }
  }

}
