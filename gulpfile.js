'use strict';

const packageInfo = require('./package.json');

const gulp        = require('gulp');
const fs          = require('fs');
const sass        = require('gulp-sass');
const sourcemaps  = require('gulp-sourcemaps');
const rename      = require('gulp-rename');
const notify      = require('gulp-notify');
const concat      = require('gulp-concat');
const uglify      = require('gulp-uglify');
const browserify  = require('browserify');
const babelify    = require('babelify');
const fetchify    = require('fetchify');
const source      = require('vinyl-source-stream');
const buffer      = require('vinyl-buffer');
const gutil       = require('gulp-util');
const reactify    = require('reactify');
const wpPot       = require('gulp-wp-pot');
const sort        = require('gulp-sort');
const gulpIf      = require('gulp-if');
var $             = require('gulp-load-plugins')();

gulp.task('admin_sass', function () {
    return gulp.src(['./assets/src/scss/admin/**/*.scss'])
        .pipe(sourcemaps.init())
        .pipe(sass({
            outputStyle: 'compressed'
        }).on('error', sass.logError))
        .pipe(sourcemaps.write())
        .pipe(rename('clientdash-admin.min.css'))
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

gulp.task('admin_js', function () {
    return gulp.src([
        './assets/vendor/select2/dist/js/select2.full.js',
        './assets/src/js/admin/**/*.js'
    ])
        .pipe(concat('clientdash-admin.min.js'))
        .pipe(gulp.dest('./assets/dist/js/'))
        .pipe(sourcemaps.init())
        .pipe(uglify())
        .pipe(sourcemaps.write('/'))
        .pipe(gulp.dest('./assets/dist/js/'))
        .pipe(notify({message: 'JS Admin complete'}));
});

gulp.task('customize_inpreview_js', function () {
    return gulp.src('./assets/src/js/customize-inpreview/*.js')
        .pipe(concat('clientdash-customize-inpreview.min.js'))
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
                presets: ["es2015", "stage-2", "react"]
            }]
        ],
        entries: ['./assets/src/js/customize/customize.js'],
        debug: true
    })
        .bundle()
        .pipe(source('clientdash-customize.min.js'))
        .pipe(buffer())
        .pipe(sourcemaps.init({loadMaps: true}))
        .pipe(gulpIf(process.env.NODE_ENV === 'production', uglify()
            .on('error', e => {
                console.log(e);
            })
        ))
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

gulp.task('version', function () {
    return gulp.src([
        'admin/**/*',
        'assets/src/**/*',
        'core/**/*',
        '!core/library/**/*',
        'languages/**/*',
        'templates/**/*',
        'client-dash.php',
        'client-dash-bootstrapper.php',
        'readme.txt',
		'README.md'
    ], {base: './'})
		// Doc block versions
        .pipe($.replace(/\{\{VERSION}}/g, packageInfo.version))
        // Plugin header
        .pipe($.replace(/(\* Version: )\d+\.\d+\.\d+/, "$1" + packageInfo.version))
        // Version constant
        .pipe($.replace(/(define\( 'CLIENTDASH_VERSION', ')\d+\.\d+\.\d+/, "$1" + packageInfo.version))
        // readme.txt
        .pipe($.replace(/(Stable tag: )\d+\.\d+\.\d+/, "$1" + packageInfo.version))
		// README.md
        .pipe($.replace(/(\#\sv)\d+\.\d+\.\d+/, "$1" + packageInfo.version))
        .pipe(gulp.dest('./'));
});

gulp.task('generate_pot', function () {
    return gulp.src('./**/*.php')
        .pipe(sort())
        .pipe(wpPot({
            domain: 'client-dash',
            package: 'ClientDash',
        }))
        .pipe(gulp.dest('./languages/client-dash.pot'));
});


gulp.task('default', ['admin_sass', 'admin_js', 'customize_sass', 'customize_inpreview_sass', 'customize_inpreview_js', 'customize_js'], function () {
    gulp.watch(['./assets/src/scss/admin/**/*.scss'], ['admin_sass']);
    gulp.watch(['./assets/src/scss/customize/*.scss'], ['customize_sass']);
    gulp.watch(['./assets/src/scss/customize-inpreview/*.scss'], ['customize_inpreview_sass']);
    gulp.watch(['./assets/src/js/admin/**/*.js'], ['admin_js']);
    gulp.watch(['./assets/src/js/customize/*.js'], ['customize_js']);
    gulp.watch(['./assets/src/js/customize-inpreview/customize-inpreview.js'], ['customize_inpreview_js']);
});

gulp.task('build', ['apply-prod-environment', 'admin_sass', 'admin_js', 'customize_sass', 'customize_inpreview_sass', 'customize_inpreview_js', 'customize_js', 'generate_pot']);
