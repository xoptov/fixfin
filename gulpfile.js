var gulp = require('gulp');
var gulpLess = require('gulp-less');

var scriptsDestination = 'web/js';
var stylesDestination = 'web/css';

var scriptFiles = [
    'bower_components/bootstrap/dist/js/bootstrap.js',
    'bower_components/jquery/dist/jquery.js',
    'src/AppBundle/Resources/javascripts/**/*.js'
];

var lessFiles = [
    'bower_components/bootstrap/less/bootstrap.less',
    'bower_components/font-awesome/less/font-awesome.less',
    'src/AppBundle/Resources/inspinia/less/style.less',
    'src/AppBundle/Resources/less/**/*.less'
];

var cssFiles = [
    'bower_components/animate.css/animate.css'
];

gulp.task('scripts', function() {
    gulp.src(scriptFiles)
        .pipe(gulp.dest(scriptsDestination));
});

gulp.task('css', function() {
    gulp.src(cssFiles)
        .pipe(gulp.dest(stylesDestination));
});

gulp.task('less', function() {
    gulp.src(lessFiles)
        .pipe(gulpLess())
        .pipe(gulp.dest(stylesDestination));
});

gulp.task('styles', ['less', 'css']);

gulp.task('default', ['styles', 'scripts']);