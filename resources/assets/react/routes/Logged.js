/**
 * Gestion des routes privées (par défaut, on doit être au moins connecté et ensuite d'avoir une spécifité)
 *
 * @author Alexandre Brasseur <alexandre.brasseur@etu.utc.fr>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license AGPL-3.0
**/

import React from 'react'
import { connect } from 'react-redux'
import { Route, Redirect } from 'react-router-dom'

const LoggedRoute = ({ component: Component, redirect, types, user, isAuthenticated, ...params }) => {
	const isAllowed = () => {
		if (isAuthenticated) {
			if (types && types.length) {
				for (let key in types) {
					if (user['is_' + types[key]]) {
						return true;
					}
				}
			}
			else {
				return true;
			}
		}

		return false;
	};

	return (
		<Route
			{ ...params }
			render={ props => (
				isAllowed() ? (
					<Component {...props} />
				) : (
					<Redirect to={{ pathname: redirect || '/', state: { from: props.location } }} />
				)
			)}
		/>
	);
}

export default connect(store => ({
	user: store.getData('user'),
	isAuthenticated: store.isFetched('user'),
}))(LoggedRoute);
