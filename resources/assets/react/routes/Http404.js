/**
 * Display an error page for a not found page.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

import React from 'react';
import { Link } from 'react-router-dom';

const Http404 = () => (
	<div className="container">
		<h1 className="title">
			Page introuvable <span className="text-light">404</span>
		</h1>
		<Link className="btn btn-primary" to="/">
			Retourner à l'accueil
		</Link>
	</div>
);

export default Http404;
