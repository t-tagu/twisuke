import gulp from 'gulp';
import webpackConfig from './webpack.config.js';
import webpack from 'webpack-stream';
import browserSync from 'browser-sync';
import notify from 'gulp-notify';
import plumber from 'gulp-plumber';

gulp.task('build', function(){
  return gulp.src('resources/js/app.js')
    .pipe(plumber({
      errorHandler: notify.onError("Error: <%= error.message %>")
    }))
    .pipe(webpack(webpackConfig))
    .pipe(gulp.dest('public/js/'))
});

gulp.task('bs-reload', done=>{
  return browserSync.reload();
});

gulp.task('default', gulp.series(gulp.parallel('build'), function(done){ //gulpコマンドを打った時に実行される
  done();
}));
