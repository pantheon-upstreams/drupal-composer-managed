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

const browserSync = require("browser-sync").create();
const dotenv = require("dotenv").config();
const gulp = require("gulp");
const ignore = require("gulp-ignore");
const plumber = require("gulp-plumber");
const postcss = require("gulp-postcss");
const rename = require("gulp-rename");
const sass = require("gulp-sass")(require("sass"));
const sourcemaps = require("gulp-sourcemaps");
const uglify = require("gulp-uglify");
const webpack = require("webpack-stream");
const glob = require("glob");




/*------------------------------------*\
  02 - Paths
  Paths are defined here as to where Gulp should look for files to compile,
  as well as where to put files that have been compiled already.
\*------------------------------------*/

const paths = {
  base: {
    styles: {
      src: 'libraries/**/*.scss',
      dest: 'dist/css',
    },
    scripts: {
      src: './libraries/**/*.js',
      dest: 'dist/js',
    },
  },
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
  03 - Entry Points
  In order for Webpack to compile multiple entry points, we will need to glob
  together an array of paths. This needs to be done for both components and
  any base JavaScript.
\*------------------------------------*/

const componentEntryPoints = glob.sync(paths.component.scripts.src).reduce((entries, entry) => {
  const name = entry.replace('/src', '');
  entries[name] = entry;
  return entries;
}, {});

const baseEntryPoints = glob.sync(paths.base.scripts.src).reduce((entries, entry) => {
  const name = entry.replace('/libraries', '');
  entries[name] = entry;
  return entries;
}, {});




/*------------------------------------*\
  04 -  Styles
  Define both compilation of SASS files during development and also when ready for Production and final
  build / minification. Autoprefixer is included in PostCSS Preset Env, thus is not defined here.
\*------------------------------------*/

gulp.task("baseStylesWatch", function () {
  return gulp.src(paths.base.styles.src)
    .pipe(sourcemaps.init())
    .pipe(sass())
    .on("error", sass.logError)
    .pipe(postcss()) // PostCSS will automatically grab any additional plugins and settings from postcss.config.js
    .pipe(sourcemaps.write())
    .pipe(gulp.dest(paths.base.styles.dest))
    .pipe(browserSync.stream());
});

gulp.task("componentStylesWatch", function () {
  return gulp.src(paths.component.styles.src)
    .pipe(sourcemaps.init())
    .pipe(sass())
    .on("error", sass.logError)
    .pipe(postcss()) // PostCSS will automatically grab any additional plugins and settings from postcss.config.js
    .pipe(sourcemaps.write())
    .pipe(rename(function (file) {
      file.dirname = file.dirname.replace('/src', '');
    }))
    .pipe(gulp.dest(paths.component.styles.dest))
    .pipe(browserSync.stream());
});

gulp.task("baseStylesBuild", function () {
  return gulp.src(paths.base.styles.src)
    .pipe(sass())
    .on("error", sass.logError)
    .pipe(postcss())
    .pipe(gulp.dest(paths.base.styles.dest));
});

gulp.task("componentStylesBuild", function () {
  return gulp.src(paths.component.styles.src)
    .pipe(sass())
    .on("error", sass.logError)
    .pipe(postcss())
    .pipe(rename(function (file) {
      file.dirname = file.dirname.replace('/src', '');
    }))
    .pipe(gulp.dest(paths.component.styles.dest))
});




/*------------------------------------*\
  05 - Scripts
  Define both compilation of JavaScript files during development and also when
  ready for Production and final build / minification. Here, Webpack is
  defined and streamed into the Gulp process.

  We first define each task as a function that accepts a callback. This allows us leverage gulp's
  lastRun feature which will only recompile files that have changed since the last time the task was run.
  This greatly increases performance when running gulp watch with many files.
\*------------------------------------*/

gulp.task('baseScriptsWatch', function() {
  return gulp.src(paths.base.scripts.src)
    .pipe(plumber())
    .pipe(webpack({
      ...require('./webpack.config.js'),
      entry:  baseEntryPoints,
      output: {
        path: `${__dirname}/dist/js`,
        filename: '[name]'
      },
    }))
    .pipe(gulp.dest(paths.base.scripts.dest))
});

gulp.task('componentScriptsWatch', function() {
  return gulp.src(paths.component.scripts.src)
    .pipe(plumber())
    .pipe(webpack({
      ...require('./webpack.config.js'),
      entry: componentEntryPoints,
      output: {
        path: `${__dirname}/components/[name]`,
        filename: '[name]'
      },
    }))
    .pipe(gulp.dest(paths.component.scripts.dest))
});

gulp.task('baseScriptsBuild', function() {
  return gulp.src(paths.base.scripts.src)
    .pipe(plumber())
    .pipe(webpack({
      ...require('./webpack.config.js'),
      entry:  baseEntryPoints,
      output: {
        path: `${__dirname}/dist/js`,
        filename: '[name]'
      },
    }))
    .pipe(ignore.exclude([ "**/*.map" ]))
    .pipe(uglify())
    .pipe(gulp.dest(paths.base.scripts.dest))
});

gulp.task('componentScriptsBuild', function() {
  return gulp.src(paths.component.scripts.src)
    .pipe(plumber())
    .pipe(webpack({
      ...require('./webpack.config.js'),
      entry: componentEntryPoints,
      output: {
        path: `${__dirname}/components/[name]`,
        filename: '[name]'
      },
    }))
    .pipe(ignore.exclude([ "**/*.map" ]))
    .pipe(uglify())
    .pipe(gulp.dest(paths.component.scripts.dest))
});




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
  browserSync.init({
    proxy: process.env.BS_PROXY || "http://prototype.lndo.site",
    browser: process.env.BS_BROWSER || "google chrome",
    open: false,
    logConnections: true,
  });
  gulp.watch(paths.base.styles.src, gulp.series("baseStylesWatch"));
  gulp.watch(paths.base.scripts.src, gulp.series("baseScriptsWatch"));
  gulp.watch(paths.component.styles.src, gulp.series("componentStylesWatch"));
  gulp.watch(paths.component.scripts.src, gulp.series("componentScriptsWatch"));
};

exports.build = (done) => {
  console.log("You are building for production.");
  gulp.parallel(
    "baseStylesBuild",
    "baseScriptsBuild",
    "componentStylesBuild",
    "componentScriptsBuild"
  )(done);
};
