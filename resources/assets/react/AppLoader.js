/**
 * Chargement de toutes les ressources de base
 * On réalise un loader séparé pour éviter les conflits entre loading/redux/router
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license AGPL-3.0
**/

import React from 'react';
import { connect } from 'react-redux';
import { Route, Redirect, Switch } from 'react-router-dom';
import LoadingScreen from 'react-loading-screen';

import actions from './redux/actions.js';

@connect(store => ({
	loading: [
		store.hasFinished('login'),
		store.hasFinished('user'),
		store.hasFinished('user/permissions'),
		store.hasFinished('user/assos'),
	]
}))
class AppLoader extends React.Component {
	// Toutes les infos à récupérer dès le lancement
	componentWillMount() {
		// Get Login Methods
		this.props.dispatch(actions.login.all())
		// Get User Info
		this.props.dispatch(actions.user.get())
		// Get User Permissions
		this.props.dispatch(actions.user.permissions.all())
		// Get User Assos
		this.props.dispatch(actions.user.assos.all());
	}

	// Permet d'afficher le chargement initial de la page
	render() {
		return (
			<LoadingScreen
				loading={ this.props.loading.some(loading => !loading) }
			    bgColor='#f1f1f1'
			    spinnerColor='#9ee5f8'
			    textColor='#676767'
			    logoSrc='http://assos.utc.fr/larsen/style/img/logo-bde.jpg'
			    text="Portail des Associations"
			>
				<div id="loaded"></div>
			</LoadingScreen>
		);
	}
}

export default AppLoader;
