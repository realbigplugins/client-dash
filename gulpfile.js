'use strict';

const gulp       = require('gulp');
const fs         = require('fs');
const sass       = require('gulp-sass');
const sourcemaps = require('gulp-sourcemaps');
const rename     = require('gulp-rename');
const notify     = require('gulp-notify');
const concat     = require('gulp-concat');
const uglify     = require('gulp-uglify');
const browserify = require('browserify');
const babelify   = require('babelify');
const fetchify   = require('fetchify');
const source     = require('vinyl-source-stream');
const buffer     = require('vinyl-buffer');
const gutil      = require('gulp-util');
const reactify   = require('reactify');

gulp.task('sass', function () {
    return gulp.src('./assets/src/scss/*.scss')
        .pipe(sourcemaps.init())
        .pipe(sass({
            outputStyle: 'compressed'
        }).on('error', sass.logError))
        .pipe(sourcemaps.write())
        .pipe(rename({
            dirname: '',
            suffix: '.min'
        }))
        .pipe(gulp.dest('./assets/dist/css'))
        .pipe(notify({message: 'SASS complete'}));
});

gulp.task('customize_sass', function () {
    return gulp.src('./assets/src/scss/customize/*.scss')
        .pipe(sourcemaps.init())
        .pipe(sass({
            outputStyle: 'compressed'
        }).on('error', sass.logError))
        .pipe(sourcemaps.write())
        .pipe(rename({
            dirname: '',
            suffix: '.min'
        }))
        .pipe(gulp.dest('./assets/dist/css'))
        .pipe(notify({message: 'SASS Customize complete'}));
});

gulp.task('customize_inpreview_sass', function () {
    return gulp.src('./assets/src/scss/customize-inpreview/*.scss')
        .pipe(sourcemaps.init())
        .pipe(sass({
            outputStyle: 'compressed'
        }).on('error', sass.logError))
        .pipe(sourcemaps.write())
        .pipe(rename({
            dirname: '',
            suffix: '.min'
        }))
        .pipe(gulp.dest('./assets/dist/css'))
        .pipe(notify({message: 'SASS Customize Inpreview complete'}));
});

gulp.task('scripts', function () {
    return gulp.src('./assets/src/js/*.js')
        .pipe(concat('clientdash.min.js'))
        .pipe(gulp.dest('./assets/dist/js/'))
        .pipe(sourcemaps.init())
        .pipe(uglify())
        .pipe(sourcemaps.write('/'))
        .pipe(gulp.dest('./assets/dist/js/'))
        .pipe(notify({message: 'JS complete'}));
});

gulp.task('customize_inpreview_js', function () {
    return gulp.src('./assets/src/js/customize-inpreview/*.js')
        .pipe(concat('clientdash-inpreview.min.js'))
        .pipe(gulp.dest('./assets/dist/js/'))
        .pipe(sourcemaps.init())
        .pipe(uglify())
        .pipe(sourcemaps.write('/'))
        .pipe(gulp.dest('./assets/dist/js/'))
        .pipe(notify({message: 'JS Customize inpreview complete'}));
});

gulp.task('customize_js', function () {
    return browserify({
        transform: [
            [babelify, {
                presets: ["es2015", "react"]
            }]
        ],
        entries: ['./assets/src/js/customize/customize.js'],
        debug: true
    })
        .bundle()
        .pipe(source('clientdash-customize.min.js'))
        .pipe(buffer())
        .pipe(sourcemaps.init({loadMaps: true}))
        .pipe(uglify())
        .on('error', gutil.log)
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest('./assets/dist/js/'))
        .pipe(notify({message: 'JS Customize complete'}));
});

gulp.task('apply-prod-environment', function () {
    process.stdout.write("Setting NODE_ENV to 'production'" + "\n");
    process.env.NODE_ENV = 'production';
    if ( process.env.NODE_ENV != 'production' ) {
        throw new Error("Failed to set NODE_ENV to production!!!!");
    } else {
        process.stdout.write("Successfully set NODE_ENV to production" + "\n");
    }
});

gulp.task('default', ['sass', 'scripts', 'customize_sass', 'customize_inpreview_sass', 'customize_inpreview_js', 'customize_js'], function () {
    gulp.watch(['./assets/src/scss/*.scss'], ['sass']);
    gulp.watch(['./assets/src/scss/customize/*.scss'], ['customize_sass']);
    gulp.watch(['./assets/src/scss/customize-inpreview/*.scss'], ['customize_inpreview_sass']);
    gulp.watch(['./assets/src/js/*.js'], ['scripts']);
    gulp.watch(['./assets/src/js/customize/*.js'], ['customize_js']);
    gulp.watch(['./assets/src/js/customize/customize-inpreview.js'], ['customize_inpreview_js']);
});

gulp.task('build', ['version', 'apply-prod-environment', 'sass', 'scripts', 'customize_sass', 'customize_inpreview_sass', 'customize_inpreview_js', 'customize_js', 'generate_pot']);
