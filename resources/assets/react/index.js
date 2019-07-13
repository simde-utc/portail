/**
 * Frontend entrypoint.
 *
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Natan Danous <natous.danous@hotmail.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

import React from 'react';
import ReactDOM from 'react-dom';
import { BrowserRouter as Router } from 'react-router-dom';
import { Provider } from 'react-redux';
import reduxStore from './redux/store';

import App from './App';

ReactDOM.render(
	<Provider store={reduxStore}>
		<Router>
			<App />
		</Router>
	</Provider>,
	document.getElementById('root')
);
