// ### Scripts
// `gulp scripts` - Runs JSHint then compiles, combines, and optimizes Bower JS
// and project JS.

module.exports = function(gulp, data, util, taskName){
  'use strict';

  var $           = data.plugins,
      mergeStream = require('merge-stream'),
      lazypipe    = require('lazypipe');

    // Creates plugin sequence for a dependency using lazy pipe
    function createDepLazyPipe(dep) {
      return lazypipe()
        .pipe($.concat, dep.name)
        .pipe($.uglify,{
          compress: {
            'drop_debugger': data.enabled.stripJSDebug
          }
        })
        .pipe($.notify, {
          onLast: true,
          title: 'JS Compiled',
          message: '<%= file.relative %> complete'
        })
        .pipe(gulp.dest, data.manifest.paths.dist);
    }

  // Task declaration
  gulp.task(taskName, function() {
    var merged = mergeStream();

    // go through each dependency and add pipe to stream.
    data.manifest.forEachDependency('js', function (dep) {
      merged.add(gulp.src(dep.globs)
                     .pipe($.if(!data.enabled.failStyleTask, $.plumber({
                              errorHandler: function(err){
                                $.notify.onError({
                                    title:    "JS Error",
                                    message:  "Error: <%= error.message %>",
                                    sound:    "Basso"
                                })(err);
                                this.emit('end');
                              }
                            })))
                     .pipe(createDepLazyPipe(dep)()));
    });

    return merged;
  });
};
