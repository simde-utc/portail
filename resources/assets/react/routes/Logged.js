/**
 * Private routes management (Default: Must be at least connected then have a secificity).
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

		// No redirection while we don't have user data.
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
