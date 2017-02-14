// ## Globals
var gulp           = require('gulp'),
    argv           = require('minimist')(process.argv.slice(2)),
    plugins        = require('gulp-load-plugins')(),
    data           = { plugins: plugins };

/* To test locally for bower_components folder. Reguired by manifest.js,
   but project doesn't use bower */
var fs = require('fs');

try {
  fs.accessSync('./bower_components', fs.F_OK);
} catch (e) {
  fs.mkdir('./bower_components');
}

// See https://github.com/austinpray/asset-builder
data.manifest = require('asset-builder')('./blocks/manifest.json');

// CLI options
data.enabled = {
  // Enable static asset revisioning when `--production`
  rev: !!argv.production,
  // Fail due to JSHint warnings only when `--production`
  failJSHint: !!argv.production,
  // Strip debug statments from javascript when `--production`
  stripJSDebug: !!argv.production
};

// Load multiple gulp tasks using globbing patterns.
// @see https://github.com/adriancmiranda/load-gulp-config
var config = require('load-gulp-config');

// Specifics of npm's package.json handling.
// @see https://docs.npmjs.com/files/package.json
var pack = config.util.readJSON('package.json');

config(gulp, {
  // path to task's files, defaults to gulp dir.
  configPath: config.util.path.join('blocks/tasks', '*.{js,json,yml,yaml}'),

  // data passed into config task.
  data:Object.assign(data, pack)
});
