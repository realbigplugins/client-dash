'use strict';
module.exports = function (grunt) {

    // load all grunt tasks
    require('matchdep').filterDev('grunt-*').forEach(grunt.loadNpmTasks);

    grunt.initConfig({

        // watch for changes and trigger compass, jshint, uglify and livereload
        watch: {
            options: {
                livereload: true
            },
            sass: {
                files: ['./assets/scss/**/*.scss'],
                tasks: ['sass', 'autoprefixer']
            },
            js: {
                files: ['./assets/js/source/*.js'],
                tasks: ['uglify:dist']
            },
            jsdeps: {
                files: ['./assets/js/source/deps/*.js'],
                tasks: ['uglify:deps']
            },
            livereload: {
                files: ['./**/*.html', './**/*.php', './assets/images/**/*.{png,jpg,jpeg,gif,webp,svg}', '!./**/*ajax.php']
            }
        },

        // SASS transpilation
        sass: {
            dist: {
                options: {
                    outputStyle: 'compressed',
                    imagePath: './assets/images'
                },
                files: {
                    './assets/css/clientdash.min.css': './assets/scss/clientdash.scss'
                }
            }
        },

        autoprefixer: {
            dist: {
                expand: true,
                flatten: true,
                src: './assets/css/**/*.css',
                dest: './assets/css/',
                options: {
                    browsers: ['last 2 version', 'ie 8', 'ie 9']
                }
            }
        },

        // uglify to concat, minify, and make source maps
        uglify: {
            dist: {
                files: {
                    './assets/js/clientdash.min.js': [
                        './assets/js/source/*.js'
                    ]
                }
            }
        }
    });

    // register task
    grunt.registerTask('Watch', ['watch']);

};