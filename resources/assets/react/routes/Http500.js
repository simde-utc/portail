/**
 * Affiche une page d'erreur lorsque le Portail plante
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

import React from 'react';
import { Link } from 'react-router-dom';

const Http500 = () => (
	<div className="container">
		<h1 className="title">
			Une erreur a été rencontrée <span className="text-light">500</span>
		</h1>
		<img src="https://emoji.slack-edge.com/T0ME52X2Q/samy/c090ea5060c3e6a6.jpg" alt="Samy" />
		<Link className="btn btn-primary" to="/">
			Retourner à l'accueil
		</Link>
	</div>
);

export default Http500;
