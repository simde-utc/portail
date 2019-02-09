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
import { Route, Redirect } from 'react-router-dom';

@connect(store => ({
	user: store.getData('user'),
	isAuthenticated: store.isFetched('user'),
}))
class LoggedRoute extends React.Component {
	isAllowed() {
		const { isAuthenticated, user, types } = this.props;

		if (isAuthenticated) {
			if (types && types.length) {
				for (let key = 0; key < types.length; key++) {
					if (user[`is_${types[key]}`]) {
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
		if (this.isAllowed()) {
			return <Route {...this.props} />;
		}

		const { redirect } = this.props;
		if (redirect) {
			return (
				<Route
					{...this.props}
					render={props => (
						<Redirect to={{ pathname: redirect, state: { from: props.location } }} />
					)}
				/>
			);
		}

		window.location.href = `/login?redirect=${window.location.href}`;

		return null;
	}
}

export default LoggedRoute;
