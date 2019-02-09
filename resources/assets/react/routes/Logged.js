/**
 * Gestion des routes privées (par défaut, on doit être au moins connecté et ensuite d'avoir une spécifité)
 *
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
**/

import React from 'react'
import { connect } from 'react-redux'
import { Route, Redirect, Link } from 'react-router-dom'

@connect(store => ({
	user: store.getData('user'),
	isAuthenticated: store.isFetched('user'),
}))
class LoggedRoute extends React.Component {
	isAllowed() {
		if (this.props.isAuthenticated) {
			if (this.props.types && this.props.types.length) {
				for (let key in this.props.types) {
					if (this.props.user['is_' + this.props.types[key]]) {
						return true;
					}
				}
			}
			else {
				return true;
			}
		}

		return false;
	}

	render() {
		if (this.isAllowed()) {
			return (
				<Route
					{ ...this.props }
				/>
			);
		}
		else if (this.props.redirect) {
			return (
				<Route
					{ ...this.props }
					render={props => (
						<Redirect to={{ pathname: this.props.redirect, state: { from: props.location } }} />
					)}
				/>
			);
		}
		else {
			window.location.href = '/login?redirect=' + window.location.href;

			return null;
		}
	}
}

export default LoggedRoute;
