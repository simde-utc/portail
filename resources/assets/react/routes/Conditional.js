/**
 * Gestion des routes privées sur condition
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019, SiMDE-UTC
 * @license GNU GPL-3.0
 */

import React from 'react';
import { Route, Redirect } from 'react-router-dom';

class ConditionalRoute extends React.Component {
	render() {
		const { redirect, isAllowed } = this.props;

		if (isAllowed()) {
			return <Route {...this.props} />;
		}

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

export default ConditionalRoute;
