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

mix.js(['resources/assets/js/app.js'], 'public/js')
   .scripts(['resources/assets/bootstrap-datepicker/js/bootstrap-datepicker.js',
            'resources/assets/bootstrap-datepicker/locales/bootstrap-datepicker.zh-CN.min.js',
            'resources/assets/js/report.js'
   ], 'public/js/all.js')
   .sass('resources/assets/sass/app.scss', 'public/css')
   .styles('resources/assets/bootstrap-datepicker/css/bootstrap-datepicker3.css', 'public/css/all.css');
