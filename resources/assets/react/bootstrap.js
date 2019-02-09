/**
 * Préparation de l'application
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Natan Danous <natous.danous@hotmail.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

import { store } from './redux/store';

window._ = require('lodash');

// Permet d'exécuter des requêtes Ajax simplement vers le Portail
window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Récupération du token CSRF
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
	window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}
else {
	console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

// Ajout un intercepter de réponse.
window.axios.interceptors.response.use(
	response => response,
	error => {
		// Récupération des requêtes HTTP 401
		if (error.response.status === 401 && store.resources) {
			store.resources.user = {};
		}

		return Promise.reject(error);
	}
);
