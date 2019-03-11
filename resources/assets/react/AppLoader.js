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
	// Données importantes à charger
	dataLoaded: [
		store.hasFinished('user'),
		store.hasFinished('user/permissions'),
		store.hasFinished('semesters/current'),
	],
}))
class AppLoader extends React.Component {
	// Toutes les infos à récupérer dès le lancement
	componentWillMount() {
		const { dispatch } = this.props;

		// Récupère les semestres
		dispatch(actions.semesters.all());
		// Récupère le semestre courant
		dispatch(actions.semesters('current').get());
		// Récupère les données utilisateurs
		dispatch(actions.user.all({ types: '*' }))
			.then(() => {
				window.isLogged = true;
			})
			.catch(() => {
				window.isLogged = false;
			});
		// Récupère les permissions de l'utilisateur
		dispatch(actions.user.permissions.all());
		// Récupère les associations de l'utilisateur
		dispatch(actions.user.assos.all());
		// Récupère les services de l'utilisateur
		dispatch(actions.user.services.all());

		library.add(fas, far, fab);
		moment.locale('fr');
	}

	// Permet d'afficher le chargement initial de la page
	render() {
		const { dataLoaded, generateChildren } = this.props;

		const isLoading = dataLoaded.some(loading => !loading);

		// Lorsque le chargement est terminé, on génère la page
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
