/**
 * Partner display.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

import React from 'react';
import AspectRatio from 'react-aspect-ratio';

import Img from './Image';

const Service = ({ name, image, description }) => (
	<div className="m-0 my-3 my-md-4 justify-content-start align-items-sm-center">
		<AspectRatio className="mb-2" ratio="1">
			<Img image={image} style={{ width: '100px' }} />
		</AspectRatio>
		<div>
			<h3>{name}</h3>
			<p>{description}</p>
		</div>
	</div>
);

export default Service;
