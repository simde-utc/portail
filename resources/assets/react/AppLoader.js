/**
 * All base resources loading.
 * Seperate loader to avoid confilct with loading/redux/router.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

import React from 'react';
import { connect } from 'react-redux';
import LoadingScreen from 'react-loading-screen';
import moment from 'moment';

import { library } from '@fortawesome/fontawesome-svg-core';
import { fas } from '@fortawesome/free-solid-svg-icons';
import { far } from '@fortawesome/free-regular-svg-icons';
import { fab } from '@fortawesome/free-brands-svg-icons';

import actions from './redux/actions';
import bdeImage from '../images/bde.jpg';

import 'moment/locale/fr';

require('./bootstrap');

@connect(store => ({
	// Important data to load.
	dataLoaded: [
		store.hasFinished('user'),
		store.hasFinished('user/permissions'),
		store.hasFinished('semesters/current'),
	],
}))
class AppLoader extends React.Component {
	// All information to retrieve at the app launch.
	componentWillMount() {
		const { dispatch } = this.props;

		// Semesters retrievement.
		dispatch(actions.semesters.all());
		// Current semester retrievement.
		dispatch(actions.semesters('current').get());
		// User data retrievement.
		dispatch(actions.user.all({ types: '*' }))
			.then(() => {
				window.isLogged = true;
			})
			.catch(() => {
				window.isLogged = false;
			});
		// User permissions retrievement.
		dispatch(actions.user.permissions.all());
		// User's associations retrievement.
		dispatch(actions.user.assos.all());
		// User's services retrievement.
		dispatch(actions.user.services.all());

		library.add(fas, far, fab);
		moment.locale('fr');
	}

	// Displays initial page's loading.
	render() {
		const { dataLoaded, generateChildren } = this.props;

		const isLoading = dataLoaded.some(loading => !loading);

		// When the loading is over the page is generated.
		if (!isLoading) {
			generateChildren();
		}

		return (
			<LoadingScreen
				loading={isLoading}
				bgColor="#f1f1f1"
				spinnerColor="#9ee5f8"
				textColor="#676767"
				logoSrc={bdeImage}
				text="Portail des Associations"
			>
				<div />
			</LoadingScreen>
		);
	}
}

export default AppLoader;
