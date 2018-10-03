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
import bdeImage from '../images/bde.jpg'

@connect(store => ({
	loading: [
		store.hasFinished('login'),
		store.hasFinished('user'),
		store.hasFinished('user/permissions'),
		// Important ?
		// store.hasFinished('user/assos'),
		// store.hasFinished('user/services'),
	]
}))
class AppLoader extends React.Component {
	// Toutes les infos à récupérer dès le lancement
	componentWillMount() {
		// Get Login Methods
		this.props.dispatch(actions.login.all())
		// Get Semesters
		this.props.dispatch(actions.semesters.all())
		// Get User Info
		this.props.dispatch(actions.user.all({ allTypes: true }))
		// Get User Permissions
		this.props.dispatch(actions.user.permissions.all())
		// Get User Assos
		this.props.dispatch(actions.user.assos.all());
		// Get User Services
		this.props.dispatch(actions.user.services.all());
	}

	// Permet d'afficher le chargement initial de la page
	render() {
		return (
			<LoadingScreen
				loading={ this.props.loading.some(loading => !loading) }
			    bgColor='#f1f1f1'
			    spinnerColor='#9ee5f8'
			    textColor='#676767'
			    logoSrc={ bdeImage }
			    text="Portail des Associations"
			>
				<div id="loaded"></div>
			</LoadingScreen>
		);
	}
}

export default AppLoader;
