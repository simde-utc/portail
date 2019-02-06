/**
 * Permet de générer une Card asso.
 *
 * @author Matt Glorion <matt@glorion.fr>
 *
 * @copyright Copyright (c) 2019, SiMDE-UTC
 * @license GNU GPL-3.0
**/

import React from 'react';
import bdeImage from '../../images/bde.jpg'

class AssoCard extends React.Component {
	render() {
		var image = this.props.image;

		if (!this.props.unloader) {
			image = bdeImage;
		}

		return <div className="asso-card">
			<div className="thumbnail"
				 style={{backgroundImage: "url('" + image + "')"}}>
				<div className={ "overlay " + this.props.login}>{ this.props.name }</div>
			</div>
			<div className="content">
				<div className="asso-shortname">{ this.props.shortname }</div>
			</div>
			<div className={"card-line "+ this.props.login}/>
		</div>;
	}
}

export default AssoCard;
