/*
 * Base Gulp File
 * - 01 - Requirements
 * - 02 - Paths
 * - 03 - Styles
 * - 04 - Scripts
 * - 05 - Exports
 */


/*------------------------------------*\
  01 - Requirements
  Although Gulp inherently does not require any other libraries in order to
  work, other NPM libraries will be used to generate sourcemaps, be able to
  automatically view in a browser and to minify distributed files.
\*------------------------------------*/

const browserSync = require("browser-sync").create();
const cssnano = require('cssnano');
const gulp = require('gulp');
const ignore = require('gulp-ignore');
const plumber = require('gulp-plumber');
const postcss = require('gulp-postcss');
const sass = require('gulp-sass')(require('sass'));
const sourcemaps = require('gulp-sourcemaps');
const uglify = require('gulp-uglify');
const webpack = require('webpack-stream');




/*------------------------------------*\
  02 - Paths
  Paths are defined here as to where Gulp should look for files to compile,
  as well as where to put files that have been compiled already.
\*------------------------------------*/

const paths = {
  styles: {
    src: `libraries/**/*.scss`,
    dest: `dist/css`,
  },
  scripts: {
    src: `libraries/**/*.js`,
    dest: `dist/js`,
  },
};




/*------------------------------------*\
  03 -  Styles
  Define both compilation of SASS files during development and also when ready for Production and final
  build / minification. Autoprefixer is included in PostCSS Preset Env, thus is not defined here.
\*------------------------------------*/

gulp.task('styles', function () {
  return gulp.src(paths.styles.src)
    .pipe(sourcemaps.init())
    .pipe(sass())
    .on("error", sass.logError)
    .pipe(postcss()) // PostCSS will automatically grab any additional plugins and settings from postcss.config.js
    .pipe(sourcemaps.write())
    .pipe(gulp.dest(paths.styles.dest))
    .pipe(browserSync.stream())
});

gulp.task('stylesBuild', function () {
  return gulp.src(paths.styles.src)
    .pipe(sass())
    .on("error", sass.logError)
    .pipe(postcss([
      cssnano(), // Minifies all CSS
    ]))
    .pipe(gulp.dest(paths.styles.dest))
});




/*------------------------------------*\
  04 - Scripts
  Define both compilation of JavaScript files during development and also when
  ready for Production and final build / minification. Here, Webpack is
  defined and streamed into the Gulp process.
\*------------------------------------*/

gulp.task('scripts', function() {
  return gulp.src(paths.scripts.src)
    .pipe(plumber())
    .pipe(webpack(require('./webpack.config.js')))
    .pipe(gulp.dest(paths.scripts.dest))
});

gulp.task('scriptsBuild', function() {
  return gulp.src(paths.scripts.src)
    .pipe(plumber())
    .pipe(webpack(require('./webpack.config')))
    .pipe(uglify())
    .pipe(ignore.exclude([ "**/*.map" ]))
    .pipe(gulp.dest(paths.scripts.dest))
});




/*------------------------------------*\
  05 - Exports
  Define both the developmental, "Watch" and final production, "Build"
  processes for compiling files. The final production, "Build" process includes
  minified files.

  The BrowserSync Proxy address is determined by creating a custom version of a .env file, from the .env-example file.
  Here you will specify the exact local address of your website.
\*------------------------------------*/

exports.watch = () => {
  console.log('You are currently in development watch mode.');
  browserSync.init({
    proxy: process.env.BS_PROXY || 'local.test',
    browser: process.env.BS_BROWSER || 'google chrome',
  });
  gulp.watch(paths.styles.src, gulp.series('styles'));
  gulp.watch(paths.scripts.src, gulp.series('scripts'));
};

exports.build = (done) => {
  console.log('You are building for production.');
  gulp.parallel('stylesBuild')(done);
  gulp.parallel('scriptsBuild')(done);
};
