# README

[![Build Status](https://travis-ci.com/p1ho/accessibility-checker.svg?branch=master)](https://travis-ci.com/p1ho/accessibility-checker)
[![Coverage Status](https://coveralls.io/repos/github/p1ho/accessibility-checker/badge.svg?branch=master)](https://coveralls.io/github/p1ho/accessibility-checker?branch=master)

# Table of Content
* [Introduction](#introduction)
* [Installation](#installation)
* [Usage](#usage)
* [Report Structure](#report-structure)
  * [Color Contrast Errors](#color-contrast-errors)
  * [Heading Structure Errors](#heading-structure-errors)
  * [Image Accessibility Errors](#image-accessibility-errors)
  * [Link Accessibility Errors](#link-accessibility-errors)
* [Development](#development)
* [Contributors](#contributors)

# Introduction

This is a [Web Accessibility](https://en.wikipedia.org/wiki/Web_accessibility) testing suite that evaluates the HTML string extracted from Content Management Systems. This is not meant to be an exhaustive accessibility check, but it aims to bring severe accessibility issues to light, as well as generate awareness for accessibility in general.

Currently, this is being developed with the goal to analyze body texts from *Drupal Nodes*. However, algorithms used in these programs may be useful for developing a more general purpose solution.

## Categories

1. **Color Contrast**: Since low color contrast can cause readability issues, this program will go through all the elements in the HTML and compare its text color with the background to see if its contrast level adheres to [WCAG 2.0 standard](https://www.w3.org/TR/WCAG/#contrast-minimum). Currently, it will only analyze inline styles, and will ignore the property 'opacity' due to the complexity it entails.

1. **Heading Structure**: For screen readers or other devices that do not render the page as the developers had intended, we owe it to them to make sure information on the page can be parsed correctly on their devices, too. Since Heading Structure (```<h1>``` through ```<h6>```) is pivotal in building the hierarchy for the page, this program will check if there are any headings that are skipped or misplaced.

1. **Image Accessibility**: We have to assume that people using the screen readers cannot see anything on the page (only hear it), and this includes pictures. Thus, images that are integral to the content must also contain a description in its 'alt' attribute. This program will raise errors for ```<img>``` that do not have alt attributes and will raise warnings for ```<img>``` that has an empty alt attribute (because it is possible for a picture to be there simply for aesthetics).

1. **Link Accessibility**: This checks 2 things: whether links are dead/unoptimized, and whether link texts are clear and descriptive. In particular, link text is also important for screen readers because usually there is an option to list all the links on a particular page; if all the links reads ***click here*** or ***more detail***, then it becomes impossible for the user to know whether the link is useful without actually accessing it. (In previous versions, HEAD requests were used; however, it turns out not all servers may implement HEAD requests correctly, or allow it at all, thus a regular GET request is used now, trading data size for consistency)

# Installation

This package is not currently published to [Packagist](https://packagist.org/), but can be included through composer by including the following lines in your `composer.json`

```JavaScript
    "require": {
        "p1ho/accessibility-checker": "@dev",
    }
    "repositories": [
        {
            "type": "vcs",
            "url": "link-to-this-repository"
        }
    ],
```
(Note: if you have other items in `"require"` and `"respositories"` already, just add them to the existing list)

# Usage

example:
```PHP
<?php

require_once __DIR__ . '/vendor/autoload.php';

use P1ho\AccessibilityChecker\ColorContrast;
use P1ho\AccessibilityChecker\HeadingStructure;
use P1ho\AccessibilityChecker\ImageAccessibility;
use P1ho\AccessibilityChecker\LinkAccessibility;

// initialize accessibility checkers
$color_contrast_checker     = new ColorContrast\Checker("AA"); // AA or AAA mode
$heading_structure_checker  = new HeadingStructure\Checker(1, true); // heading shift(1-5) and strict mode
$img_accessibility_checker  = new ImageAccessibility\Checker();
$link_accessibility_checker = new LinkAccessibility\Checker();

$html = "<p>Enter your html here</p>";
$dom = new DOMDocument();
libxml_use_internal_errors(true);
$dom->loadHTML($html);

$page_url = "url-where-page-is-taken-from"; // this is to check relative links.

$color_contrast_result      = $color_contrast_checker->evaluate($dom);
$heading_structure_result   = $heading_structure_checker->evaluate($dom);
$image_accessibility_result = $img_accessibility_checker->evaluate($dom);
$link_accessibility_result  = $link_accessibility_checker->evaluate($dom, $page_url);
```
# Report Structure

In general, the reporting schematics will take the following structure:
```JavaScript
{
  "passed": true | false,
  "errors": [
    // list of objects with error details
  ],
  // for image accessibility checker
  "warnings": [
    // list of objects with warning details
  ]
}
```

## Color Contrast Errors

### Invalid Style Properties

| Key | Value |
| --- | ----- |
| **type** | one of `["invalid color", "invalid size", "invalid weight"]` |
| **property** | one of `["background-color", "color", "font-size", "font-weight"]` |
| **tag** | name of tag such as `h1` or `p` |
| **text** | text inside the tag (also texts from nested tags) |
| **html** | raw html of the tag |
| **recommendation** | `"Fix the invalid <insert-property>."` |

### Bad Color Contrast

| Key | Value |
| --- | ----- |
| **type** | `"low contrast"` |
| **property** | `"AA"` or `"AAA"` (see [WCAG 2.0 conformance levels](https://www.ucop.edu/electronic-accessibility/standards-and-best-practices/levels-of-conformance-a-aa-aaa.html)) |
| **tag** | name of tag such as `h1` or `p` |
| **text** | text inside the tag (also texts from nested tags) |
| **html** | raw html of the tag |
| **text_is_large** | whether the bolding level or size of text makes it a *large* font |
| **contrast_ratio** | calculated contrast rounded to 2 decimal places (e.g. `1.23`) |
| **recommendation** | `"Contrast Ratio for this element must be at least <insert-value>"` |

## Heading Structure Errors

### Heading Unallowed

| Key | Value |
| --- | ----- |
| **type** | `"heading unallowed"` |
| **tag** | one of heading tags (e.g. `h1`) |
| **text** | text inside the tag (also texts from nested tags) |
| **html** | raw html of the tag |
| **recommendation** | `"Check and use only allowed headings (<insert-list-of-allowed-headings>)."` |

### Heading Inside Heading

| Key | Value |
| --- | ----- |
| **type** | `"heading inside heading"` |
| **tag** | one of heading tags (e.g. `h1`) |
| **text** | text inside the tag (also texts from nested tags) |
| **html** | raw html of the tag |
| **recommendation** | `"Do not put heading inside another heading."` |

### Heading Skipped

| Key | Value |
| --- | ----- |
| **type** | `"heading skipped"` |
| **tag** | one of heading tags (e.g. `h1`) |
| **text** | text inside the tag (also texts from nested tags) |
| **html** | raw html of the tag |
| **recommendation** | If skipped h3, it would be `"<h3> is expected before the placement of this heading."` |

### Heading Too Shallow

| Key | Value |
| --- | ----- |
| **type** | `"heading too shallow"` |
| **tag** | one of heading tags (e.g. `h1`) |
| **text** | text inside the tag (also texts from nested tags) |
| **html** | raw html of the tag |
| **recommendation** | `"Try nesting this heading deeper."` |

### Heading Too Deep

| Key | Value |
| --- | ----- |
| **type** | `"heading too deep"` |
| **tag** | one of heading tags (e.g. `h1`) |
| **text** | text inside the tag (also texts from nested tags) |
| **html** | raw html of the tag |
| **recommendation** | `"Try nesting this heading shallower."` |

### Heading Misplaced

| Key | Value |
| --- | ----- |
| **type** | `"heading misplaced"` |
| **tag** | one of heading tags (e.g. `h1`) |
| **text** | text inside the tag (also texts from nested tags) |
| **html** | raw html of the tag |
| **recommendation** | `"Try nesting this heading shallower."` |

### Invalid Heading

| Key | Value |
| --- | ----- |
| **type** | `"invalid heading"` |
| **tag** | one of heading tags (e.g. `h1`) |
| **text** | text inside the tag (also texts from nested tags) |
| **html** | raw html of the tag |
| **recommendation** | `"Use valid headings only (<h1> through <h6>).` |

## Image Accessibility Errors

### No Alt Text (Error)

| Key | Value |
| --- | ----- |
| **type** | `"no alt"` |
| **src** | values inside `src` attribute |
| **html** | raw html of the tag |
| **recommendation** | `"Add an alt attribute to the img and add a description."` |

### Empty Alt Text (Warning)

| Key | Value |
| --- | ----- |
| **type** | `"empty alt"` |
| **src** | values inside `src` attribute |
| **html** | raw html of the tag |
| **recommendation** | `"If this image is integral to the content, please add a description."` |

## Link Accessibility Errors

### Redirect

| Key | Value |
| --- | ----- |
| **type** | `"redirect"` |
| **href** | value inside `href` attribute |
| **text** | text inside the tag (also texts from nested tags) |
| **html** | raw html of the tag |
| **recommendation** | `"Use the final redirected link."` |

### Dead

| Key | Value |
| --- | ----- |
| **type** | `"dead"` |
| **href** | value inside `href` attribute |
| **text** | text inside the tag (also texts from nested tags) |
| **html** | raw html of the tag |
| **recommendation** | `"Find an alternative working link."` |

### Domain Overlap

| Key | Value |
| --- | ----- |
| **type** | `"domain overlap"` |
| **href** | value inside `href` attribute |
| **text** | text inside the tag (also texts from nested tags) |
| **html** | raw html of the tag |
| **recommendation** | `"Use relative URL."` |

Note: This is to make sure other pages in the same domain are linked via relative paths instead of absolute paths.

### Slow Connection

| Key | Value |
| --- | ----- |
| **type** | `"slow connection"` |
| **href** | value inside `href` attribute |
| **text** | text inside the tag (also texts from nested tags) |
| **html** | raw html of the tag |
| **recommendation** | `"Troubleshoot why the page takes so long to load."` |

Note: The checker uses the HEAD request to fetch meta data for a page, this should not take long; thus, if the checker times out after 5 seconds, the checker will deem the link as slow.

### Poor Link Text

| Key | Value |
| --- | ----- |
| **type** | `"poor link text"` |
| **href** | value inside `href` attribute |
| **text** | text inside the tag (also texts from nested tags) |
| **html** | raw html of the tag |
| **recommendation** | `"Use more descriptive and specific wording."` |

Note: at least 2/3 of the words in the text are in the black list.

**Black List**:
* check
* click
* detail
* details
* download
* find
* go
* here
* info
* information
* it
* learn
* link
* more
* now
* other
* page
* read
* see
* this
* view
* visit

### Url Link Text

| Key | Value |
| --- | ----- |
| **type** | `"url link text"` |
| **href** | value inside `href` attribute |
| **text** | text inside the tag (also texts from nested tags) |
| **html** | raw html of the tag |
| **recommendation** | `"Use real words that describe the link."` |

### Text Too Long

| Key | Value |
| --- | ----- |
| **type** | `"text too long"` |
| **href** | value inside `href` attribute |
| **text** | text inside the tag (also texts from nested tags) |
| **html** | raw html of the tag |
| **recommendation** | `"Shorten the link text."` |

Note: The current limit is 100 characters.

### Unclear PDF Link

| Key | Value |
| --- | ----- |
| **type** | `"unclear pdf link"` |
| **href** | value inside `href` attribute |
| **text** | text inside the tag (also texts from nested tags) |
| **html** | raw html of the tag |
| **recommendation** | `"Include the word "PDF" in the link"` |

### Unclear Download Link

| Key | Value |
| --- | ----- |
| **type** | `"unclear download link"` |
| **href** | value inside `href` attribute |
| **text** | text inside the tag (also texts from nested tags) |
| **html** | raw html of the tag |
| **recommendation** | `"Include the word "download" in the link."` |

# Development

* `$ composer install` to install all dependencies

* `$ composer test` will run all the tests (use `$ composer test-win` on Windows). To enable coverage analysis, a code coverage driver is needed. I used [Xdebug](https://xdebug.org/index.php) when developing on Windows. Afterwards, run `$ composer phpcov-merge` (use `$ composer phpcov-merge-win` on Windows) to merge `build/cov/coverage.cov` with `build/logs/clover.xml` as instructed on [php-coveralls doc](https://packagist.org/packages/php-coveralls/php-coveralls).

* Run `$ composer style-fix-download` to download the latest php-cs-fixer file to project directory. Afterwards, you can run `$ composer style-fix` to auto style fix all your code.

# Contributors
|[![](https://github.com/p1ho.png?size=50)](https://github.com/p1ho)|
|:---:|
|[p1ho](https://github.com/p1ho)|
