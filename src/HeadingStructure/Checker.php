<?php declare(strict_types=1);

namespace P1ho\AccessibilityChecker\HeadingStructure;

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

class Checker
{

    // Keeps track of errors
    private $errors;

    // Heading Shift (User provided, See __construct())
    private $heading_shift;

    // Keeps track of how deep headings are nested in the structure
    private $heading_level_structure;

    // Keeps track of order headings appear (regardless of how they're nested)
    private $heading_order;

    // strict mode
    private $is_strict;

    // Block elements
    private $block_elements;

    /**
     * __construct function
     * @param int  $heading_shift (1 - 5; corresponds to h2 - h6)
     * [Number of heading levels to shift. For example, if 1 is given, we shift
     * heading level by 1, meaning the first heading we expect is h2]
     * @param bool $is_strict
     * [whether we allow different headings to exist at the same nesting level]
     */
    public function __construct(int $heading_shift = 1, bool $is_strict = false)
    {
        require __DIR__ . "/../FontHelpers/block_elements.php";
        $this->block_elements = $block_elements;
        if ($heading_shift < 1) {
            $this->heading_shift = 1;
        } elseif ($heading_shift > 6) {
            $this->heading_shift = 6;
        } else {
            $this->heading_shift = $heading_shift;
        }
        $this->is_strict = $is_strict;
    }

    /**
     * Check if heading structure is well formed
     *
     * Checks include:
     * - starts at user defined heading (default: <h2>), ends at <h6>
     * - no heading rank skipped (e.g., from <h3> directly to <h5>)
     * - higher ranked headings are not nested deeper than lower ranked heading
     *    (e.g., no <h3> nested deeper than <h4>)
     * - no headings are encapsulated in another heading
     * - *if strict mode on, same level contains only 1 type of heading
     *
     * @param  DOMDocument $dom [The whole parsed HTML DOM Tree]
     * @return array
     *
     */
    public function evaluate(\DOMDocument $dom): array
    {
        $this->errors = [];
        $this->heading_level_structure = [$this->heading_shift => 0];
        $this->heading_order = array_slice([1,2,3,4,5,6], 0, $this->heading_shift);

        if (count($dom->getElementsByTagName('body')) > 0) {
            $body = $dom->getElementsByTagName('body')[0];
        } else {
            $body = null;
        }

        $eval_array = [];
        if ($body === null) {
            $eval_array['passed'] = true;
            $eval_array['errors'] = [];
        } else {
            $this->_eval_DOM($body, $this->heading_shift + 1, 1, false);
            $eval_array['passed'] = (count($this->errors) === 0);
            $eval_array['errors'] = $this->errors;
        }
        return $eval_array;
    }

