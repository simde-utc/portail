/**
 * Puts the BDE-UTC image if the original image isn't found.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

import React from 'react';
import Img from 'react-image';
import bdeImage from '../../images/bde.jpg';

const Image = ({ image, images, unloader, ...props }) => {
	let src = [];

	if (image) {
		src = [image];
	} else if (images) {
		src = Array.isArray(images) ? images : [images];
	}

	if (!unloader) {
		src.push(bdeImage);
	}

	return (
		<Img
			loader={<span className="loader small active" />}
			unloader={unloader}
			{...props}
			src={src}
		/>
	);
};

export default Image;
