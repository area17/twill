// ### Clean
// `gulp clean` - Deletes the build folder entirely.

var del = require('del');
module.exports = function(gulp, data, util, taskName){
  'use strict';

  gulp.task(taskName, del.bind(null, [data.manifest.paths.dist]));
};
