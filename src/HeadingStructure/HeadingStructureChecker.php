<?php

/**
 * Heading Structure Checker class for Accessibility Checker
 *
 * This is NOT a HTML5 validator, it expects the DOMObject that is passed has
 * been parsed correctly based on well-formed HTML.
 *
 * There are 2 strict modes:
 * This 'strict' nature refers to Example 17 (see [3] in consulted) where
 * heading structures could all be placed at the same nesting level, or are
 * nested based on their ranking. Because both are equally correct (However, it
 * is implied in Example 25 [4] that the latter is clearer), 2 'strict' modes
 * are created.
 * However, it's worth noting that even in 'non-strict' mode, higher heading
 * rank is still NOT allowed to be nested deeper than heading with lower ranking.
 *
 * The algorithm used here assumes the first headings it encounters are the
 * valid ones (Depth-First Search approach).
 * Therefore if the first headings encountered are placed in the wrong spot,
 * this checker will deem those as correct and see everything that comes after
 * as incorrect.
 *
 * Consulted:
 * [1] http://webaccess.hr.umich.edu/best/nav.html#headings
 * [2] https://www.w3.org/WAI/tutorials/page-structure/headings/
 * [3] https://www.w3.org/TR/html5/sections.html#the-h1-h2-h3-h4-h5-and-h6-elements
 * [4] https://www.w3.org/TR/html5/sections.html#headings-and-sections
 *
 */

class HeadingStructureChecker
{

  // Keeps track of errors
  private static $errors;

  // Keeps track of how deep headings are nested in the structure
  private static $heading_level_structure;

  // Keeps track of order headings appear (regardless of how they're nested)
  private static $heading_order;

  /**
   * Check if heading structure is well formed
   * Will assume heading tag starts at <h3>
   * (because our site's current default has h1 as Org title, and h2 as Page title)
   *
   * Checks include:
   * - starts at <h3>, ends at <h6>
   * - no heading rank skipped (e.g., from <h3> directly to <h5>)
   * - higher ranked headings are not nested deeper than lower ranked heading
   *    (e.g., no <h3> nested deeper than <h4>)
   * - no headings are encapsulated in another heading
   * - *if strict mode on, same level contains only 1 type of heading
   *
   * @param  DOMObject $dom [The whole parsed HTML DOM Tree]
   * @return array
   *
   */
  public static function evaluate($dom, $is_strict = TRUE)
  {
    self::$errors                  = array();
    self::$heading_level_structure = array(2=>0); //sets a baseline
    self::$heading_order           = array(1,2);  //assume h1 and h2 have been defined

    if (count($dom->getElementsByTagName('body')) > 0) {
      $body = $dom->getElementsByTagName('body')[0];
    } else {
      $body = NULL;
    }

    $eval_array = array();
    if ($body === NULL) {
      $eval_array['passed'] = TRUE;
      $eval_array['errors'] = array();
    } else {
      self::_eval_DOM($body, 3, 1, FALSE, $is_strict);
      $eval_array['passed'] = (count(self::$errors) === 0);
      $eval_array['errors'] = self::$errors;
    }
    return $eval_array;
  }

