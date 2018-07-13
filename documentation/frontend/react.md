# React

Le frontend du portail des assos est une Single Page Application (SPA) développée avec React.
Tous les fichiers se trouvent dans `resources/assets/react/`.


## Architecture

Le point d'entrée de React est `index.js`.


Les vues (_screens_) se trouvent dans le dossier `screens/`. Il s'agit de composant qui agglomèrent d'autres composants afin de former une interface utilisateur cohérente. Ces composants sont suffixé par `Screen` (par exemple _AssosListScreen_)


Les composants réutilisables se trouvent dans `components/`. Ils sont utilisés par différentes vues.


## Redux

Redux est utilisé pour gérer les données dans un store.
Toute l'implémentation de Redux est faite dans le dossier `redux/`.
Pour plus d'informations, [lisez la documentation ici.](./redux.md)