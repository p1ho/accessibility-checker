<?php declare(strict_types=1);

namespace P1ho\AccessibilityChecker\LinkAccessibility;

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

class Checker extends Base
{
    private static $cache_time = 86400;

    /**
     * evaluate function
     * @param  DOMDocument $dom [The whole parsed HTML DOM Tree]
     * @param  string $page_url [a page url, MUST include protocol]
     * @return array
     */
    public function evaluate(\DOMDocument $dom, string $page_url): array
    {
        $link_nodes_obj = $dom->getElementsByTagName('a');
        $link_nodes = array();
        for ($i = 0; $i < $link_nodes_obj->length; $i++) {
            // remove placeholder links from checks
            // https://stackoverflow.com/questions/5292343/is-an-anchor-tag-without-the-href-attribute-safe
            if ($link_nodes_obj[$i]->getAttribute('href') !== '') {
                  $link_nodes[] = $link_nodes_obj[$i];
            }
        }

        // create array to be returned
        $eval_array = array();
        $errors = $this->_eval_links($link_nodes, $page_url);
        $eval_array['passed'] = count($errors) === 0;
        $eval_array['errors'] = $errors;

        return $eval_array;
    }

    /**
     * _eval_links helper function
     * Parses all the links
     * @param  array  $link_nodes [list of DOMElement Object with that is <a>]
     * @param  string $page_url   [a page url, MUST include protocol]
     * @return array
     */
    private function _eval_links(array $link_nodes, string $page_url): array
    {
        self::_prefetch($link_nodes, $page_url);

        $errors = [];
        foreach ($link_nodes as $link_node) {
            $text = $this->_get_text_content($link_node);
            $path = $link_node->getAttribute('href');

            // check link-quality
            $link_quality_eval = LinkQuality\Checker::evaluate($path, $page_url);
            if ($link_quality_eval['is_redirect']) {
                $errors[] = (object) [
                    'type' => 'redirect',
                    'href' => $path,
                    'text' => $text,
                    'recommendation' => 'Use the final redirected link.'];
            }
            if ($link_quality_eval['is_dead']) {
                $errors[] = (object) [
                    'type' => 'dead',
                    'href' => $path,
                    'text' => $text,
                    'recommendation' => 'Find an alternative working link.'];
            }
            if ($link_quality_eval['is_same_domain']) {
                $errors[] = (object) [
                    'type' => 'domain overlap',
                    'href' => $path,
                    'text' => $text,
                    'recommendation' => 'Use relative URL.'];
            }
            if ($link_quality_eval['timed_out']) {
                $errors[] = (object) [
                    'type' => 'slow connection',
                    'href' => $path,
                    'text' => $text,
                    'recommendation' => 'Troubleshoot why the page takes so long to load.'];
            }

            // check link-text accessibility
            $link_text_eval = LinkText\Checker::evaluate($link_node, $page_url);
            if (!$link_text_eval['passed_blacklist_words']) {
                $errors[] = (object) [
                    'type' => 'poor link text',
                    'href' => $path,
                    'text' => $text,
                    'recommendation' => 'Use more descriptive and specific wording.'];
            }
            if (!$link_text_eval['passed_text_not_url']) {
                $errors[] = (object) [
                    'type' => 'url link text',
                    'href' => $path,
                    'text' => $text,
                    'recommendation' => 'Use real words that describe the link.'];
            }
            if (!$link_text_eval['passed_text_length']) {
                $errors[] = (object) [
                    'type' => 'text too long',
                    'href' => $path,
                    'text' => $text,
                    'recommendation' => 'Shorten the link text.'];
            }
            if ($link_text_eval['url_is_pdf']) {
                if (!$link_text_eval['text_has_pdf']) {
                    $errors[] = (object) [
                        'type' => 'unclear pdf link',
                        'href' => $path,
                        'text' => $text,
                        'recommendation' => 'Include the word "PDF" in the link.'];
                }
            } else {
                if ($link_text_eval['url_is_download'] &&
                    $link_text_eval['text_has_download']) {
                    $errors[] = (object) [
                        'type' => 'unclear download link',
                        'href' => $path,
                        'text' => $text,
                        'recommendation' => 'Include the word "download" in the link.'];
                }
            }
        }
        return $errors;
    }

    /**
     * _prefetch function.
     * Prefetch all the URL so they are in cache.
     *
     * @param  array  $link_nodes [list of DOMElement Object with that is <a>]
     * @param  string $page_url   [a page url, MUST include protocol]
     * @return void
     */
    private static function _prefetch(array $link_nodes, string $page_url): void
    {
        $link_urls = array_map(function ($x) {
            return $x->getAttribute('href');
        }, $link_nodes);
        $site_url  = static::get_site_url($page_url);
        $page_path = str_replace($site_url, "", $page_url);

        $filtered_paths = array();
        foreach ($link_urls as $link_url) {
            if (!preg_match("/^(#|tel:|mailto:){1}/", $link_url)) {
                $filter_paths[] = static::compute_link_url($link_url, $page_path, $site_url);
            }
        }

        $curl = new \Zebra_cURL();
        $curl->cache(__DIR__. '/../../cache/', self::$cache_time, true, 0777);
        // HEAD requests should NOT take long to return results, will terminate
        // after 5 seconds.
        $curl->option([
          CURLOPT_TIMEOUT => 5,
          CURLOPT_CONNECTTIMEOUT => 5
        ]);
        $curl->header($filtered_paths, function ($x) {
            return;
        });
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
    private function _get_text_content(\DOMElement $dom_el): string
    {
        $text = '';
        foreach ($dom_el->childNodes as $childNode) {
            if (get_class($childNode) === 'DOMText') {
                $text .= htmlspecialchars_decode(trim($childNode->wholeText));
            } elseif (get_class($childNode) === 'DOMComment') {
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
