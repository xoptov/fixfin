var gulp = require('gulp');
var less = require('gulp-less');
var spritesmith = require('gulp.spritesmith');

var scriptsOut = './Resources/public/js';
var stylesOut = './Resources/public/css';
var imagesOut = './Resources/public/img';
var fontsOut = './Resources/public/fonts';

var scriptFiles = [
    './Resources/bower_components/bootstrap/dist/js/bootstrap.js',
    './Resources/bower_components/jquery/dist/jquery.js',
    './Resources/bower_components/modulejs/dist/modulejs.js',
    './Resources/bower_components/clipboard/dist/clipboard.js',
    './Resources/bower_components/selectize/dist/js/standalone/selectize.js',
    './Resources/assets/javascripts/**/*.js'
];

var lessFiles = [
    './Resources/assets/less/cabinet.less',
    './Resources/assets/less/landing.less'
];

var cssFiles = [
    './Resources/bower_components/animate.css/animate.css'
];

var imageFiles = [
    './Resources/assets/images/**/*.+(png|jp*g|svg)'
];

var spriteFiles = [
    './Resources/assets/sprites/**/*.png'
];

var fontsFiles = [
    './Resources/bower_components/bootstrap/fonts/*.+(eot|svg|ttf|woff*)',
    './Resources/assets/fonts/*.+(eot|svg|ttf|woff*)'
];

function compileScripts(){
    gulp.src(scriptFiles).pipe(gulp.dest(scriptsOut));
}

function compileCss(){
    gulp.src(cssFiles).pipe(gulp.dest(stylesOut));
}

function compileLess(){
    gulp.src(lessFiles).pipe(less()).pipe(gulp.dest(stylesOut));
}

function compileImages(){
    gulp.src(imageFiles).pipe(gulp.dest(imagesOut));
}

function compileSprites(){
    var spriteData = gulp.src(spriteFiles).pipe(spritesmith({
        imgName: 'sprites.png',
        imgPath: '../img/sprites.png',
        cssName: 'sprites.less',
        padding: 1
    }));

    spriteData.img.pipe(gulp.dest(imagesOut));
    spriteData.css.pipe(gulp.dest('./Resources/assets/less/'));
}

function compileFonts(){
    gulp.src(fontsFiles).pipe(gulp.dest(fontsOut));
}

function handleEvent(handler){
    return function(event){
        console.log('[' + Date.now() + ']' + ' File ' + event.path + ' was ' + event.type);
        handler();
    }
}

gulp.task('scripts', compileScripts);
gulp.task('css', compileCss);
gulp.task('less', compileLess);
gulp.task('images', compileImages);
gulp.task('sprites', compileSprites);
gulp.task('fonts', compileFonts);

gulp.task('styles', ['less', 'css', 'images', 'sprites', 'fonts']);
gulp.task('default', ['styles', 'scripts']);

gulp.task('watch', function(){
    gulp.watch(scriptFiles, handleEvent(compileScripts));
    gulp.watch(lessFiles, handleEvent(compileLess));
    gulp.watch(imageFiles, handleEvent(compileImages));
    gulp.watch(spriteFiles, handleEvent(compileSprites))
});
