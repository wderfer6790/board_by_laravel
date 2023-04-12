const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */
// mix.js('node_modules/bootstrap/dist/js/bootstrap.js', 'public/js/app.js')
//     .js('node_modules/jquery/dist/jquery.js', 'public/js/app.js')
//     .css('node_modules/bootstrap/dist/css/bootstrap.css', 'public/css/app.css');

mix.scripts([
        'node_modules/jquery/dist/jquery.js',
        'node_modules/bootstrap/dist/js/bootstrap.js',
        'node_modules/bootstrap/dist/js/bootstrap.bundle.js',
    ], 'public/js/app.js')
    .styles([
        'node_modules/bootstrap/dist/css/bootstrap.css',
        'node_modules/bootstrap/dist/css/bootstrap-grid.css',
        'node_modules/bootstrap/dist/css/bootstrap-reboot.css',
        'node_modules/bootstrap/dist/css/bootstrap-utilities.css',


    ], 'public/css/app.cs');
