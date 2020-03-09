const mix = require('laravel-mix');
require("laravel-mix-polyfill");
require('dotenv').config();
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
	outputStyle: 'compressed',
};

// Custom Bootstrap
mix.sass('resources/assets/bootstrap/sass/bootstrap.scss', 'public/css', sassOptions);
// Custom CSS
mix.sass('resources/assets/sass/app.scss', 'public/css', sassOptions);

// React
mix.react('resources/assets/react/index.js', 'public/js');

// Browser Sync Auto-Reload
// Simply put the url where laravel run in .env APP_URL
// Use npm run watch to lauch the dev server on port 3000 (by default)
mix.browserSync(process.env.APP_URL);

mix.disableSuccessNotifications();
mix.webpackConfig({
    module: {
        rules: [{
            test: /\.jsx?$/,
            exclude: /(bower_components)/,
            use: [
                {
                    loader: 'babel-loader',
                    options: mix.config.babel()
                },
            ],
        }],
    }
});
// Polyfill
mix.polyfill({
	enabled : true,
	useBuiltIns: 'entry',
	targets: "defaults",
	entry : "stable"
});