    /**
     * Recursive DOM Element parsing helper
     * @param  DOMElement $dom_el
     * @param  int $expected_heading_rank
     * @param  int $nested_level
     * @param  bool $is_in_h [Current DOM Element is wrapped in a <h> tag]
     * @return void
     */
    private function _eval_DOM(\DOMElement $dom_el, int $expected_heading_rank, int $nested_level, bool $is_in_h): void
    {
        if (get_class($dom_el) === 'DOMComment') {
            return;
        } // skip comments

        // pre-define some variables
        $tag_name = $dom_el->tagName;
        $text = trim($this->_get_text_content($dom_el));
        $html = $this->_get_outerHTML($dom_el);
        $expected_tag = 'h' . $expected_heading_rank;

        // if element is heading
        if ($this->_is_heading_tag($tag_name)) {
            $heading_rank = (int) $tag_name[1];
            $allowed_headings = array_slice([1,2,3,4,5,6], $this->heading_shift);
            if (!in_array($heading_rank, $allowed_headings)) {
                if (count($allowed_headings) > 0) {
                    $allowed_headings_text = implode(', ', array_map(function ($x) {
                        return "<h$x>";
                    }, $allowed_headings));
                } else {
                    $allowed_headings_text = "no headings are allowed";
                }
                $this->errors[] = (object) [
                  'type' => 'heading unallowed',
                  'tag' => $tag_name,
                  'text' => $text,
                  'html' => $html,
                  'recommendation' => "Check and use only allowed headings ($allowed_headings_text)."
                ];
            } else {
                /*
                Check if this heading element is inside another heading tag,
                if so, it will add an error to the log.
                */
                if ($is_in_h) {
                    $this->errors[] = (object) [
                      'type' => 'heading inside heading',
                      'tag' => $tag_name,
                      'text' => $text,
                      'html' => $html,
                      'recommendation' => 'Do not put heading inside another heading.'
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
                    $prev_rank = $this->heading_order[count($this->heading_order) - 1];
                    $this->heading_order[] = $heading_rank; // record heading appearance order
                    $rank_diff = $heading_rank - $prev_rank;
                    if ($rank_diff > 1) {
                        for ($i = $rank_diff - 1; $i > 0; $i--) {
                            $missing_rank   = $heading_rank - $i;
                            $this->errors[] = (object) [
                              'type' => 'heading skipped',
                              'tag' => $tag_name,
                              'text' => $text,
                              'html' => $html,
                              'recommendation' => "<h$missing_rank> is expected before the placement of this heading."
                            ];
                        }
                    } else {
                        if (isset($this->heading_level_structure[$heading_rank])) {
                            /*
                            If yes: we check if the current_nested_level is the same as the one
                            set in heading_level_structure. If they're not the same, we
                            know there is an inconsistency. If this test passed, we
                            check if heading_rank is the same as expected_heading_rank. In
                            any case, we increment the expected_heading_rank because we've
                            confirmed that this tag is a heading tag, even if its number
                            is incorrect.
                            */
                            $recorded_nested_level = $this->heading_level_structure[$heading_rank];
                            if ($nested_level < $recorded_nested_level) {
                                $this->errors[] = (object) [
                                  'type' => 'heading too shallow',
                                  'tag' => $tag_name,
                                  'text' => $text,
                                  'html' => $html,
                                  'recommendation' => "Try nesting this heading deeper."
                                ];
                            } elseif ($nested_level > $recorded_nested_level) {
                                $this->errors[] = (object) [
                                  'type' => 'heading too deep',
                                  'tag' => $tag_name,
                                  'text' => $text,
                                  'html' => $html,
                                  'recommendation' => "Try nesting this heading shallower."
                                ];
                            } else {
                                if ($this->is_strict) {
                                    /*
                                    strict mode:
                                    if heading_rank is smaller than expected_heading_rank, log error.
                                    There is already a check for skipped heading from line 128-137
                                    The non-strict scenario is already captured in the previous 2 tests.
                                    */
                                    if ($heading_rank < $expected_heading_rank) {
                                        $this->errors[] = (object) [
                                          'type' => 'heading misplaced',
                                          'tag' => $tag_name,
                                          'text' => $text,
                                          'html' => $html,
                                          'recommendation' => "Try nesting this heading shallower."
                                        ];
                                    }
                                }
                            }
                        } else {
                            if ($this->is_strict) {
                                /*
                                strict mode:
                                if heading_rank is smaller than expected_heading_rank, log error.
                                Or, if previous heading has been set, but the nested level of the
                                current heading is lower than the previous heading, log error.

                                There is already a check for skipped heading from line 128-137
                                The non-strict scenario is already captured in the previous 2 tests.
                                */
                                if ($heading_rank < $expected_heading_rank) {
                                    $this->errors[] = (object) [
                                      'type' => 'heading misplaced',
                                      'tag' => $tag_name,
                                      'text' => $text,
                                      'html' => $html,
                                      'recommendation' => "Try nesting this heading shallower."
                                    ];
                                } else {
                                    if (isset($this->heading_level_structure[$heading_rank - 1])) {
                                        if ($nested_level <= $this->heading_level_structure[$heading_rank - 1]) {
                                            $this->errors[] = (object) [
                                              'type' => 'heading too shallow',
                                              'tag' => $tag_name,
                                              'text' => $text,
                                              'html' => $html,
                                              'recommendation' => "Try nesting this heading deeper."
                                            ];
                                        } else {
                                            /*
                                            When all previous tests passed, log where this heading is.
                                            This means the nesting level of this heading is now regarded
                                            as correct and canonical.
                                            */
                                            $this->heading_level_structure[$heading_rank] = $nested_level;
                                        }
                                    }
                                }
                            } else {
                                $this->heading_level_structure[$heading_rank] = $nested_level;
                            }
                        }
                    }
                }
            }
            $is_in_h = true;
            $expected_heading_rank++;
        } else {
            // edgecase: if an invalid headtag is entered (e.g., <h7>), show an error
            if ($tag_name[0] === 'h' && ctype_digit(substr($tag_name, 1))) {
                $this->errors[] = (object) [
                  'type' => 'invalid heading',
                  'tag' => $tag_name,
                  'text' => $text,
                  'html' => $html,
                  'recommendation' => "Use valid headings only (<h1> through <h6>)."
                ];
            }
            /*
            if it's not a heading tag but its nested level was used by another heading
            previously, we increment expected heading by 1.
            */
            $heading_level_structure_flip = array_flip($this->heading_level_structure);
            if (isset($heading_level_structure_flip[$nested_level])) {
                $expected_heading_rank++;
            }
        }
        // base case (if element has no children, will simply return)
        $child_elements = $this->_get_childElements($dom_el);
        foreach ($child_elements as $child_element) {
            $this->_eval_DOM($child_element, $expected_heading_rank, $nested_level + 1, $is_in_h);
        }
    }

    /**
     * _get_childElements helper. Because DOMElement->childNodes also returns
     * DOMText which is not what we want, this helps with filtering those out.
     * @param  DOMElement $dom_el
     * @return array      [Array containing only DOMElement objects]
     */
    private function _get_childElements(\DOMElement $dom_el): array
    {
        $child_nodes = $dom_el->childNodes;
        $child_elements = [];
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
     * @param  string  $s [tag name]
     * @return bool
     */
    private function _is_heading_tag(string $s): bool
    {
        return preg_match('%^h[1-6]{1}$%iu', $s) === 1;
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
    private function _get_text_content(\DOMElement $dom_el): string
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
    private function _get_outerHTML(\DOMElement $dom_el): string
    {
        $doc = new \DOMDocument();
        $doc->appendChild($doc->importNode($dom_el, true));
        return trim(str_replace(["\r"], '', $doc->saveHTML()));
    }
}
