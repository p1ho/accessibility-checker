<?php

/**
 * Link Accessibility Checker class for Accessibility Checker
 *
 * This goes through the DOM structure, check if the <a> tags it encounters have
 * 'href', if yes, then it will pass it to the 2 helper classes that will check
 * link quality and link text.
 *
 * It will return an array with the following:
 * 1) pass: whether there are any errors.
 * 2) errors: all the errors that were tallied
 *
 */

class LinkAccessibilityChecker
{
  // Keeps track of errors
  private static $errors;

  public static function evaluate($dom, $domain)
  {
    self::_init();

    if (count($dom->getElementsByTagName('body')) > 0) {
      $body = $dom->getElementsByTagName('body')[0];
    } else {
      $body = NULL;
    }

    $eval_array = array();
    if ($body === NULL) {
      $eval_array['passed']   = TRUE;
      $eval_array['errors']   = array();
    } else {
      self::_eval_DOM($body, $domain);
      $eval_array['passed']   = (count(self::$errors) === 0);
      $eval_array['errors']   = self::$errors;
    }

    return $eval_array;
  }

  /**
   * require needed resources
   */
  private static function _init() {
    self::$errors = array();
    require_once "link-quality/LinkQualityChecker.php";
    require_once "link-text/LinkTextChecker.php";
  }

  /**
   * Recursive DOM Element parsing helper
   * @param  DOMElement $dom_el
   * @return void
   */
  private static function _eval_DOM($dom_el, $domain) {
    if (get_class($dom_el) === 'DOMComment') {return;} // skip comments
    $tag_name = $dom_el->tagName;
    $url = $dom_el->getAttribute('href');
    if ($tag_name === "a") {
      $text_raw = self::_get_text_content($dom_el);
      $text     = self::_get_trimmed_text($text_raw);

      // check link-quality
      $link_quality_eval = LinkQualityChecker::evaluate($url, $domain);
      if ($link_quality_eval['is_redirect']) {
        self::$errors[] = "Redirect Link: link with text '$text' is a redirect, use the final redirected link.";
      }
      if ($link_quality_eval['is_dead']) {
        self::$errors[] = "Dead Link: link with text '$text' no longer works.";
      }
      if ($link_quality_eval['is_same_domain']) {
        self::$errors[] = "Same Domain: link with text '$text' links to somewhere in this website, use relative URL.";
      }

      // check link-text accessibility
      $link_text_eval = LinkTextChecker::evaluate($dom_el);
      if (!$link_text_eval['passed_blacklist_words']) {
        self::$errors[] = "Poor Link Name: link with text '$text' could be more descriptive";
      }
      if (!$link_text_eval['passed_text_not_url']) {
        self::$errors[] = "URL Link: link with text '$text' is an URL link. URL is harder to read.";
      }
      if (!$link_text_eval['passed_text_length']) {
        self::$errors[] = "Long Link Text: link with text '$text' is too long, please shorten it.";
      }
      if ($link_text_eval['url_is_pdf']) {
        if (!$link_text_eval['text_has_pdf']) {
          self::$errors[] = "PDF Link: link with text '$text' is a PDF, please have the word 'PDF' in the link text.";
        }
      } else {
        if ($link_text_eval['url_is_download'] &&
            $link_text_eval['text_has_download']) {
          self::$errors[] = "Download Link: link with text '$text' is a download link, please have the word 'download' in the link text'.";
        }
      }
    }

    $child_elements = self::_get_childElements($dom_el);
    foreach ($child_elements as $child_element) {
      self::_eval_DOM($child_element, $domain);
    }
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
    return $text;
  }

  /**
   * _get_trimmed_text description helper.
   * If it's longer than 20 character long,
   * it will only return the first 17 characters + '...'
   *
   * @param  String $raw_text
   * @return string
   */
  private static function _get_trimmed_text($raw_text)
  {
    $text = str_replace(array("\r", "\n"), '', trim($raw_text));
    if (strlen($text) > 20) {
      return substr($text, 0, 17) . '...';
    } else {
      return $text;
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
}
