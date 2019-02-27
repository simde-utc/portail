/**
 * Affiche une page d'erreur lorsque le Portail plante
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

import React from 'react';

const Http500 = () => (
	<div className="container">
		<h1 className="title">
			Une erreur a été rencontrée <span className="text-light">500</span>
		</h1>
		<a className="btn btn-primary" href="/">
			Retourner à l'accueil
		</a>
	</div>
);

export default Http500;
