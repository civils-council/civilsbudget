var gulp = require('gulp');
var less = require('gulp-less');
// var babel = require('gulp-babel');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var rename = require('gulp-rename');
var cleanCSS = require('gulp-clean-css');
var del = require('del');

function styles() {
    return gulp.src(['web-src/less/*.less'])
        .pipe(less())
        .pipe(cleanCSS())
        .pipe(gulp.dest('web/css/'));
}
function libJs() {
    return gulp.src([
        'node_modules/jquery/dist/jquery.js',
        'node_modules/bootstrap/dist/js/bootstrap.js',
        'node_modules/select2/dist/js/select2.full.js'
    ], { sourcemaps: true })
        // .pipe(babel())
        .pipe(uglify())
        .pipe(concat('app.js'))
        .pipe(gulp.dest('web/js/'));
}

function pagesJs() {
    return gulp.src([
        'web-src/js/*.js'
    ], { sourcemaps: true })
        // .pipe(babel())
        .pipe(uglify())
        .pipe(gulp.dest('web/js/'));
}

function images () {
    return gulp.src([
        'web-src/images/*'
    ])
        .pipe(gulp.dest('web/images/'))
}

function developers () {
    return gulp.src([
        'web-src/images/developers/*'
    ])
        .pipe(gulp.dest('web/images/developers/'))
}

function fonts () {
    return gulp.src([
        'node_modules/bootstrap/fonts/*',
        'node_modules/bootwatch/fonts/*',
        'node_modules/font-awesome/fonts/*',
        'web-src/fonts/*'
    ])
        .pipe(gulp.dest('web/fonts/'))
}

function clean() {
    return del(['web/css/*', 'web/js/*', 'web/images/*', 'web/images/developers/', 'web/fonts/*']);
}

function watch() {
    gulp.watch('web-src/less/*.less', styles);
    gulp.watch('web-src/js/*.js', pagesJs);
}

var build = gulp.series(clean, gulp.parallel(images, developers, fonts, libJs), gulp.parallel(styles, pagesJs));

gulp.task('build', build);
gulp.task('default', build);
gulp.task('watch', watch);