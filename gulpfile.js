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

gulp.task('apply-prod-environment', function ( done ) {
    process.stdout.write("Setting NODE_ENV to 'production'" + "\n");
    process.env.NODE_ENV = 'production';
    if ( process.env.NODE_ENV != 'production' ) {
        throw new Error("Failed to set NODE_ENV to production!!!!");
		done();
    } else {
        process.stdout.write("Successfully set NODE_ENV to production" + "\n");
		done();
    }
});

gulp.task('remove-prod-environment', function ( done ) {
    process.stdout.write("Setting NODE_ENV to 'dev'" + "\n");
    process.env.NODE_ENV = 'dev';
    if ( process.env.NODE_ENV != 'dev' ) {
        throw new Error("Failed to set NODE_ENV to dev!!!!");
		done();
    } else {
        process.stdout.write("Successfully set NODE_ENV to dev" + "\n");
		done();
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


gulp.task('default', gulp.parallel( 'admin_sass', 'admin_js', 'customize_sass', 'customize_inpreview_sass', 'customize_inpreview_js', 'customize_js', function () {
    gulp.watch(['./assets/src/scss/admin/**/*.scss'], gulp.parallel( 'admin_sass' ) );
    gulp.watch(['./assets/src/scss/customize/*.scss'], gulp.parallel( 'customize_sass' ) );
    gulp.watch(['./assets/src/scss/customize-inpreview/*.scss'], gulp.parallel( 'customize_inpreview_sass' ) );
    gulp.watch(['./assets/src/js/admin/**/*.js'], gulp.parallel( 'admin_js' ) );
    gulp.watch(['./assets/src/js/customize/*.js'], gulp.parallel( 'customize_js' ) );
    gulp.watch(['./assets/src/js/customize-inpreview/customize-inpreview.js'], gulp.parallel( 'customize_inpreview_js' ) );
} ) );

gulp.task('build', gulp.series( 'admin_sass', 'admin_js', 'customize_sass', 'customize_inpreview_sass', 'customize_inpreview_js', 'customize_js', 'generate_pot' ) );

gulp.task( 'svn_copy', function() {
	
	return gulp.src( [
		'admin/**/*',
        'assets/**/*',
		'!assets/vendor/**',
        'core/**/*',
        'core/library/**/*',
        'languages/**/*',
        'templates/**/*',
        'client-dash.php',
        'client-dash-bootstrapper.php',
        'readme.txt',
		'!core/library/rbm-field-helpers/assets/{src,src/**}',
		'!core/library/rbm-field-helpers/{bin,bin/**}',
		'!./**/.babelrc',
		'!./**/.gitignore',
		'!./**/.git',
		'!./**/config.yml',
		'!./**/config-default.yml',
		'!./**/gulpfile.js',
		'!./**/gulpfile.babel.js',
		'!./**/grunfile.js',
		'!./**/package.json',
		'!./**/package-lock.json',
		'!./**/README.md',
		'!./**/webpack.config.js',
	], { base: '*' } )
	.pipe(rename(function(file) {
		file.dirname = file.dirname.replace( '..', '' );
		return file;
	 }))
	.pipe( gulp.dest( 'client-dash-svn/trunk/' ) );
	
} );

gulp.task( 'svn_build', gulp.series( 'version', 'apply-prod-environment', 'build', 'svn_copy', 'remove-prod-environment', 'build' ) );