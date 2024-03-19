/*
 * Base Gulp File
 * - 01 - Requirements
 * - 02 - Paths
 * - 03 - Entry Points
 * - 04 - Styles
 * - 05 - Scripts
 * - 06 - Exports
 */


/*------------------------------------*\
  01 - Requirements
  Although Gulp inherently does not require any other libraries in order to
  work, other NPM libraries will be used to generate sourcemaps, be able to
  automatically view in a browser and to minify distributed files.
\*------------------------------------*/

const browserSync = require('browser-sync').create();
const dependents = require('gulp-dependents');
const dotenv = require('dotenv').config();
const gulp = require('gulp');
const ignore = require('gulp-ignore');
const named = require('vinyl-named');
const path = require('path');
const postcss = require('gulp-postcss');
const rename = require('gulp-rename');
const sass = require('gulp-sass')(require('sass'));
const sourcemaps = require('gulp-sourcemaps');
const uglify = require('gulp-uglify');
const webpack = require('webpack-stream');
const webpackCompiler = require('webpack');
const webpackConfig = require('./webpack.config.js');




/*------------------------------------*\
  02 - Paths
  Paths are defined here as to where Gulp should look for files to compile,
  as well as where to put files that have been compiled already.
\*------------------------------------*/

const paths = {
  component: {
    styles: {
      src: 'components/**/src/*.scss',
      dest: 'components',
    },
    scripts: {
      src: './components/**/src/*.js',
      dest: './',
    },
  },
};




/*------------------------------------*\
  03 - Filename Conversion
  In order for Webpack to compile multiple entry points, we will need to dynamically update the
  file names of the scripts passed in. Rather than passing an array of entry points (a.k.a. source files)
  to a single webpack instance, we pipe each files to their own instance. This allows for incremental builds
  that only recompile files that have changed. This is a huge performance boost when working with many files.
\*------------------------------------*/

/**
 * Rename a component script file with an absolute source path to base destination path
 * relative to the theme's directory.
 *
 * @param {object} file
 *  Vinyl file object
 * @returns {string}
 *  Relative path to the destination file with the .js extension removed
 */
const renameComponentScripts = (file) => {
  return path.relative(process.cwd(), file.path.replace('/src', '')
    // Remove the .js extension
    .slice(0, -3));
};




/*------------------------------------*\
  04 -  Styles
  Define both compilation of SASS files during development and also when ready for Production and final
  build / minification. Autoprefixer is included in PostCSS Preset Env, thus is not defined here.
\*------------------------------------*/

const componentStylesWatchTask = function(done) {
  gulp.src(paths.component.styles.src, {
    sourcemaps: true,
    since: gulp.lastRun(componentStylesWatchTask),
  })
    .pipe(sourcemaps.init())
    .pipe(dependents())
    .pipe(sass())
    .on('error', sass.logError)
    .pipe(postcss()) // PostCSS will automatically grab any additional plugins and settings from postcss.config.js
    .pipe(rename(function(file) {
      file.dirname = file.dirname.replace('/src', '');
    }))
    .pipe(gulp.dest(paths.component.styles.dest, { sourcemaps: true }))
    .pipe(browserSync.stream());
  done();
};

const componentStylesBuildTask = function(done) {
  gulp.src(paths.component.styles.src)
    .pipe(sass())
    .on('error', sass.logError)
    .pipe(postcss()) // PostCSS will automatically grab any additional plugins and settings from postcss.config.js
    .pipe(rename(function(file) {
      file.dirname = file.dirname.replace('/src', '')
    }))
    .pipe(gulp.dest(paths.component.styles.dest));
  done();
}

exports.componentStylesWatchTask = componentStylesWatchTask;
exports.componentStylesBuildTask = componentStylesBuildTask;




/*------------------------------------*\
  05 - Scripts
  Define both compilation of JavaScript files during development and also when
  ready for Production and final build / minification. Here, Webpack is
  defined and streamed into the Gulp process.

  We first define each task as a function that accepts a callback. This allows us leverage gulp's
  lastRun feature which will only recompile files that have changed since the last time the task was run.
  This greatly increases performance when running gulp watch with many files.
\*------------------------------------*/

const componentScriptsWatchTask = function(done) {
  gulp.src(paths.component.scripts.src, {
    since: gulp.lastRun(componentScriptsWatchTask),
  })
    .pipe(named(renameComponentScripts))
    .pipe(webpack(webpackConfig, webpackCompiler))
    .on('error', function(err) {
      this.emit('end'); // Don't stop the rest of the task
    })
    .pipe(gulp.dest(paths.component.scripts.dest));
  done();
};

const componentScriptsBuildTask = function(done) {
  return gulp.src(paths.component.scripts.src)
    .pipe(named(renameComponentScripts))
    .pipe(webpack(webpackConfig, webpackCompiler))
    .pipe(ignore.exclude(['**/*.map']))
    .pipe(uglify())
    .pipe(gulp.dest(paths.component.scripts.dest));
  done();
};

exports.componentScriptsWatchTask = componentScriptsWatchTask;
exports.componentScriptsBuildTask = componentScriptsBuildTask;



/*------------------------------------*\
  06 - Exports
  Define both the developmental, "Watch" and final production, "Build"
  processes for compiling files. The final production, "Build" process includes
  minified files.

  The BrowserSync Proxy address is determined by creating a custom version of a .env file, from the .env-example file.
  Here you will specify the exact local address of your website.
\*------------------------------------*/

exports.watch = () => {
  console.log("You are currently in development watch mode.");
  const watchOptions = {
    ignoreInitial: false, // Ensure all files are built when the task starts.
  }
  browserSync.init({
    proxy: process.env.BS_PROXY || "http://prototype.lndo.site",
    browser: process.env.BS_BROWSER || "google chrome",
    open: false,
    logConnections: true,
  });
  gulp.watch(paths.component.styles.src, watchOptions, gulp.series("componentStylesWatchTask"));
  gulp.watch(paths.component.scripts.src, watchOptions, gulp.series("componentScriptsWatchTask"));
};

exports.build = (done) => {
  console.log("You are building for production.");
  gulp.series(
    "componentStylesBuildTask",
    "componentScriptsBuildTask"
  )(done);
};
