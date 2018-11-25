/**
 * Permet de faire en sorte de toujours avoir le BDE en image si l'image de base n'est pas trouvée
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
**/

import React from 'react';
import Img from 'react-image';
import bdeImage from '../../images/bde.jpg'

class Image extends React.Component {
	render() {
		var images = this.props.image;

		if (!Array.isArray(images)) {
			images = [images];
		}

		if (!this.props.unloader) {
			images.push(bdeImage);
		}

		return (
			<Img
				loader={( <span className="loader large active"></span> )}
				{ ...this.props }
				src={ images } />
		);
	}
}

export default Image;
