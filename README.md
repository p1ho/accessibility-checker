# Accessibility Checker for PHP

This is a simple self-contained program that takes in HTML in string form and a domain name where the HTML is taken from, and will run accessibility tests on the HTML. This is not meant to be an exhaustive accessibility check, but it aims to bring severe accessibility issues to light, as well as generate awareness for accessibility in general.

Currently, this is being developed with the goal to analyze body texts from *Drupal Nodes*. However, algorithms used in these programs may be useful for developing a more general propose solution.

## What's inside

There are 4 accessibility categories being checked:

1. **Color Contrast**: Since low color contrast can cause readability issues, this program will go through all the elements in the HTML and compare its text color with the background to see if its contrast level adheres to [WCAG 2.0 standard](https://www.w3.org/TR/WCAG/#contrast-minimum). Currently, it will only analyze inline styles, and will ignore the property 'opacity' due to the complexity it entails.

1. **Heading Structure**: For screen readers or other devices that do not render the page as the developers had intended, we owe it to them to make sure information on the page can be parsed correctly on their devices, too. Since Heading Structure (```<h1>``` through ```<h6>```) is pivotal in building the hierarchy for the page, this program will check if there are any headings that are skipped or misplaced.

1. **Image Accessibility**: We have to assume that people using the screen readers cannot see anything on the page (only hear it), and this includes pictures. Thus, images that are integral to the content must also contain a description in its 'alt' attribute. This program will raise errors for ```<img>``` that do not have alt attributes and will raise warnings for ```<img>``` that has an empty alt attribute (because it is possible for a picture to be there simply for aesthetics).

1. **Link Accessibility**: This checks 2 things: whether links are dead/unoptimized, and whether link texts are clear and descriptive. In particular, link text is also important for screen readers because usually there is an option to list all the links on a particular page; if all the links reads 'click here' or 'more detail', then it becomes impossible for the user to know whether the link is useful without actually accessing it.

## How to use?

example:
```
$domain = "https://enter-your-url.com/";
$html = "<p>Enter your html here</p>";

require "accessibility-checker/AccessibilityChecker.php";
AccessibilityChecker::init();
AccessibilityChecker::set_domain($domain);
AccessibilityChecker::load_html($html);

$color_contrast_result      = AccessibilityChecker::evaluate_color_contrast();
$heading_structure_result   = AccessibilityChecker::evaluate_heading_structure();
$image_accessibility_result = AccessibilityChecker::evaluate_image_accessibility();
$link_accessibility_result  = AccessibilityChecker::evaluate_link_accessibility();
```

## Unit Tests

Unit tests were written for some parts that require testing (such as checking color contrast), all the tests can be bulk-run by running the ```unit_tests.sh``` found at the root folder.

## Dependencies

- [PHPUnit](https://phpunit.de/)
- [Zebra_cURL](https://github.com/stefangabos/Zebra_cURL)

## UPDATE

- There was a performance issue with link checker because it checked links sequentially instead of in parallel. [Zebra_cURL](https://github.com/stefangabos/Zebra_cURL) has been integrated which does both multiple curl requests in parallel and caching.

- Updated error messages to object so as to allow more flexibility on the front end in terms of error display.

## TODO

- (Debating) Image Accessibility Checker should also check if the image is dead, will implement after [Zebra_cURL](https://github.com/stefangabos/Zebra_cURL) is successfully implemented for Link Accessibility.
