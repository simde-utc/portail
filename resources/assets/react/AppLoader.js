/**
 * Chargement de toutes les ressources de base
 * On réalise un loader séparé pour éviter les conflits entre loading/redux/router
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

import React from 'react';
import { connect } from 'react-redux';
import LoadingScreen from 'react-loading-screen';

import { library } from '@fortawesome/fontawesome-svg-core';
import { fas } from '@fortawesome/free-solid-svg-icons';
import { far } from '@fortawesome/free-regular-svg-icons';

import actions from './redux/actions';
import bdeImage from '../images/bde.jpg';

@connect(store => ({
	// Données importantes à charger
	dataLoaded: [
		store.hasFinished('login'),
		store.hasFinished('user'),
		store.hasFinished('user/permissions'),
		store.hasFinished('semesters/current'),
	],
}))
class AppLoader extends React.Component {
	// Toutes les infos à récupérer dès le lancement
	componentWillMount() {
		const { dispatch } = this.props;

		require('./bootstrap');
		library.add(fas, far);

		// Récupère les méthodes de connexion
		dispatch(actions.login.all());
		// Récupère les semestres
		dispatch(actions.semesters.all());
		// Récupère le semestre courant
		dispatch(actions.semesters('current').get());
		// Récupère les données utilisateurs
		dispatch(actions.user.all({ allTypes: true })).then(response => {
			window.isLogged = true;
		}).catch(response => {
			window.isLogged = false;
		});
		// Récupère les permissions de l'utilisateur
		dispatch(actions.user.permissions.all());
		// Récupère les associations de l'utilisateur
		dispatch(actions.user.assos.all());
		// Récupère les services de l'utilisateur
		dispatch(actions.user.services.all());
	}

	// Permet d'afficher le chargement initial de la page
	render() {
		const { dataLoaded } = this.props;

		return (
			<LoadingScreen
				loading={dataLoaded.some(loading => !loading)}
				bgColor="#f1f1f1"
				spinnerColor="#9ee5f8"
				textColor="#676767"
				logoSrc={bdeImage}
				text="Portail des Associations"
			>
				<div id="loaded" />
			</LoadingScreen>
		);
	}
}

export default AppLoader;
