/**
 * List partners.
 *
 * @author Corentin Mercier <corentin@cmercier.fr>
 *
 * @copyright Copyright (c) 2019, SiMDE-UTC
 * @license GNU GPL-3.0
 */

import React from 'react';
import { connect } from 'react-redux';
import actions from '../redux/actions';

import Partner from '../components/Partner';

@connect()
class PartnerListScreen extends React.Component {
	constructor() {
		super();

		this.state = {
			partners: [],
		};
	}

	componentDidMount() {
		const { dispatch } = this.props;

		actions.partners.all().payload.then(({ data }) => {
			this.setState({ partners: data });
		});

		dispatch(actions.config({ title: 'Listes des Partenaires du BDE-UTC' }));
	}

	render() {
		const { partners } = this.state;

		if (partners.length) {
			return (
				<div className="container">
					{partners.map(({ id, description, image, name }) => (
						<Partner key={id} name={name} image={image} description={description} />
					))}
				</div>
			);
		}
		return (
			<p className="text-center p-5">Aucun partenariat n'a encore été référencé sur le portail.</p>
		);
	}
}

export default PartnerListScreen;
