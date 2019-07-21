# Portal's Frontend

## Installation

- Install NodeJS
- Install dependencies with `npm install`
- Compile with `npm run dev`

## Architecture

The frontend is developed with ReactJS, Bootstrap and Semantic UI.
All is in the folder `resources/`

The React folders can be found in `resources/react/`.
React is integrated to Laravel trought the view `resources/view/react.blade.php`.
A connection token to the API is totally handled by Laravel. You just have to do calls with Axios.
For more information, read [the react documentation](./react.md).

Bootstrap Sass files can be found in the folder `resources/assets/bootstrap/sass/`. Bootstrap theme can easily be modified in the file `resources/assets/bootstrap/sass/_variables.scss`.

Customized stylesheets can be found `resources/assets/sass/`.
They use Bootstrap variables to stay consistent and they define additional style.

Semantic UI may be used later for its React components.

All files are compiled by Laravel Mix, configured by `webpack.mix.js`.

## Development

To run the development server BrowserSync:
- Make sure you've configured `APP_URL` in the file `.env` with the Laravel server adress.
- Use `npm run watch`
- The development server runs on the port 3000 by default.
- The display is reloaded at every change in the files.