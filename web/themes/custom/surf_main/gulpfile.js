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
const gulp = require("gulp");
const ignore = require("gulp-ignore");
const named = require("vinyl-named");
const path = require("path");
const plumber = require("gulp-plumber");
const postcss = require("gulp-postcss");
const rename = require("gulp-rename");
const sass = require("gulp-sass")(require("sass"));
const sourcemaps = require("gulp-sourcemaps");
const uglify = require("gulp-uglify");
const webpack = require("webpack-stream");
const webpackCompiler = require("webpack");
const webpackConfig = require("./webpack.config");

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
  /*
    These paths are used for the CL Component styles and scripts.
    There is no destination. Compiled files will be saved
    within their own component folder under /css and /js.
  */
  component: {
    styles: {
      src: `./templates/components/**/src/*.scss`,
    },
    scripts: {
      src: `./templates/components/**/src/*.js`,
    },
  },
};

/*------------------------------------*\
  03 -  Styles
  Define both compilation of SASS files during development and also when ready for Production and final
  build / minification. Autoprefixer is included in PostCSS Preset Env, thus is not defined here.

  @TODO: This needs to be updated so
  only files that have changed and those
  that depend on them get recompiled
\*------------------------------------*/

gulp.task("styles", function () {
  return gulp
    .src(paths.styles.src)
    .pipe(sourcemaps.init())
    .pipe(sass({ includePaths: ["./libraries/partials"] }))
    .on("error", sass.logError)
    .pipe(postcss()) // PostCSS will automatically grab any additional plugins and settings from postcss.config.js
    .pipe(sourcemaps.write())
    .pipe(gulp.dest(paths.styles.dest))
    .pipe(browserSync.stream());
});

gulp.task("stylesBuild", function () {
  return gulp
    .src(paths.styles.src)
    .pipe(sass({ includePaths: ["./libraries/partials"] }))
    .on("error", sass.logError)
    .pipe(postcss([]))
    .pipe(gulp.dest(paths.styles.dest));
});

gulp.task("componentStyles", function () {
  return gulp
    .src(paths.component.styles.src, { base: "./" })
    .pipe(sourcemaps.init())
    .pipe(sass({ includePaths: ["./libraries/partials"] }))
    .on("error", sass.logError)
    .pipe(postcss()) // PostCSS will automatically grab any additional plugins and settings from postcss.config.js
    .pipe(sourcemaps.write())
    .pipe(
      rename((path) => {
        path.dirname = path.dirname.replace("src", "css");
      })
    )
    .pipe(gulp.dest("./"))
    .pipe(browserSync.stream());
});

gulp.task("componentStylesBuild", function () {
  return gulp
    .src(paths.component.styles.src, { base: "./" })
    .pipe(sass({ includePaths: ["./libraries/partials"] }))
    .on("error", sass.logError)
    .pipe(postcss())
    .pipe(
      rename((path) => {
        path.dirname = path.dirname.replace("src", "css");
      })
    )
    .pipe(gulp.dest("./"));
});

/*------------------------------------*\
  04 - Scripts
  Define both compilation of JavaScript files during development and also when
  ready for Production and final build / minification. Here, Webpack is
  defined and streamed into the Gulp process.

  We first define each task as a function that accepts a callback. This allows us leverage gulp's
  lastRun feature which will only recompile files that have changed since the last time the task was run.
  This greatly increases performance when running gulp watch with many files.
\*------------------------------------*/

const scriptsTask = function (cb) {
  gulp
    .src(paths.scripts.src, {
      base: "./",
      since: gulp.lastRun(scriptsTask),
    })
    // This is necessary to ensure Webpack treats each file as a separate entry point,
    // rather than bundling them all together in main.js.
    .pipe(
      named((file) => {
        return path.relative("./", file.path).slice(0, -3);
      })
    )
    .pipe(plumber())
    .pipe(webpack(webpackConfig), webpackCompiler)
    .pipe(
      rename((path) => {
        // @TODO: Update this and the library definitions so the
        // folder is not named after the file.
        path.dirname = path.basename
          // We have to strip the .js extension from the basename when .map files are passed through.
          .replace(".js", "");
      })
    )
    .pipe(gulp.dest(paths.scripts.dest));

  cb();
};
gulp.task("scripts", scriptsTask);

const scriptsBuildTask = function (cb) {
  gulp
    .src(paths.scripts.src, {
      base: "./",
      since: gulp.lastRun(scriptsBuildTask),
    })
    // This is necessary to ensure Webpack treats each file as a separate entry point,
    // rather than bundling them all together in main.js.
    .pipe(
      named((file) => {
        return path.relative("./", file.path).slice(0, -3);
      })
    )
    .pipe(plumber())
    .pipe(webpack(webpackConfig), webpackCompiler)
    .pipe(ignore.exclude(["**/*.map"]))
    .pipe(uglify())
    .pipe(
      rename((path) => {
        // @TODO: Update this and the library definitions so the
        // folder is not named after the file.
        path.dirname = path.basename;
      })
    )
    .pipe(gulp.dest(paths.scripts.dest));

  cb();
};
gulp.task("scriptsBuild", scriptsBuildTask);

const componentScriptsTask = function (cb) {
  gulp
    .src(paths.component.scripts.src, {
      base: "./",
      since: gulp.lastRun(componentScriptsTask),
    })
    .pipe(
      named((file) => {
        return path.relative("./", file.path).slice(0, -3);
      })
    )
    .pipe(plumber())
    .pipe(webpack(webpackConfig), webpackCompiler)
    .pipe(
      rename((path) => {
        path.dirname = path.dirname.replace("src", "js");
      })
    )
    .pipe(gulp.dest("./"));

  cb();
};
gulp.task("componentScripts", componentScriptsTask);

const componenetSriptsBuildTask = function (cb) {
  gulp
    .src(paths.component.scripts.src, {
      base: "./",
      since: gulp.lastRun(componenetSriptsBuildTask),
    })
    .pipe(
      named((file) => {
        return path.relative("./", file.path).slice(0, -3);
      })
    )
    .pipe(plumber())
    .pipe(webpack(webpackConfig), webpackCompiler)
    .pipe(ignore.exclude(["**/*.map"]))
    .pipe(uglify())
    .pipe(
      rename((path) => {
        path.dirname = path.dirname.replace("src", "js");
      })
    )
    .pipe(gulp.dest("./"));

  cb();
};
gulp.task("componentScriptsBuild", componenetSriptsBuildTask);

/*------------------------------------*\
  05 - Exports
  Define both the developmental, "Watch" and final production, "Build"
  processes for compiling files. The final production, "Build" process includes
  minified files.

  The BrowserSync Proxy address is determined by creating a custom version of a .env file, from the .env-example file.
  Here you will specify the exact local address of your website.
\*------------------------------------*/

exports.watch = () => {
  console.log("You are currently in development watch mode.");
  browserSync.init({
    proxy: process.env.BS_PROXY || "http://surf.lndo.site",
    browser: process.env.BS_BROWSER || "google chrome",
  });
  gulp.watch(paths.styles.src, gulp.series("styles"));
  gulp.watch(paths.scripts.src, gulp.series("scripts"));
  gulp.watch(paths.component.styles.src, gulp.series("componentStyles"));
  gulp.watch(paths.component.scripts.src, gulp.series("componentScripts"));
};

exports.build = (done) => {
  console.log("You are building for production.");
  gulp.parallel(
    "stylesBuild",
    "scriptsBuild",
    "componentStylesBuild",
    "componentScriptsBuild"
  )(done);
};
