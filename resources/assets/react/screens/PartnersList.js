/**
 * List partners.
 *
 * @author Corentin Mercier <corentin@cmercier.fr>
 *
 * @copyright Copyright (c) 2019, SiMDE-UTC
 * @license GNU GPL-3.0
 */

import React from 'react';
import { Button } from 'reactstrap';

import { connect } from 'react-redux';
import actions from '../redux/actions';

import Partner from '../components/Partner';

@connect()
class PartnerListScreen extends React.Component {
	constructor() {
		super();

		this.state = {
			partners: [],
			page: 1,
			fetched: false,
			canAskMore: true,
		};

		this.moreArticles = this.moreArticles.bind(this);
	}

	componentDidMount() {
		const { dispatch } = this.props;

		actions
			.partners()
			.all()
			.payload.then(({ data }) =>
				this.setState(state => {
					const oldState = state || {};
					return {
						...oldState,
						canAskMore: true,
						partners: data,
						fetched: true,
					};
				})
			);

		dispatch(actions.config({ title: 'Listes des Partenaires du BDE-UTC' }));
	}

	moreArticles() {
		const { page } = this.state;
		const newPage = { page: page + 1 };

		// Do not display the button while fetching
		this.setState(oldState => ({ ...oldState, canAskMore: false, fetched: false }));

		actions
			.partners()
			.all(newPage)
			.payload.then(({ data }) =>
				this.setState(state => {
					return {
						...state,
						...newPage,
						canAskMore: true,
						partners: [...state.partners, ...data],
						fetched: true,
					};
				})
			)
			.catch(() => {
				// No more pages : Nothing to do, canAskMore is already set to false
			});
	}

	render() {
		const { partners, canAskMore, fetched } = this.state;
		if (partners.length) {
			return (
				<div className="container">
					{partners.map(({ id, description, image, name, website, address, postal_code, city }) => (
						<Partner
							key={id}
							name={name}
							image={image}
							description={description}
							website={website}
							address={address}
							postal_code={postal_code}
							city={city}
						/>
					))}
					{fetched && canAskMore && (
						<div className="d-flex justify-content-center">
							<Button className="btn btn-primary mb-3" onClick={this.moreArticles} color="primary">
								Voir plus de partenaires !
							</Button>
						</div>
					)}
				</div>
			);
		}
		return (
			<p className="text-center p-5">Aucun partenariat n'a encore été référencé sur le portail.</p>
		);
	}
}

export default PartnerListScreen;
