// ### Triggers build pipeline.
// Necessary to ensure clean task is complete
// before continuing with build

module.exports = function(gulp, data, util, taskName){
  'use strict';

  gulp.task(taskName, ['clean'], function () {
    gulp.start('buildComponents');
  });
};
