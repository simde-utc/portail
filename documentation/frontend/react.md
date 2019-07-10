# React

The portal's frontend is a Single Page Application (SPA) developed with React.
All files can be found in `resources/assets/react/`.


## Architecture

The React entrypoint is `index.js`.

The views (_screens_) are in the folder `screens/`. They contain components wich use other components in order to create a consistent UI. They are sufixed by `Screen` (for example _AssosListScreen_).

Reusable components are in `components.`. They are used by several views.


## Redux

Redux is used to manage the data in a store.
The Redux implementation is done in the folder `redux/`.
For more information, [read the Redux documentation.](./redux.md)