import React from 'react';
import { Link } from 'react-router-dom';

export default class NotFoundRoute extends React.Component {
	render() {
		return (
			<div className="container">
				<h1 className="title">Page introuvable <span className="text-light">404</span></h1>
				<img src="https://emoji.slack-edge.com/T0ME52X2Q/samy/c090ea5060c3e6a6.jpg" />
				<Link className="btn btn-primary" to="/">Retourner à l'accueil</Link>
			</div>
		);
	}
}
