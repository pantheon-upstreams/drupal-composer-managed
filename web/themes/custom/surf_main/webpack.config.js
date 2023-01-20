/**
 * Webpack JavaScript Compilation
 * Current compilation standards dictate that all JavaScript be compiled using
 * Webpack compilation services - https://webpack.js.org/
 *
 * - 01 - Imports
 * - 02 - Entry Points
 * - 03 - Exports
 */


/*------------------------------------*\
  01 - Imports
  Define the NPM packages to be used during the Webpack compilation, including
  Webpack itself. Even though the Webpack library isn't directly used, it is
  still required to be defined.
\*------------------------------------*/

const glob = require('glob'),
      path = require('path'),
      webpack = require('webpack');




/*------------------------------------*\
  02 - Entry Points
  Since Webpack does not inherently dynamically build a list of resources to
  be compiled, we will use a Glob (https://github.com/isaacs/node-glob) to
  automatically watch JavaScript files and automatically compile them into
  a distribution directory.
\*------------------------------------*/

const entryPoints = glob.sync('./libraries/**/*.js').reduce((entries, entry) => {
  const entryName = path.parse(entry).name
  entries[entryName] = entry
  return entries
}, {});




/*------------------------------------*\
  03 - Exports
  Prepare all resources using Webpack to be exported as distributed and
  compiled files. Here, Babel (https://babeljs.io/) is being used to convert
  any ES6 syntax to earlier versions, if necessary.
\*------------------------------------*/

module.exports = {
  mode: 'none',
  entry: entryPoints,
  output: {
    path: `${__dirname}/dist/js`,
    filename: '[name]/[name].js'
  },
  devtool: "source-map",
  externals: {
    "jquery": "jQuery"
  },
  resolve: {
    extensions: ['.js', '.jsx', '.vue'],
  },
  module: {
    rules: [
      {
        test: /\.js$/,
        exclude: /node_modules/,
        use: {
          loader: 'babel-loader', // All Babel plugins and options will be loaded via the babel.config.js file
        }
      }
    ]
  }
}
