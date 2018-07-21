import React, { Component } from 'react';
import { Link } from 'react-router-dom';


class AssociativeCarreer extends Component {
	render() {
		const { carreer } = this.props;
		return (
			<div>
				<h2>Mon Parcours Associatif</h2>
			</div>
		);
	}
}

export default AssociativeCarreer;