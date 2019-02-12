/**
 * Permet de générer une Card asso.
 *
 * @author Matt Glorion <matt@glorion.fr>
 *
 * @copyright Copyright (c) 2019, SiMDE-UTC
 * @license GNU GPL-3.0
 * */

import React from 'react';
import bdeImage from '../../images/bde.jpg';

const AssoCard = ({ image, login, name, shortname }) => {
	return (
		<div className="asso-card">
			<div className="thumbnail" style={{ backgroundImage: `url('${!image ? bdeImage : image}')` }}>
				<div className={`overlay ${login}`}>{name}</div>
			</div>
			<div className="name-container">
				<div className="asso-shortname">{shortname}</div>
			</div>
			<div className={`card-line ${login}`} />
		</div>
	);
};

export default AssoCard;
