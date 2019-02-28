/**
 * Gestion des routes privées (par défaut, on doit être au moins connecté et ensuite d'avoir une spécifité)
 *
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

import React from 'react';
import { connect } from 'react-redux';

import ConditionalRoute from './Conditional';

@connect(store => ({
	user: store.getData('user'),
	isLoading: store.isFetching('user'),
	isAuthenticated: store.isFetched('user'),
}))
class LoggedRoute extends React.Component {
	isAllowed() {
		const { isAuthenticated, isLoading, user, types } = this.props;

		// On ne redirige pas tant qu'on ne possède pas les données de l'utilisateur.
		if (isLoading) {
			return true;
		}

		if (isAuthenticated) {
			if (types && types.length) {
				for (let key = 0; key < types.length; key++) {
					if (user.types[types[key]]) {
						return true;
					}
				}
			} else {
				return true;
			}
		}

		return false;
	}

	render() {
		return <ConditionalRoute isAllowed={this.isAllowed.bind(this)} {...this.props} />;
	}
}

export default LoggedRoute;
