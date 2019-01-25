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

    if (count($dom->getElementsByTagName('body')) > 0) {
      $body = $dom->getElementsByTagName('body')[0];
    } else {
      $body = NULL;
    }

    $eval_array = array();
    if ($body === NULL) {
      $eval_array['passed']   = TRUE;
    } else {
      self::_eval_DOM($body);
      $eval_array['passed']   = (count(self::$errors) === 0);
    }
    $eval_array['errors']   = self::$errors;
    $eval_array['warnings'] = self::$warnings;

    return $eval_array;
  }

  /**
   * Recursive DOM Element parsing helper
   * @param  DOMElement $dom_el
   * @return void
   */
  private static function _eval_DOM($dom_el) {
    if (get_class($dom_el) === 'DOMComment') { return; } // skip comments
    if ($dom_el->tagName === "img") {
      if (!$dom_el->hasAttribute('alt')) {
        $src = self::_get_trimmed_src($dom_el);
        $error_msg = "Error: image '$src' has no alt attribute.";
        self::$errors[] = $error_msg;
      } else {
        $alt = $dom_el->getAttribute('alt');
        if (trim($alt) === "") {
          $src = self::_get_trimmed_src($dom_el);
          $warning_msg = "Warning: image '$src' has empty alt attribute.";
          self::$warnings[] = $warning_msg;
        }
      }
    }
    $child_elements = self::_get_childElements($dom_el);
    foreach ($child_elements as $child_element) {
      self::_eval_DOM($child_element);
    }
  }

  /**
   * _get_childElements helper. Because DOMElement->childNodes also returns
   * DOMText which is not what we want, this helps with filtering those out.
   * @param  DOMElement $dom_el
   * @return array      [Array containing only DOMElement objects]
   */
  private static function _get_childElements($dom_el)
  {
    $child_nodes = $dom_el->childNodes;
    $child_elements = array();
    foreach ($child_nodes as $node) {
      if (property_exists($node, 'tagName')) {
        $child_elements[] = $node;
      }
    }
    return $child_elements;
  }

  /**
   * _get_trimmed_src description helper. returns content in 'src' attribute, if it's
   * longer than 15 char, it will trim and only return the filename.
   * @param  DOMElement $img_el
   * @return string
   */
  private static function _get_trimmed_src($img_el)
  {
    $src = $img_el->getAttribute('src');
    if (strlen($src) > 15) {
      $src_split = explode('/', $src);
      $src = $src_split[count($src_split)-1];
    }
    return $src;
  }

}
