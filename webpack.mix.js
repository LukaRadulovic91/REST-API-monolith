let mix = require('laravel-mix');


mix.js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css')
    .styles('node_modules/toastr/build/toastr.css', 'public/css/toastr.css') // Include Toastr CSS
    .scripts('node_modules/toastr/toastr.js', 'public/js/toastr.js'); // Include Toastr JavaScript

