/**
 * Partner display.
 *
 * @author Corentin Mercier <corentin@cmercier.fr>
 *
 * @copyright Copyright (c) 2019, SiMDE-UTC
 * @license GNU GPL-3.0
 */

import React from 'react';
import AspectRatio from 'react-aspect-ratio';
import { Button } from 'reactstrap';
import { NotificationManager } from 'react-notifications';

import Img from './Image';

const getWebsite = website => {
	if (website) {
		return (
			<p>
				Site Web :{' '}
				<a href={website} target="_blank" rel="noopener noreferrer">
					{website}
				</a>
			</p>
		);
	}
};

const getAddress = (address, postal_code, city) => {
	if (address && postal_code && city) {
		return (
			<p>
				Adresse : {address}, {postal_code}, {city}
			</p>
		);
	}
};
const mapsSearch = (address, postal_code, city, service) => {
	const queryElements = address
		.split(' ')
		.concat([postal_code])
		.concat(city.split(' '));

	const query = queryElements.map(element => encodeURIComponent(element)).join('+');
	let baseUrl;

	switch (service) {
		case 'gmaps':
			baseUrl = `https://www.google.com/maps/search/?api=1&query=`;
			break;
		case 'mappy':
			baseUrl = `https://www.qwant.com/?q=%26mappy+`;
			break;
		case 'qmaps':
			baseUrl = `https://duckduckgo.com/?q=%21qmaps+`;
			break;
		default:
			NotificationManager.error("Ce service de carte n'est pas implémenté sur le portail.");
			return;
	}
	window.open(baseUrl + query, '_blank');
};

const Partner = ({ name, image, description, website, address, postal_code, city }) => {
	const fullAddress = address && postal_code && city;
	return (
		<div className="mx-auto my-3 my-md-4 d-flex justify-content-between align-items-sm-center flex-wrap">
			<div className="w-75 mr-3">
				<div className="d-flex justify-content-start align-items-center">
					<AspectRatio className="mb-2 mr-3" ratio="1">
						<Img image={image} style={{ width: '100px' }} />
					</AspectRatio>
					<h3>{name}</h3>
				</div>
				<div>
					{getWebsite(website)}
					{getAddress(address, postal_code, city)}
					<p>{description}</p>
				</div>
			</div>
			{fullAddress && (
				<div className="d-flex flex-column justify-content-center">
					<Button
						className="m-1 btn btn-sm font-weight-bold"
						color="primary"
						outline
						onClick={() => mapsSearch(address, postal_code, city, 'qmaps')}
					>
						Trouver sur Qwant Maps
					</Button>
					<Button
						className="m-1 btn btn-sm font-weight-bold"
						color="primary"
						outline
						onClick={() => mapsSearch(address, postal_code, city, 'mappy')}
					>
						Trouver sur Mappy
					</Button>
					<Button
						className="m-1 btn btn-sm font-weight-bold"
						color="primary"
						outline
						onClick={() => mapsSearch(address, postal_code, city, 'gmaps')}
					>
						Trouver sur Google Maps
					</Button>
				</div>
			)}
		</div>
	);
};

export default Partner;
