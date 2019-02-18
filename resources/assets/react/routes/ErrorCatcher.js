/**
 * Récupère les erreurs
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

import React from 'react';
import Http500 from './Http500';

export default class ErrorCatcher extends React.Component {
	constructor(props) {
		super(props);

		this.state = {
			hasError: false,
		};
	}

	componentDidCatch(error, info) {
		this.setState({ hasError: true });

		console.warn('Error catched !', error, info);
	}

	render() {
		const { hasError } = this.state;
		const { children } = this.props;

		if (hasError) {
			return <Http500 />;
		}
		return children;
	}
}
