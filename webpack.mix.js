let mix = require('laravel-mix');

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

const sassOptions = {
	outputStyle: 'compressed'
};

// Custom Bootstrap
mix.sass('resources/assets/bootstrap/sass/bootstrap.scss', 'public/css', sassOptions)
// Custom CSS
mix.sass('resources/assets/sass/app.scss', 'public/css');

// React
mix.react('resources/assets/react/index.js', 'public/js');




