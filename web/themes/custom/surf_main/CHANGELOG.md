# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## 4.x - [Unreleased]
The 4.x release marks a move from Prototype as a starter-kit to Prototype as an actual base theme. It also shifts away from a heavy use of custom Sass mixins and functions, instead embracing future enhancements to the CSS spec enabled by the [PostCSS Env](https://github.com/csstools/postcss-preset-env) plugin. *It is not recommended to update from Prototype 8.2.x to 4.x.* Without creating a brand new sub theme. There are too many breaking changes. In reality, if you are already using Prototype 2.x, there is very little code in Prototype that would not already be overriden by your existing sub-theme.

It is the intention of the project maintainers to release incremental enhancements which, due to the nature of themes, have a high risk of breaking changes. We will do our best to document those here.

### Added

- Drupal 9 support.
- This `CHANGELOG.md`!
- Build tool configuration additions.
    - [Browserlist](https://github.com/browserslist/browserslist) support to control the list of browsers targeted by [Babel](https://babeljs.io/), [PostCSS Env](https://github.com/csstools/postcss-preset-env) and [Autoprefixer](https://github.com/postcss/autoprefixer).
    - Additional [Babel](https://babeljs.io/) overrides can be configured with `.babelrc` if Browserlist is insufficient.
    - Support for setting [Browsersync](https://browsersync.io/) configuration in an `.env` file to make the build tools more portable across environments.
    - A minimal set of [Tailwind CSS](https://tailwindcss.com/) utility classes. These can be modified via `tailwind.config.js`.
    - [Webpack](https://webpack.js.org/) support for JS bundling. This can be controlled via `webpack.config.js`
- Accessible JS libraries added.
    - (WIP) Added `toggle.js` library.
    - (WIP) Added `tabbed.js` library.
    - (WIP) Added `a11y-dialog.js` library.
- Layout utility classes have been added based on patterns described in [Every Layout](https://every-layout.dev/) by Heydon Pickering and Andy Bell.
    - `.l-stack` for [controlling vertical spacing](https://every-layout.dev/layouts/stack/).
    - `.l-cluster` for [consistent spacing](https://every-layout.dev/layouts/cluster/) between horizontal flowing block elements.
    - `.l-grid` for [multi-column grids](https://every-layout.dev/layouts/grid/).
- (WIP) Cypress tests have been added.
- Styled: Status Messages

### Changed

- Most Sass variables have been replaced with [CSS Custom properties](https://developer.mozilla.org/en-US/docs/Web/CSS/Using_CSS_custom_properties). These Properties follow a naming convention based on the ["Naming Tokens in Design Systems"](https://medium.com/eightshapes-llc/naming-tokens-in-design-systems-9e86c7444676) article by Nathan Curtis.
- (WIP) CSS classes have been namespaced – loosely following the conventions outlined in the ["More Transparent UI Code with Namespaces"](https://csswizardry.com/2015/03/more-transparent-ui-code-with-namespaces/) article by Harry Roberts.
    - `.c-` for component classes.
    - `.l-` for layout classes.
    - `.t-` for themes and color schemes.
    - `.u-` for utility classes.
    - `.is-` and `.has` for state classes.
    - `.js-` for attaching JavaScript behaviors.
- `.l-constrained` has been rewritten to better adapt to dynamic changes to it's container.
- (WIP) Form css has been rewritten from the ground up.
- (WIP) Base text styles have been rewritten.
- The front-end build tools have been completely updated to provide more control via tool specific config files such as `.browserlistrc` and `.postcssrc.js` and less reliance on editing gulp files directly or editing `config.js`, although it still may be necessary in some cases.
    - [Browserify](https://browserify.org/) has been replaced by [Webpack](https://webpack.js.org/) as the main JavaScript bundler.
    - [Node-sass](https://github.com/sass/node-sass) has been replaced with [Dart-sass](https://sass-lang.com/dart-sass).
    - `sass-lint` CSS linting has been replaced with `.stylelintrc.json` to conform with Drupal standards. However we do prefer alphabetized CSS properties over Drupal's groupings.
- The structure of `prototype.libraries.yml` has been refactored to allow sub-themes more granular control when overriding and extending libraries. This includes more appropriate use of [CSS library namespaces](https://www.drupal.org/docs/develop/standards/css/css-file-organization-for-drupal-9) for more control on source order.
- `.header` and `.footer` have been renamed to `.c-site-header` and `.c-site-footer` to better distinguish them from other header and footer elements.

- Gulp tasks have been moved into a folder named `gulpfile.js`.

### Removed

- The STARTER_KIT folder and bash script have been removed as this prevented Prototype from being installed as a stand-alone theme without a sub-theme. New sub-themes can be generated using the [Prototype Sub-theme](https://github.com/AtenDesignGroup/generator-prototype-subtheme) Yeoman generator.
- [Breakpoint-sass](http://breakpoint-sass.com/) along with the `bp()` mixin have been removed as it is no longer maintained. This functionality is replaced by added support for [Custom Media Queries](https://github.com/postcss/postcss-custom-media) and [Media Minmax](https://github.com/postcss/postcss-media-minmax) via PostCSS Env
- All font related Sass mixins and their related Sass maps and variables have been removed in favor of CSS custom properties.
- `global.js` was removed. This was an empty JS file that encouraged bad practices of loading JS globally and was often removed from subthemes anyway.
- The build process no longer attempts code splitting JS modules into common chunks. Instead developers are encouraged to load necessary external JS libraries as UMD modules and declare dependencies using Drupal's libraries API.  If your scripts are complicated enough to warrant code-splitting, they are most likely providing functionality that is more complicated than what the theme layer should provide. These scripts are likely more suited to a custom module.

## [8.2.x] - 2017-09-20

### Added

- Added STARTER_KIT folder and related bash script.

## [8.1.x] - 2017-04-18

- Initial Drupal 8 release.

## [7.3.x] - 2017-04-18

### Changed

- Replaced [Compass](http://compass-style.org/) with [Libsass](https://sass-lang.com/libsass)

## [7.1.x] - 2017-04-18

- Initial Drupal 7 release.

[unreleased]: https://git.drupalcode.org/project/prototype/-/compare/8.x-2.x...4.x?from_project_id=57591
[8.2.x]: https://git.drupalcode.org/project/prototype/-/compare/8.x-1.x...8.x-2.x?from_project_id=57591
[8.1.x]: https://git.drupalcode.org/project/prototype/-/compare/7.x-3.x...8.x-1.x?from_project_id=57591
[7.3.x]: https://git.drupalcode.org/project/prototype/-/compare/7.x-1.x...7.x-3.x?from_project_id=57591
[7.1.x]: https://git.drupalcode.org/project/prototype/-/tree/7.x-1.x
