var gulp = require('gulp');
var gulpLess = require('gulp-less');

var scriptsDestination = 'web/js';
var stylesDestination = 'web/css';
var imagesDestination = 'web/img';
var fontsDestination = 'web/fonts';

var scriptFiles = [
    'bower_components/bootstrap/dist/js/bootstrap.js',
    'bower_components/jquery/dist/jquery.js',
    'src/AppBundle/Resources/javascripts/**/*.js'
];

var lessFiles = [
    'src/AppBundle/Resources/less/sprites.less',
    'src/AppBundle/Resources/less/main.less'
];

var cssFiles = [
    'bower_components/animate.css/animate.css'
];

var imageFiles = [
    'src/AppBundle/Resources/images/**/*.+(png|jpeg|jpg)'
];

var fontsFiles = [
    'bower_components/bootstrap/fonts/*.*'
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

gulp.task('images', function(){
    gulp.src(imageFiles)
        .pipe(gulp.dest(imagesDestination));
});

gulp.task('fonts', function(){
    gulp.src(fontsFiles)
        .pipe(gulp.dest(fontsDestination));
});

gulp.task('styles', ['less', 'css', 'images', 'fonts']);

gulp.task('default', ['styles', 'scripts']);

gulp.task('watch', function(){
    gulp.watch(scriptFiles, function(event){
        console.log('File ' + event.path + ' was changed');
        gulp.src(event.path)
            .pipe(gulp.dest(scriptsDestination));
    });

    gulp.watch(lessFiles, function(event){
        console.log('File ' + event.path + ' was changed');
        gulp.src(event.path)
            .pipe(gulpLess())
            .pipe(gulp.dest(stylesDestination));
    });

    gulp.watch(imageFiles, function(event){
        console.log('File ' + event.path + ' was changed');
        gulp.src(event.path)
            .pipe(gulp.dest(imagesDestination));
    });
});
