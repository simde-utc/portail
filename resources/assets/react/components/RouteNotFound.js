import React from 'react';
import { Link } from 'react-router-dom';

export default class RouteNotFoundScreen extends React.Component {
	render() {
		return (
			<div className="container">
				<h1 className="title">Page introuvable <span className="text-light">404</span></h1>
				<Link className="btn btn-primary" to="/">Retourner à l'accueil</Link>
			</div>
		);
	}
}

