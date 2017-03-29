const { mix } = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.copy('resources/assets/gentelella/vendors/font-awesome/fonts/', 'public/fonts')
   .js([
       'resources/assets/js/app.js',
       'resources/assets/gentelella/vendors/bootstrap-daterangepicker/daterangepicker.js',
       'resources/assets/gentelella/vendors/nprogress/nprogress.js',
       'resources/assets/gentelella/src/js/helpers/smartresize.js',
       'resources/assets/gentelella/src/js/custom.js',
       'resources/assets/js/report.js'
   ], 'public/js/app.js')
   .sass('resources/assets/sass/app.scss', 'public/css/main.css')
   .combine([
        'resources/assets/gentelella/vendors/font-awesome/css/font-awesome.min.css',
        'resources/assets/gentelella/vendors/bootstrap-daterangepicker/daterangepicker.css',
        'resources/assets/gentelella/vendors/nprogress/nprogress.css',
        'public/css/main.css'
    ], 'public/css/main.css');
