# The following are commands to run unit tests on this package.
# Assumes Gitbash is installed
#
# simply type "./tests.sh" into your terminal where this file is located

echo "Testing Color Contrast Helpers"
vendor/bin/phpunit color-contrast/color-contrast-helpers/tests
echo "------------------------------"

echo "Testing Color Contrast Checker"
vendor/bin/phpunit color-contrast/tests
echo "------------------------------"

echo "Testing Heading Structure Checker"
vendor/bin/phpunit heading-structure/tests
echo "------------------------------"

echo "Testing Link Accessibility Checker"
vendor/bin/phpunit link-accessibility/link-text/tests
echo "------------------------------"

read -p "Tests Completed. Press Enter to continue."
