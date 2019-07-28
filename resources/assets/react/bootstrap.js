/**
 * Application preparation.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Natan Danous <natous.danous@hotmail.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

window._ = require('lodash');

// Allow to execute Ajax request simply to the portal.
window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// CSRF token retrievmement.
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
	window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
	console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

// Add a response intercepter.
window.axios.interceptors.response.use(
	response => response,
	error => {
		// HTTP 401 request catching.
		if (error.response.status === 401 && window.isLogged) {
			window.location.reload();
		}

		return Promise.reject(error);
	}
);
