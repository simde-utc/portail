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

const ConditionalRoute = props => {
	const { redirect, isAllowed } = props;

	if (isAllowed()) {
		return <Route {...props} />;
	}

	if (redirect) {
		return (
			<Route
				{...props}
				render={props => <Redirect to={{ pathname: redirect, state: { from: props.location } }} />}
			/>
		);
	}

	window.location.replace(`/login?redirect=${window.location.href}`);

	return null;
};

export default ConditionalRoute;
