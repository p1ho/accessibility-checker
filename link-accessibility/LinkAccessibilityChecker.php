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

  // cURL
  private static $curl;

  // sets how long cache will be saved
  private static $cache_time = 86400;

  public static function evaluate($dom, $domain)
  {
    self::_init();

    // trim the trailing '/' in domain if it's there
    if ($domain[strlen($domain)-1] === '/') {
      $domain = substr($domain, 0, -1);
    }

    $link_nodes_obj = $dom->getElementsByTagName('a');
    $link_nodes = array();
    for ($i = 0; $i < $link_nodes_obj->length; $i++) {
      $link_nodes[] = $link_nodes_obj[$i];
    }

    // fetch header from all links and cache them using Zebra_cURL
    self::_prefetch($link_nodes, $domain);

    // create array to be returned
    $eval_array = array();
    self::_eval_links($link_nodes, $domain);
    $eval_array['passed'] = count(self::$errors) === 0;
    $eval_array['errors'] = self::$errors;

    return $eval_array;
  }

  /**
   * require needed resources
   */
  private static function _init() {
    self::$errors = array();
    require_once "link-quality/LinkQualityChecker.php";
    require_once "link-text/LinkTextChecker.php";
    require_once __DIR__ . '/../vendor/stefangabos/zebra_curl/Zebra_cURL.php';
    self::$curl = new Zebra_cURL();
    self::$curl->cache(__DIR__ . '/../cache/', self::$cache_time);
    self::$curl->threads = 50;
  }

  /**
   * _prefetch function.
   * Prefetch all the URL so they are in cache.
   *
   * Note: './' and '../' is skipped because we don't have the absolute URL this
   * path is accessed from.
   *
   * @param  Array  $link_nodes [list of DOMElement Object with that is <a>]
   * @param  String $domain     [domain name (with transfer protocol defined)]
   * @return void
   */
  private static function _prefetch($link_nodes, $domain) {
    $paths = array_map( function ($x) { return $x->getAttribute('href'); }, $link_nodes);
    $filtered_paths = array();
    foreach ($paths as $path) {
      if (!preg_match("/^(#|tel:|mailto:|\.\/|\.\\\\|\.\.\/|\.\.\\\\)/", $path)) {
        if ($path[0] === '/') {
          $filtered_paths[] = $domain . $path;
        } else {
          $filtered_paths[] = $path;
        }
      }
    }
    self::$curl->header($filtered_paths, function ($x) { return; });
  }

  /**
   * Parses all the links
   * @param  Array  $link_nodes [list of DOMElement Object with that is <a>]
   * @param  String $domain     [domain name (with transfer protocol defined)]
   * @return void
   */
  private static function _eval_links($link_nodes, $domain) {
    foreach ($link_nodes as $link_node) {
      $text = self::_get_text_content($link_node);
      $path = $link_node->getAttribute('href');

      // check link-quality
      $link_quality_eval = LinkQualityChecker::evaluate($path, $domain);
      if ($link_quality_eval['is_redirect']) {
        self::$errors[] = (object) [
          'type' => 'redirect',
          'path' => $path,
          'link_text' => $text,
          'recommendation' => 'Use the final redirected link.',
        ];
      }
      if ($link_quality_eval['is_dead']) {
        self::$errors[] = (object) [
          'type' => 'dead',
          'path' => $path,
          'link_text' => $text,
          'recommendation' => 'Find an alternative working link.',
        ];
      }
      if ($link_quality_eval['is_same_domain']) {
        self::$errors[] = (object) [
          'type' => 'domain overlap',
          'path' => $path,
          'link_text' => $text,
          'recommendation' => 'Use relative URL.',
        ];
      }

      // check link-text accessibility
      $link_text_eval = LinkTextChecker::evaluate($link_node, $domain);
      if (!$link_text_eval['passed_blacklist_words']) {
        self::$errors[] = (object) [
          'type' => 'poor link text',
          'path' => $path,
          'link_text' => $text,
          'recommendation' => 'Use more descriptive and specific wording.',
        ];
      }
      if (!$link_text_eval['passed_text_not_url']) {
        self::$errors[] = (object) [
          'type' => 'url link text',
          'path' => $path,
          'link_text' => $text,
          'recommendation' => 'Use real words that describe the link.',
        ];
      }
      if (!$link_text_eval['passed_text_length']) {
        self::$errors[] = (object) [
          'type' => 'text too long',
          'path' => $path,
          'link_text' => $text,
          'recommendation' => 'Shorten the link text.',
        ];
      }
      if ($link_text_eval['url_is_pdf']) {
        if (!$link_text_eval['text_has_pdf']) {
          self::$errors[] = (object) [
            'type' => 'unclear pdf link',
            'path' => $path,
            'link_text' => $text,
            'recommendation' => 'Include the word "PDF" in the link',
          ];
        }
      } else {
        if ($link_text_eval['url_is_download'] &&
            $link_text_eval['text_has_download']) {
          self::$errors[] = (object) [
            'type' => 'unclear download link',
            'path' => $path,
            'link_text' => $text,
            'recommendation' => 'Include the word "download" in the link.',
          ];
        }
      }
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
    return str_replace(array("\r", "\n"), '', $text);
  }

}