  /**
   * Recursive DOM Element parsing helper
   * @param  DOMElement $dom_el
   * @param  Int        $expected_heading_rank
   * @param  Int        $nested_level
   * @param  Boolean    $is_in_h [Current DOM Element is wrapped in a <h> tag]
   * @param  Boolean    $is_strict [whether employ strict mode]
   * @return void
   */
  private static function _eval_DOM($dom_el, $expected_heading_rank, $nested_level, $is_in_h, $is_strict)
  {
    if (get_class($dom_el) === 'DOMComment') {return;} // skip comments
    // pre-define some variables
    $tag_name = $dom_el->tagName;
    $text = self::_get_text_content($dom_el);
    $expected_tag = 'h' . $expected_heading_rank;

    // if element is heading
    if (self::_is_heading_tag($tag_name)) {
      $heading_rank = (int) $tag_name[1];
      if (!in_array($heading_rank, array(
        3,
        4,
        5,
        6
      ))) { // As mentioned, h1 and h2 are unallowed
        self::$errors[] = (object) [
          'type' => 'heading unallowed',
          'tag' => $tag_name,
          'text' => $text,
          'recommendation' => 'Use allowed heading (<h3> to <h6>).',
        ];
      } else {
        /*
        Check if this heading element is inside another heading tag,
        if so, it will add an error to the log.
        */
        if ($is_in_h) {
          self::$errors[] = (object) [
            'type' => 'heading inside heading',
            'tag' => $tag_name,
            'text' => $text,
            'recommendation' => 'Do not put heading inside another heading.',
          ];
        } else {
          /*
          If heading is not inside another <h> tag.
          We do 2 checks:
          1) previous heading rank is from range [1 below current - max possible]
          If not, we know there may be heading skipping
          2) If first test passed, we check if heading number has been set in
          the heading_level_structure, then do subsequent tests.
          */
          $prev_rank = self::$heading_order[count(self::$heading_order) - 1];
          self::$heading_order[] = $heading_rank; // record heading appearance order
          $rank_diff = $heading_rank - $prev_rank;
          if ($rank_diff > 1) {
            for ($i = $rank_diff - 1; $i > 0; $i--) {
              $missing_rank   = $heading_rank - $i;
              self::$errors[] = (object) [
                'type' => 'heading skipped',
                'tag' => $tag_name,
                'text' => $text,
                'recommendation' => "<h$missing_rank> is expected before the placement of this heading.",
              ];
            }
          } else {
            if (isset(self::$heading_level_structure[$heading_rank])) {
              /*
              If yes: we check if the current_nested_level is the same as the one
              set in heading_level_structure. If they're not the same, we
              know there is an inconsistency. If this test passed, we
              check if heading_rank is the same as expected_heading_rank. In
              any case, we increment the expected_heading_rank because we've
              confirmed that this tag is a heading tag, even if its number
              is incorrect.
              */
              $recorded_nested_level = self::$heading_level_structure[$heading_rank];
              if ($nested_level < $recorded_nested_level) {
                self::$errors[] = (object) [
                  'type' => 'heading too shallow',
                  'tag' => $tag_name,
                  'text' => $text,
                  'recommendation' => "Try nesting this heading deeper.",
                ];
              } else if ($nested_level > $recorded_nested_level) {
                self::$errors[] = (object) [
                  'type' => 'heading too deep',
                  'tag' => $tag_name,
                  'text' => $text,
                  'recommendation' => "Try nesting this heading shallower.",
                ];
              } else {
                if ($is_strict) {
                  /*
                  strict mode:
                  if heading_rank is smaller than expected_heading_rank, log error.
                  There is already a check for skipped heading from line 128-137
                  The non-strict scenario is already captured in the previous 2 tests.
                  */
                  if ($heading_rank < $expected_heading_rank) {
                    self::$errors[] = (object) [
                      'type' => 'heading misplaced',
                      'tag' => $tag_name,
                      'text' => $text,
                      'recommendation' => "Try nesting this heading shallower.",
                    ];
                  }
                }
              }
            } else {
              if ($is_strict) {
                /*
                strict mode:
                if heading_rank is smaller than expected_heading_rank, log error.
                Or, if previous heading has been set, but the nested level of the
                current heading is lower than the previous heading, log error.

                There is already a check for skipped heading from line 128-137
                The non-strict scenario is already captured in the previous 2 tests.
                */
                if ($heading_rank < $expected_heading_rank) {
                  self::$errors[] = (object) [
                    'type' => 'heading misplaced',
                    'tag' => $tag_name,
                    'text' => $text,
                    'recommendation' => "Try nesting this heading shallower.",
                  ];
                } else {
                  if (isset(self::$heading_level_structure[$heading_rank - 1])) {
                    if ($nested_level <= self::$heading_level_structure[$heading_rank - 1]) {
                      self::$errors[] = (object) [
                        'type' => 'heading too shallow',
                        'tag' => $tag_name,
                        'text' => $text,
                        'recommendation' => "Try nesting this heading deeper.",
                      ];
                    } else {
                      /*
                      When all previous tests passed, log where this heading is.
                      This means the nesting level of this heading is now regarded
                      as correct and canonical.
                      */
                      self::$heading_level_structure[$heading_rank] = $nested_level;
                    }
                  }
                }
              } else {
                self::$heading_level_structure[$heading_rank] = $nested_level;
              }
            }
          }
        }
      }
      $is_in_h = TRUE;
      $expected_heading_rank++;
    } else {
      // edgecase: if an invalid headtag is entered (e.g., <h7>), show an error
      if ($tag_name[0] === 'h' && ctype_digit(substr($tag_name, 1))) {
        self::$errors[] = (object) [
          'type' => 'invalid heading',
          'tag' => $tag_name,
          'text' => $text,
          'recommendation' => "Use valid headings only (<h1> through <h6>).",
        ];
      }
      /*
      if it's not a heading tag but its nested level was used by another heading
      previously, we increment expected heading by 1.
      */
      $heading_level_structure_flip = array_flip(self::$heading_level_structure);
      if (isset($heading_level_structure_flip[$nested_level])) {
        $expected_heading_rank++;
      }
    }
    // base case (if element has no children)
    $child_elements = self::_get_childElements($dom_el);
    foreach ($child_elements as $child_element) {
      self::_eval_DOM($child_element, $expected_heading_rank, $nested_level + 1, $is_in_h, $is_strict);
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
    $child_nodes    = $dom_el->childNodes;
    $child_elements = array();
    foreach ($child_nodes as $node) {
      if (property_exists($node, 'tagName')) {
        $child_elements[] = $node;
      }
    }
    return $child_elements;
  }

  /**
   * _is_heading_tag function. check if passed in tag name ($s) is one of the
   * valid heading tags
   * @param  String  $s [tag name]
   * @return boolean
   */
  private static function _is_heading_tag($s)
  {
    return preg_match('%^h[1-6]{1}$%iu', $s);
  }

  /**
   * _get_text_content helper.
   * Gets text content that are not wrapped in any other tags.
   * If it encounters another child tag, it will replace its content with '<tag>...</tag>'.
   * If it encounters <br>, it will replace it with ' '.
   *
   * Example:
   * <div>
   *   This is
   *   <div>I'm wrapped</div>
   *   some text
   * </div>
   *
   * Running this function over the node would return 'This is <div>...</div> some text'.
   *
   * @param  DOMElement $dom_el
   * @return string
   */
  private static function _get_text_content($dom_el) {
    $text = '';
    foreach ($dom_el->childNodes as $childNode) {
      if (get_class($childNode) === 'DOMText') {
        $text .= htmlspecialchars_decode(trim($childNode->wholeText));
      } else if (get_class($childNode) === 'DOMComment') {
        continue;
      } else {
        if ($childNode->tagName == 'br') {
          $text .= ' ';
        } else {
          $tag = $childNode->tagName;
          $text .= "<$tag>...</$tag>";
        }
      }
    }
    return str_replace(array("\r", "\n"), '', $text);
  }

}
