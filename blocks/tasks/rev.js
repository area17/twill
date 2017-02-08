// ### Write to rev manifest
// If there are any revved files then write them to the rev manifest.
// See https://github.com/sindresorhus/gulp-rev

module.exports = function(gulp, data, util, taskName) {
  'use strict';

  var $            = data.plugins,
      distPath     = data.manifest.paths.dist;

  // Task declaration
  gulp.task(taskName, ['scripts'], function() {
    if (data.enabled.rev) {
      return gulp.src([distPath + '**/*.js'], {base: distPath})
        .pipe($.rev())
        .pipe($.revDeleteOriginal())
        .pipe(gulp.dest(distPath))
        .pipe($.rev.manifest({
          merge: true
        }))
        .pipe(gulp.dest(distPath));
    }
  });
};
