/**
 * Generate as association card.
 *
 * @author Matt Glorion <matt@glorion.fr>
 *
 * @copyright Copyright (c) 2019, SiMDE-UTC
 * @license GNU GPL-3.0
 * */

import React from 'react';
import bdeImage from '../../images/bde.jpg';

const AssoCard = ({ image, login, name, shortname }) => {
	const style = {
		backgroundImage: `url('${!image ? bdeImage : image}')`,
		backgroundSize: 'contain',
		backgroundRepeat: 'no-repeat',
	};

	return (
		<div className="asso-card">
			<div className="thumbnail" style={style}>
				<div className={`overlay ${login}`}>
					<div>{name}</div>
				</div>
			</div>
			<div className="name-container">
				<div className="asso-shortname">{shortname}</div>
			</div>
			<div className={`card-line bg-${login}`} />
		</div>
	);
};

export default AssoCard;
