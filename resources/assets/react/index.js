/**
 * First we will load all of this project's JavaScript dependencies which
 * includes React and other helpers. It's a great starting point while
 * building robust, powerful web applications using React + Laravel.
 */

require('./bootstrap');

/**
 * Next, we will create a fresh React component instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import { BrowserRouter as Router } from 'react-router-dom';

// Store
import { Provider } from 'react-redux';
import store from './redux/store';

import App from './App.js';

ReactDOM.render((
	<Provider store={ store }>
		<Router>
			<App />
		</Router>
	</Provider>
), document.getElementById('root'));