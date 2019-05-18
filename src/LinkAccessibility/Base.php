<?php declare(strict_types=1);

namespace P1ho\AccessibilityChecker\LinkAccessibility;

/**
 * A base class that contains methods all LinkAccessibility Checkers will use
 */

abstract class Base
{

    /**
     * get_site_url function
     * @param  string $page_url [a page url, MUST include protocol]
     * @return string           [the site/home url]
     */
    public static function get_site_url(string $page_url): string
    {
        $url_no_protocol = substr($page_url, 8);
        $third_slash_pos = strpos($url_no_protocol, '/');
        if ($third_slash_pos === false) {
            return $page_url;
        } else {
            return substr($page_url, 0, $third_slash_pos + 8);
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
}
