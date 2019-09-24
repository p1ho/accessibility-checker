<?php declare(strict_types=1);

namespace P1ho\AccessibilityChecker\LinkAccessibility;

/**
 * A base class that contains methods all LinkAccessibility Checkers will use
 */

abstract class Base
{

    /**
     * get_site_url function
     * @param  string $page_url [a page url, with or without protocol]
     * @return string           [the site/home url]
     */
    public static function get_site_url(string $page_url): string
    {
        $link_strip_protocol = preg_replace('/(http)s?\:\/\//i', '', $page_url);
        $offset = strlen($page_url) - strlen($link_strip_protocol);
        $third_slash_pos = strpos($link_strip_protocol, '/');
        if ($third_slash_pos === false) {
            return $page_url;
        } else {
            return substr($page_url, 0, $third_slash_pos + $offset);
        }
    }

    /**
     * compute_link_url function
     * @param  string $link_path [href from the a tag]
     * @param  string $page_path [path of the page (without domain)]
     * @param  string $site_url  [site url]
     * @return string            [computed final link path]
     */
    public static function compute_link_url(string $link_path, string $page_path, string $site_url): string
    {
        if (substr($link_path, 0, 2) === "//") {
            return "http:" . $link_path;
        } elseif ($link_path[0] === '/') {
            return $site_url . $link_path;
        } elseif (substr($link_path, 0, 7) !== "http://" && substr($link_path, 0, 8) !== "https://") {
            $last_slash_pos = strrpos($page_path, '/');
            if ($last_slash_pos !== false) {
                $parent_path = substr($page_path, 0, $last_slash_pos + 1);
                return $site_url . $parent_path . $link_path;
            } else {
                return $site_url . '/' . $link_path;
            }
        } else {
            return $link_path;
        }
    }
    
    /**
     * _get_text_content helper.
     *
     * Example:
     * <div>This is <div>I'm wrapped</div> some text</div>
     *
     * Running this function would return "This is  I'm wrapped  some text"
     *
     * @param  DOMElement $dom_el
     * @return string
     */
    protected function _get_text_content(\DOMElement $dom_el): string
    {
        $text = '';
        foreach ($dom_el->childNodes as $childNode) {
            if (get_class($childNode) === 'DOMComment') {
                continue;
            } else {
                if (get_class($childNode) === 'DOMText') {
                    $text_raw = htmlspecialchars_decode($childNode->wholeText);
                    $text_to_add = trim($text_raw);
                    if ($text_to_add === '') {
                        if (strlen($text_raw) !== 0) {
                            $text .= ' ';
                        }
                        continue;
                    }
                    $add_space_pre = preg_match("/^[\s\t\r\n]/", $text_raw);
                    $add_space_post = preg_match("/[\s\t\r\n]$/", $text_raw);
                    if ($add_space_pre) {
                        $text_to_add = ' ' . $text_to_add;
                    }
                    if ($add_space_post) {
                        $text_to_add .= ' ';
                    }
                } elseif (get_class($childNode) === 'DOMElement') {
                    $text_to_add = $this->_get_text_content($childNode);
                }
                if (property_exists($childNode, 'tagName')) {
                    if ($text_to_add !== '' && in_array(strtolower($childNode->tagName), $this->block_elements)) {
                        $text .= ' ' . $text_to_add . ' ';
                        continue;
                    }
                }
                $text .= $text_to_add;
            }
        }
        return str_replace(["\r", "\n", "\t"], '', $text);
    }
    
    /**
     * _get_outerHTML helper.
     * Consulted https://stackoverflow.com/questions/5404941/how-to-return-outer-html-of-domdocument
     * @param  DOMElement $dom_el
     * @return string
     */
    protected function _get_outerHTML(\DOMElement $dom_el): string
    {
        $doc = new \DOMDocument();
        $doc->appendChild($doc->importNode($dom_el, true));
        return trim(str_replace(["\r"], '', $doc->saveHTML()));
    }
}
