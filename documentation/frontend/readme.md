# Frontend du Portail

## Installation

- Installer NodeJS
- Installer les dépendances avec `npm install`
- Compiler avec `npm run dev`

## Architecture

Le frontend est développé avec ReactJS, Bootstrap et Semantic UI.
Tout se trouve dans le dossier `resources/`.


Les fichiers React se trouvent dans `resources/react/`.
React est intégré à Laravel via la vue `resources/view/react.blade.php`.
Un token de connexion à l'API est géré totalement par Laravel, il suffit alors de faire les appels avec Axios.
Pour plus d'information, lire [la documentation relative à React](./react.md).


Les fichiers Sass de Bootstrap se trouvent dans le dossier `resources/assets/bootstrap/sass/`. Cela permet de pouvoir modifier le thème de Bootstrap facilement avec le fichier `resources/assets/bootstrap/sass/_variables.scss`


Les feuilles de style personnalisées se trouvent dans `resources/assets/sass/`.
Elles utilisent les variables de Bootstrap pour rester cohérent et définissent des styles supplémentaires.


Semantic UI sera peut être utilisé plus tard pour ses composants React.


Tous les fichiers sont compilés par Laravel Mix, configuré par le fichier `webpack.mix.js`.


## Développement

Pour lancer le serveur de développement BrowserSync :
- Bien penser à configurer `APP_URL` dans le ficher `.env` avec l'adresse à laquelle tourne Laravel
- utiliser `npm run watch`
- le serveur de développement se lance alors sur le port 3000 (par défaut)
- chaque changement dans les fichiers provoque un reload de l'affichage