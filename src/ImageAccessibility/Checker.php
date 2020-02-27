<?php declare(strict_types=1);

namespace P1ho\AccessibilityChecker\ImageAccessibility;

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

class Checker
{

  // Keeps track of errors
    private $errors;

    // Keeps track of warnings
    private $warnings;

    /**
     * Check if image tags have alt tags, and if they do, whether it is empty.
     *
     * @param  DOMDocument $dom [The whole parsed HTML DOM Tree]
     * @return array
     *
     */
    public function evaluate(\DOMDocument $dom): array
    {
        $this->errors = [];
        $this->warnings = [];

        $img_nodes_obj = $dom->getElementsByTagName('img');
        $img_nodes = [];
        for ($i = 0; $i < $img_nodes_obj->length; $i++) {
            $img_nodes[] = $img_nodes_obj[$i];
        }

        $eval_array = [];
        $this->_eval_imgs($img_nodes);
        $eval_array['passed']   = count($this->errors) === 0;
        $eval_array['errors']   = $this->errors;
        $eval_array['warnings'] = $this->warnings;

        return $eval_array;
    }

    /**
     * Parses all the images
     * @param  array  $img_nodes [list of DOMElement Object with that is <img>]
     * @return void
     */
    private function _eval_imgs(array $img_nodes): void
    {
        foreach ($img_nodes as $img_node) {
            $src = $img_node->getAttribute('src');
            $html = $this->_get_outerHTML($img_node);
            
            if (!$img_node->hasAttribute('alt')) {
                $this->errors[] = (object) [
                  'type' => 'no alt',
                  'src'  => $src,
                  'html' => $html,
                  'recommendation' => 'Add an alt attribute to the img and add a description.'];
            } else {
                $alt = $img_node->getAttribute('alt');
                if (trim($alt) === "") {
                    $this->warnings[] = (object) [
                      'type' => 'empty alt',
                      'src'  => $src,
                      'html' => $html,
                      'recommendation' => 'If this image is integral to the content, please add a description.'];
                }
                // modified regex from https://stackoverflow.com/questions/6768779/test-filename-with-regular-expression
                if (preg_match("/^[\w,\s\-\.]+\.[A-Za-z]{3,}$/", trim($alt))) {
                    $this->errors[] = (object) [
                    'type' => 'filename alt',
                    'src'  => $src,
                    'html' => $html,
                    'recommendation' => 'Do not use image filename as the alt attribute, describe the image.'];
                }
            }
        }
    }
    
    /**
     * _get_outerHTML helper.
     * Consulted https://stackoverflow.com/questions/5404941/how-to-return-outer-html-of-domdocument
     * @param  DOMElement $dom_el
     * @return string
     */
    private function _get_outerHTML(\DOMElement $dom_el): string
    {
        $doc = new \DOMDocument();
        $doc->appendChild($doc->importNode($dom_el, true));
        return trim(str_replace(["\r"], '', $doc->saveHTML()));
    }
}
