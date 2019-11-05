/**
 * Display Authorized applications and enable to revoke them.
 *
 * @author Corentin Mercier <corentin@cmercier.fr>
 *
 * @copyright Copyright (c) 2019, SiMDE-UTC
 * @license GNU GPL-3.0
 */

import React from 'react';
import { connect } from 'react-redux';
import { NotificationManager } from 'react-notifications';
import ApplicationCard from '../../components/Profile/ApplicationCard';

import actions from '../../redux/actions';

@connect(store => ({
	config: store.config,
}))
class AppsScreen extends React.Component {
	constructor(props) {
		super(props);

		this.state = {
			tokens: [],
			categories: {},
		};
	}

	componentDidMount() {
		const { dispatch } = this.props;

		dispatch(actions.config({ title: 'Mes applications' }));

		actions.oauth.tokens.all().payload.then(({ data }) => {
			const newCategories = [];
			data.forEach(token => {
				actions.oauth.scopes.categories
					.all({
						scopes: token.scopes.join(' '),
					})
					.payload.then(({ data }) => {
						this.addScopesDescription(token.id, data);
					});
			});

			this.setState(prevState => {
				prevState.tokens = data;
				prevState.categories = newCategories;
				return prevState;
			});
		});
	}

	addScopesDescription(index, data) {
		this.setState(prevState => {
			prevState.categories[index] = data;
			return prevState;
		});
	}

	revokeToken(applicationId) {
		const { tokens } = this.state;

		actions.oauth.tokens
			.delete(applicationId)
			.payload.then(() => {
				NotificationManager.success(
					"Les droits de l'application ont bien été supprimés.",
					'Mes applications'
				);

				this.setState(prevState => {
					prevState.categories[applicationId] = undefined;
					tokens.forEach((token, index) => {
						if (token && token.id === applicationId) {
							prevState.tokens.splice(index, 1);
						}
					});
					return prevState;
				});
			})
			.catch(() => {
				NotificationManager.error(
					"Les droits de l'application n'ont pas pas être supprimés.",
					'Mes applications'
				);
			});
	}

	render() {
		const { tokens, categories } = this.state;
		console.log(tokens, categories);
		return (
			<div className="d-flex flex-wrap justify-content-start">
				{tokens &&
					Object.keys(categories).length > 0 &&
					tokens.map(token => {
						return (
							<ApplicationCard
								key={token.id}
								application={token}
								categories={categories[token.id]}
								revokeToken={() => this.revokeToken(token.id)}
							/>
						);
					})}
			</div>
		);
	}
}
export default AppsScreen;
