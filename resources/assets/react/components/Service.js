/**
 * Affichage d'un service.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

import React from 'react';
import { Button } from 'reactstrap';

import Img from './Image';

const Service = ({ service, isFollowing, unfollow, follow }) => (
	<div className="Service row m-0 my-3 my-md-4 justify-content-start">
		<Img src={service.image} style={{ maxWidth: 100 }} />
		<a className="col-12 col-md-8 body" href={service.url}>
			<h3>{service.name}</h3>
			{service.description}
		</a>
		{isFollowing ? (
			<Button
				className="m-1 btn btn-sm font-weight-bold"
				color="warning"
				outline
				onClick={unfollow}
			>
				Retirer des favoris
			</Button>
		) : (
			<Button className="m-1 btn btn-sm font-weight-bold" color="primary" outline onClick={follow}>
				Ajouter aux favoris
			</Button>
		)}
	</div>
);

export default Service;
