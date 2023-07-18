# Changelog

## Minifyhtml 2.0.2, 2023-04-12

Changes since 2.0.1:

- Issue #3334480: Settings form should not use overridden config
- Issue #3258507: HTML source code minification does not work after the last
update
- Issue #3339543: Fix coding standards

## Minifyhtml 2.0.1, 2022-06-20

Changes since 2.0.0

- Issue #3291473: TypeError :
Drupal\minifyhtml\EventSubscriber\MinifyHTMLExit::__construct(): Argument #3
($logger) must be of type Drupal\Core\Logger\LoggerChannelFactory

## Minifyhtml 2.0.0, 2022-06-16

Changes since 8.x-1.12:

- Issue #3288665: Automated Drupal 10 compatibility fixes
- Issue #3290946: Fix constructor signature version version 9/10

## Minifyhtml 8.x-1.12, 2022-06-16

Changes since 8.x-1.11:

- Issue #3230610: Minify Settings Ignore Admin Pages

## Minifyhtml 8.x-1.11, 2022-01-10

Changes since 8.x-1.10:

- Issue #3188015: README file mode is 755, should be 644
- Issue #3188014: Set strip comments a dependent option
- Issue #3202188: Change order Callback for replace placeholders in
placeholders
- Issue #3207580: JSON-LD not getting minified
- Issue #3135600: core key no longer required in info file
- Issue #3188013: Move module settings to the main performance options page

## Minifyhtml 8.x-1.10, 2020-05-14

Changes since 8.x-1.9:

- Issue #3038554: Create tests
- Issue #3133705: minifyhtml - drush commands don't exist and/or don't work
(still)

## Minifyhtml 8.x-1.9, 2019-12-11

Changes since 8.x-1.8:

- Issue #3100049: D9 Compatibility
- Issue #3071696: Drupal 9 Deprecated Code Report
- Issue #3060004: Module config done incorrectly

## Minifyhtml 8.x-1.8, 2019-06-06

Changes since 8.x-1.7:

- Issue #3041131 - Fail Less Hard
- Issue #3046329 - minifyhtml - what are the drush commands

## Minifyhtml 8.x-1.7, 2019-03-08

Changes since 8.x-1.6:

- Issue #3025864 - Incompatible with Gtranslate module
- Issue #3038512 - Remove HTML comments option removes style or script code
- Issue #3038527 - Make MinifyHTMLExit properties protected

## Minifyhtml 8.x-1.6, 2018-12-17

Changes since 8.x-1.5:

- Issue #3020795 - After update to 8.x-1.5

## Minifyhtml 8.x-1.5, 2018-12-14

Changes since 8.x-1.4:

- Issue #3020570 - Array key conflict

## Minifyhtml 8.x-1.4, 2018-12-13

Changes since 8.x-1.3:

- Issue #3020310 - WSOD caused by PCRE backtrace limit.
- Issue #3013716 - Add configuration option to leave comments in place
- Issue #3016910 - Coding standard

## Minifyhtml 8.x-1.3, 2018-10-02

Changes since 8.x-1.2:

- Issue #3010030 - Missing "core" key in module info.yml file

## Minifyhtml 8.x-1.2, 2018-09-19

Changes since 8.x-1.1:

- Issue #2973794 - system.performance:minifyhtml missing schema
- Issue #3000443 - Only use minified HTML if there was not a preg_replace()
error

## Minifyhtml 8.x-1.1, 2017-08-18

Changes since 8.x-1.0:

- Issue #2900653 - Config for JS- and CSS-Aggregations not saved

## Minifyhtml 8.x-1.0, 2017-07-26

- Initial port to Drupal 8.
