# ADVANCED CSS/JS AGGREGATION MODULE

## CONTENTS

 - [Core Features & Benefits]
 - [Submodule Overview]
 - [Configuration](#configuration)
 - [Information](#information)
 - [Operations](#operations)
 - [Server Settings]
 - [Tips & Tricks]
    - [JavaScript Bookmarklet]
    - [How to get a high PageSpeed score]
 - [Advagg Bundler](#bundler)
 - [AdvAgg CDN](#cdn)
 - [AdvAgg CSS/JS Validator](#validator)
 - [AdvAgg External Minifier](#minify-external)
 - [AdvAgg Minify CSS](#minify-css)
 - [AdvAgg Minify JS](#minify-js)
 - [AdvAgg Modifier](#modifier)
 - [AdvAgg Old IE Compatibility Enhancer](#ie-compatibility)

## CORE FEATURES & BENEFITS

 - AdvAgg for Drupal 8 has some significant differences from Drupal 7; instead
   of totally reworking asset handling, AdvAgg only applies some improvements.
   This is mostly because the core handling is much better. It is also better
   for compatibility with quite a few other modules such as http2_server_push.

 - Url query string to turn off AdvAgg for that request. `?advagg=0` will
   turn off file optimization if the user has the "bypass advanced aggregation"
   permission. `?advagg=-1` will completely bypass all of Advanced CSS/JS
   Aggregations modules and submodules. `?advagg=1` will enable Advanced CSS/JS
   Aggregation if it is currently disabled.

 - Button on the admin page for dropping a cookie that will turn off file
   optimization. Useful for theme development.

 - Gzip support. All optimized files can be pre-compressed into a .gz file and
   served from the server. This is faster then gzipping the file on each
   request.

 - Brotli support. All optimized files can be pre-compressed with the newer
   brotli compression algorithm; this often shows close to 30% improvements
   in file size vs gzip. Requires the brotli php extension is installed.

 - With various submodules substantially improved front end performance through
   minification of assets and other techniques can be achieved. Read more below.

## SUBMODULE OVERVIEW

AdvAgg comes with quite a few submodules to do various tasks. Most are
compatible with the vast majority of other mods but some may have conflicts.
If so those are noted here and more details in the module's full write-ups.

#### AdvAgg Bundler (advagg_bundler)

*This module may have conflicts with other modules (but none known).*

Overrides the core aggregation algorithm to provide a user chosen number of
agreggates for both JS and CSS. Most beneficial for servers that support
HTTP2.

#### AdvAgg Cdn (advagg_cdn)

*This module may have conflicts, but only with other CDN type modules.*

Load CSS or JavaScript libraries from a public CDN; currently only supports
jQuery and jQuery UI with either Google's or Microsoft's CDN.

#### AdvAgg CSS/JS Validator (advagg_validator)
Validate CSS and or JS files in your site through a few different validators.

#### AdvAgg External Minifier (advagg_ext_minify)
Minify CSS or JS through an otherwise unsupported method, with a configurable
command line.

#### AdvAgg Minify CSS (advagg_css_minify)
Minify the  CSS files using a 3rd party minifier; currently supports YUI
(included) or the Core minification algorithm.

#### AdvAgg Minify JS (advagg_js_minify)
Compress the compiled JavaScript files using a 3rd party minifier; built in
support for a number of minifiers.

#### AdvAgg Modifier (advagg_mod):

*Depending on tweaks applied this module may cause compatibility issues with
other modules.*

Includes additional tweaks that may not work for all sites such as:
  - Force preprocessing for all CSS/JS.
  - Add defer tag to all JS.
  - Defer CSS loading using `rel=preload` and JavaScript Polyfill.
  - Add async tag to all or only local JavaScript.

#### AdvAgg Old IE Compatibility Enhancer (advagg_old_ie_compatibility)

*This module may have conflicts with other modules if it is enabled.*

Includes additional functionality to improve Drupal compatibility with old
versions of Internet Explorer (6-9).


## CONFIGURATION {#configuration}

Settings page is located at: `admin/config/development/performance/advagg`.

### Global Options

 - **Enable Advanced Aggregation:** You can quickly temporarily disable all
   AdvAgg functionality by un-checking this. For testing purposes, see also
   <a href="#testing">Testing AdvAgg</a>. [Default: enabled]

 - **Use DNS Prefetch for external CSS/JS:** If enabled places prefetch tags
   near the top of your html to trigger DNS look-ups as soon as possible on
   load. [Default: disabled]

 - **Server Config:** Server configuration options for AdvAgg. Mostly only
   available on Apache server.  For more details see [Server Settings].

   - **Include Cache-Control: immutable in generated .htaccess files.**
     *Apache only* include directives in .htaccess to send
     [Cache-Control Immutable](http://bitsup.blogspot.de/2016/05/cache-control-immutable.html)
     headers for all optimized files. Only supported on Edge 15+ and Firefox 49+.
     In other browsers, they will have no effect, so can be safely enabled.
     [Default: enabled]

 - **AdvAgg Cache Settings:**
   Development will scan all files for a change on every page load. Normal is
   fine for all use cases. Aggressive should be fine in almost all use cases.
   [Default: Normal]

### Compression Options

 - **Gzip [CSS/JS] Assets:** If enabled, AdvAgg will create gzip compressed
   version of every file that is generated. This will then be served if the
   users' browser supports it instead of the uncompressed file saving time
   and bandwidth. [Default: enabled]

 - **Brotli Compress [CSS/JS] Assets:** Only selectable if the non standard
   [PHP brotli extension](https://github.com/kjdev/php-ext-brotli) is installed.
   If enabled, Advagg will create brotli compressed versions of every file that
   is generated. Moderately better compression ratios than Gzip, but lower
   server and browser support. [Default: disabled]

### CSS Options

 - **Combine CSS files by using media queries:** If enabled, AdvAgg will add a
   media query wrapper to any file that needs it so that aggregation is more
   efficient. [Default: disabled]

 - **Fix improperly set type:** If type is external but does not start with
   http, https, or // change it to be type file. If type is file but it starts
   with http, https, or // change type to be external. [Default: enabled]

### JS Options

 - **Fix improperly set type:** If type is external but does not start with
   http, https, or // change it to be type file. If type is file but it starts
   with http, https, or // change type to be external.  [Default: enabled]

 - **Do not change external to file if on same host:** In rare cases, JS files
   may be on the same host but are actually still external such as served via
   a CDN.  [Default: disabled]

### CRON Options

Unless you have a good understanding of what you are trying to do, probably
better to leave these alone. For best performance, setting up an external cron
task is better than relying on Drupal's built in "poor man's cron".

 - **Minimum amount of time between `advagg_cron()` runs:** Set the minimum
   amount of time between `advagg_cron` runs. `advagg_cron` whenever you site
   cron runs, if less than the minimum time, will return before further
   processing. [Default: 1 day]

 - **Delete aggregates modified more than a set time ago:** Set how old to keep
   generated files for. Longer will have higher performance and with AdvAgg
   8.x-3.x should have no negatives except for possibly wasted disk space from
   rarely used or changed files. [Default: 1 month]

### Obscure Options

 - **Convert absolute paths to protocol relative paths:** Only applies to
   external files, this will change any http:// or https:// based urls to use
   //. [Default: enabled]

 - **Convert http:// to https://:** Also only applies to external files and is
   mutually incompatible with **Convert absolute paths to protocol relative
   paths**. [Default: disabled]

 - **Use "Options +FollowSymLinks":** On some shared hosting configurations
   the AdvAgg htaccess files need to use `Options +FollowSymlinks`.
   [Default: disabled]

 - **Use "Options +SymLinksIfOwnerMatch":** On some rare hosting configurations
   the AdvAgg htaccess files need to use `Options +SymLinksIfOwnerMatch`
   instead of the more common `Options +FollowSymlinks`. [Default: disabled]

## Information page {#information}

Located at `admin/config/development/performance/advagg/info`. This page
provides debugging information. There are no configuration options here.

 - **Hook Theme Info:** Displays the `preprocess_html` order.

 - **Core Asset Hooks Implemented by Modules:** List modules implementing the
   various core asset hook functions. *Note:* if a module does not use these
   but is changing things with assets beyond adding/removing, there is a much
   greater chance that there may be conflicts with AdvAgg especially with some
   of the submodules.

 - **Get information about an optimized file:** Enter the name of an optimized
   file and get back an array of information about the file including its
   original name and location.

## Operations page {#operations}

Located at `admin/config/development/performance/advagg/operations`. This is a
collection of commands to control the cache and manage testing of this
module. In general this page is useful when troubleshooting some aggregation
issues.

 - **Aggregation Bypass Cookie:** Toggle The "aggregation bypass cookie" for
     the current browser. If enabled will disable AdvAgg for the user for the
     period of time specified. It acts almost the same as adding ?advagg=0
     to every URL.

 - **Cron Maintenance Tasks:** Remove All Stale Files: Scan all files in the
       css/js optimized directories and remove old files. See also `Cron
       Options` on the Configuration Page.

### Drastic Measures

 - **Clear All Caches:** Remove all data stored in the advagg cache, and delete
   all optimized files.

 - **Increment Global Counter**: Force the creation of all new files with new
   names by incrementing a global counter.

## Developing your site with Advanced Agreggates

With the new architecture, Advanced Agreggates is much more intelligent when in
development. It will detect changed files, however, depending on settings it
may not be instant. Also, repeatedly optimizing a file for every minor change
when doing stylesheets or JavaScript isn't the most efficient option. So, what
can you do? If just one site instance (questionable but budgets etc), you may
want to just adjust the configuration to refresh caches a bit faster or disable
AdvAgg temporarily while doing heavy development.

That will work but, there are a few other methods that will work better for
some work flows especially if doing config import and export.

### Temporarily Disabling AdvAgg

 1. Via a local.settings.php. Often developers will disable core asset
    aggregation ith config overrides or enable various core development
    features. You can do the same to disable AdvAgg. For example some users use
    the following lines:

    ```php
    // Disable Core CSS and JS aggregation.
    $config['system.performance']['css']['preprocess'] = FALSE;
    $config['system.performance']['js']['preprocess'] = FALSE;

    // Disable AdvAgg.
    $config['advagg.settings']['enabled'] = FALSE;
    ```

 2. Via main config page, un-check `Enable Advanced Aggregates`. This will take
    effect right away for all users.

 3. Via the AdvAgg url parameter. Not as reliable depending on various system
    caches. To use, just append `?advagg=0` to your url.

 4. Using the browser cookie function from the Operations page. Similar to the
    url parameter this isn't as good as the first 2 options.

## SERVER SETTINGS

AdvAgg can adjust configuration to improve performance for Apache servers.
It does that through .htaccess files. There are reasons not to use .htaccess,
however for most sites those aren't an important issue as it is a performance
fine tuning issue. However for non-apache servers, .htaccess files don't work
and in the case of Nginx, there is no equivalent. Instead Nginx has all such
configuration within the server/vhost definition.

So if you're using Nginx read on for instructions on adding those to your vhost
or server settings. All of the below snippets go within the `server { }` block
in your config files.

### Cache Control Immutable
The [immutable cache control](http://bitsup.blogspot.de/2016/05/cache-control-immutable.html)
header is a fairly new header. Only Firefox & Edge
[currently support](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Cache-Control#Browser_compatibility)
it as of September 2017. Browsers that don't support it will ignore so it is
safe to enable.

To enable in nginx add the following to your server configuration:
```
   location ~ ^/sites/.*/files/(css|js)/optimized {
     add_header Cache-Control 'public, max-age=31536000, immutable';
   }
```
If you wanted this to also apply to agreggates as well as the individual
AdvAgg optimized files, just removed the `optimized` from the location
line.

### Serving Compressed Assets.
On Apache, AdvaAgg handles this pretty well. On other servers, it doesn't
however, if you've already configured your server to serve compressed assets
from Drupal core, likely it'll just work. On the other hand, many Nginx
default configurations may not be setup to serve static compressed assets.

### Brotli
At this point in time, serving Brotli assets will still require you to build
your Nginx server from source, with the
[Nginx Brotli module](https://github.com/google/ngx_brotli) - doable but beyond
the scope of this manual. Once you have that installed, configuring your
vhost/server settings is very easy, just add the following:
```
   location ~ ^/sites/.*/files/(css|js) {
     brotli_static on;
   }
```

### Gzip
Serving Gzip assets also requires a seperate module to be enabled it is part of
the main nginx code and there are distributions with it - for example on Ubuntu
use `apt-get install nginx-extras` to install a prebuilt version, otherwise you
can build your own custom version. See more details about the
[Nginx GZip Module](http://nginx.org/en/docs/http/ngx_http_gzip_static_module.html)
Configuring your server setting is equally easy and almost identical to the
[Brotli] configuration:
```
   location ~ ^/sites/.*/files/(css|js) {
     gzip_static on;
   }
```
If you want to serve either Gzip or Brotli depending on the user's browser just
use both declarations:
vhost/server settings is very easy, just add the following:
```
   location ~ ^/sites/.*/files/(css|js) {
     brotli_static on;
     gzip_static on;
   }
```

## TIPS & TRICKS

### JavaScript Bookmarklet

You can use this JS code as a bookmarklet for toggling the AdvAgg URL parameter.
See http://en.wikipedia.org/wiki/Bookmarklet for more details.
```javascript
    javascript:(function(){
      var loc = document.location.href,
          qs = document.location.search,
          regex_off = /\&?advagg=-1/,
          goto = loc;
      if(qs.match(regex_off)) {
        goto = loc.replace(regex_off, '');
      } else {
        qs = qs ? qs + '&advagg=-1' : '?advagg=-1';
        goto = document.location.pathname + qs;
      }
      window.location = goto;
    })();
```
### HOW TO GET A HIGH PAGESPEED SCORE

Go to `admin/config/development/performance/advagg`
 - check "Combine CSS files by using media queries"

Ensure AdvAgg Modifier is enabled and go to
`admin/config/development/performance/advagg/mod`
 - Under "Move JS to the footer" Select "All"
 - set "Enable preprocess on all JS/CSS"
 - set "Move JavaScript added by drupal_add_html_head() into drupal_add_js()"
 - Enable every checkbox under "Optimize JavaScript/CSS Ordering"

Ensure AdvAgg Minify JS is enabled and go to
`admin/config/development/performance/advagg/js-minify`
 - Select JSMin if available; otherwise select JSMin+

# ADVANCED AGGREGATES BUNDLER {#bundler}

## OVERVIEW

*This module may have conflicts with other modules (but none known).*

Overrides the core aggregation algorithm to provide a user chosen number of
agreggates for both JS and CSS. Most beneficial for servers that support
HTTP2. Since then you can go between full aggregation (best for browsers that
don't support HTTP2) and individual files (best for HTTP2).

If there are less asset files being attached than the user selected number of
aggregates, there will be one file per aggregate and less aggregates than the
user chose.

Conversely, depending on what aggregates and other settings it may not be
possible to reduce the number of aggregates. This situation can usually be
fixed by using the options in the AdvAgg Modifier to optimize JS ordering and
enable preprocess on all JS. Depending on rounding etc and order of agreggates,
the number may also be off by one.

## CONFIGURATION

Located at `admin/config/development/performance/advagg/bundler`.

 - **Bundler is Active:** Use the bundler. [Default: disabled]

### CSS BUNDLING Options

 - **Target Number of CSS Bundles Per Page:** How many aggregates should be
   made. [Default: 0 (Disabled)]

 - **Grouping Logic:** Which logic to use. File count is slightly quicker
   however will likely lead to more uneven file sizes. [Default: File Size]

### JAVASCRIPT BUNDLING Options

 - **Target Number of JS Bundles Per Page:** How many aggregates should be
   made. [Default: 0 (Disabled)]

 - **Grouping Logic:** Which logic to use. File count is slightly quicker
   however will likely lead to more uneven file sizes. [Default: File Size]

# ADVANCED AGGREGATES CDN {#cdn}

## OVERVIEW

*Note: This module may have conflicts, but only with other CDN type modules.*

Load CSS or JavaScript libraries from a public CDN; currently only supports
jQuery and jQuery UI with either Google's or Microsoft's CDN. Note the theme
for the jQuery UI CSS that Drupal core uses is not available from either CDN.
There is a similar one, `smoothness`, available which has only very minor
differences.

## CONFIGURATION

Located at `admin/config/development/performance/advagg/cdn`.

 - **CDN to use:** Which CDN to use. [Default: Google]

 - **Use Minified resources:** Use the minified versions of the resource if
   possible. [Default: enabled]

### jQuery

 - **Serve jQuery by CDN:** Use the CDN jQuery instead of the local copy.
   [Default: enabled]

 - **jQuery version:** The version to use, defaults to the version included in
   Drupal 8. [Default: 2.1.4]

### jQuery UI

 - **Serve jQuery UI CSS by CDN:** Use the CDN jQuery UI CSS instead of the
   local copy. _**Warning: this may change the appearance due to slight
   differences from the core theme. [Default: disabled]

 - **jQuery UI theme:** The theme to use; `Smoothness` is the most similar to
   the basic jQuery UI theme in Drupal 8. [Default: smoothness]

 - **Serve jQuery UI JavaScript by CDN:** Use the CDN jQuery UI JavaScript
   instead of the local copy. [Default: enabled]

 - **jQuery UI version:** The version to use, defaults to the version included
   in Drupal 8. [Default: 1.11.4]

# ADVANCED AGGREGATES CSS/JS VALIDATOR {#validator}

## OVERVIEW

Validate CSS and or JS files in your site through a few different validators.

## CSSLint

Use the CSSLint JavaScript and the Drupal 8 core .csslintrc settings to lint
the selected file(s) from your site.

## W3.org CSS VALIDATOR

*Warning: this has privacy implications. Specifically this sends your CSS to
the W3 servers.*

Use the W3.org CSS Validator to lint the selected file(s) from your site.

## JSHint

Use the JSHint JavaScript to lint the files on your site.

# ADVANCED AGGREGATES EXTERNAL MINIFIER {#minify-external}

## OVERVIEW

*Warning: depending on permissions this presents a security risk due to using
administrator configurable command line calls.*

*Note: May conflict with AdvAgg Minify CSS and or AdvAgg Minify JS depending on
configuration. Don't use 2 minifiers on one asset type.*

Minify CSS or JS through an otherwise unsupported method, with a configurable
command line. This may not work on all servers depending on security settings.

## CONFIGURATION

 ### CSS

 - **Command to run:** the shell command to run. The form provides some
   examples and lists the available variables. [Default: empty]

 - **Enable external CSS minification:** Whether to use the configured command.
   [Default: False]

 ### JavaScript

 - **Command to run:** the shell command to run. The form provides some
   examples and lists the available variables. [Default: empty]

 - **Enable external JavaScript minification:** Whether to use the configured
   command. [Default: False]

# ADVANCED AGGREGATES MINIFY CSS {#minify-css}

## OVERVIEW

Minify CSS files using a 3rd party minifier; currently supports YUI
(included) or the Core cleaning algorithm. YUI will lead to much smaller
files sizes and is the recommended option - basically, if you aren't using it,
you probably will get better performance without this sub-module enabled. The
YUI Compressor included with Advanced Aggregates is slightly different from
that found on GitHub (as of last check, one patch had not been applied to the
GitHub version).

## CONFIGURATION

Located at `admin/config/development/performance/advagg/css-minify`

 - **Minification: Select a minifier:** Choose between `Disabled`, `Core` and
   `YUI`. [Default: YUI]

 - **Add licensing Comments:** If enable will include comment with path to the
   original file in every optimized file. Better follows the spirit of GPL but
   not required to conform. Will increase file size slightly.
   [Default: Enabled]

# ADVANCED AGGREGATES MINIFY JAVASCRIPT {#minify-js}

## OVERVIEW

Minify JavaScript files using a 3rd party minifier. AdvAgg includes a few
and supports one faster compiled PHP extension. The options are:

 - [JSMin +](https://crisp.tweakblogs.net/blog/6861/jsmin%2B-version-14.html):
   No installation required. Usually the second best minification, however it
   is the slowest supported option.

 - [JShrink](https://github.com/tedious/JShrink): No installation required,
   JShrink is reliable and has fairly comparable minification to JSMin.

 - [JSqueeze](https://github.com/tchwork/jsqueeze): No installation required,
   JSqueeze provides the most effective minification and is the fallback if
   another minification method fails. It is still fairly slow.

 - [JSMin](https://github.com/sqmk/pecl-jsmin): The fast compiled C extension
   for PHP. Supports PHP 5.3+ and 7.x. Must be installed separately. Much
   faster than the other options but slightly less reliable although that is
   mostly mitigated within AdvAgg. See below for more details.

## CONFIGURATION

Located at `admin/config/development/performance/advagg/css-minify`

 - **Minification: Select a minifier:** Choose which minifier to user.
   [Default: Disabled]

 - **Add licensing Comments:** If enable will include comment with path to the
   original file in every optimized file. Better follows the spirit of GPL but
   not required to conform. Will increase file size slightly.
   [Default: Enabled]

## JSMIN PHP EXTENSION

The AdvAgg Minify JavaScript module can take advantage of jsmin.c if it is
available and selected as minifier. In that case, JavaScript parsing and
minimizing will be done in C instead of PHP dramatically speeding up the
process. The JsMin extension can be found at
[GitHub](https://github.com/sqmk/pecl-jsmin). There are instructions on
installing and compiling it there. Note that if you are using PHP 7.x you'll
need to use the `feature/php7` branch rather than the `master` branch.

# ADVANCED AGGREGATES MODIFIER {#modifier}

## OVERVIEW

*Depending on tweaks applied this module may cause compatibility issues with
other modules.*

Includes additional tweaks that may not work for all sites such as:
  - Force preprocessing for all CSS/JS.
  - Add defer tag to all JS.
  - Defer CSS loading using `rel=preload` and JavaScript Polyfill.
  - Add async tag to all or only local JavaScript.
Some of these may significantly increase performance depending on your
individual site. However, they include possibly dangerous and minimally
tested options so use care and read warnings on each option if there are any.

## CONFIGURATION

Located at `admin/config/development/performance/advagg/mod`.

### JS

 - **Enable preprocess on all JS:** Enables optimization for all JavaScript
   files. *Warning:* this may not be compatible with all mod added files.
   In fact, it is incompatible with CKEditor JavaScript and specifically
   excludes it to prevent problems. [Default: Disabled]

 - **Remove console logging from JS Files:** Removes any calls to console.log()
   *Warning:* this is experimental. Will decrease file size and may improve
   performance. [Default: Disabled]

 - **Optimize JavaScript Ordering:** Re-order the JavaScript to improve
   aggregation. If you're not using aggregation, will have minimal effect.

   - **Move all external scripts to the top of the execution order:** Move
     external scripts to the be loaded first. [Default: Disabled]

   - **Move all browser conditional JavaScript to the bottom of the group:**
     As browser conditional scripts are usually the last needed, this often
     provides better front-end performance. [Default: Disabled]

 - **Adjust JavaScript Location and Execution:** Mostly safe but may cause
   serious issues depending on your specific site configuration. Due to changes
   in Drupal 8 these options are mostly irrelevant but may still have some
   small effect.

   - **Deferred JavaScript Execution:** Add the defer tag to all or only
     local scripts. [Default: Disabled]

   - **Experimental settings:**

     - **Asynchronous JavaScript Execution:** Add the async tag to all
       JavaScript. *Warning:* this may cause issues! [Default: Disabled]

     - **Group Async JavaScript:** Group any Async Javascript together.
       May lead to better aggregates if only some of your scripts are being
       loaded asynchronously otherwise irrelevant. [Default: Disabled]

### CSS

 - **Enable preprocess on all CSS:** Enables optimization for all CSS files.
   *Warning:* this may not be compatible with all mod added files, although
    there are no known cases of it causing problems at this time.
    [Default: Disabled]

 - **Optimize CSS Ordering:** Re-order the CSS to improve
   aggregation. If you're not using aggregation, will have minimal effect.

   - **Move all external CSS to the top of the execution order:** Move
     external CSS to the be loaded first. [Default: Disabled]

   - **Move all browser conditional CSS to the bottom of the group:**
     As browser conditional CSS are usually applied last so this often provides
     better front-end performance. [Default: Disabled]

 - **Adjust CSS Location and Execution:** *Warning:* may cause serious issues
   depending on your specific site configuration. Unlikely to see any
   improvement if using HTTP 2 but may find some if using HTTP 1.x.

   - **Deferred CSS Execution: Use JS to Load CSS:** Attempt optimized CSS
     delivery using JavaScript. [Default: Disabled]

   - **Use JS to load CSS in admin theme:** Apply JS based CSS loading to the
     admin theme as well. [Default: Disabled]

   - **Use JS to load External CSS:** Optimize delivery for external
     stylesheets using JavaScript. [Default: Enabled]

   - **How to include the JS loading code:** Method of including the JS to load
     the CSS. [Default: Inline]

# ADVANCED AGGREGATES OLD IE COMPATIBILITY ENHANCER {#ie-compatibility}

## OVERVIEW

*This module may have conflicts with other modules if it is enabled.*

Includes additional functionality to improve Drupal compatibility with old
versions of Internet Explorer (6-9). No currently known compatibility issues
however due to method required to override core on this it is easily possible
that there are other modules that do conflict.

This module prevents CSS aggregates from being produced with more than 4095
(or a configured value) selectors as **old** Internet Explorer versions do not
handle more than 4095 selectors in an individual file.

## CONFIGURATION

Located at `admin/config/development/performance/advagg/old_ie_compatibility`.

 - **Prevent more than [number] CSS selectors in an aggregate CSS file:**
   [Default: enabled]

 - **Selector count limit:** The maximum number of selectors to allow in an
   aggregate CSS file. [Default 4095]
