/**
 * Display Authorized applications and enable to revoke them.
 *
 * @author Corentin Mercier <corentin@cmercier.fr>
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
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
	assos: store.getData('assos'),
	assosFetching: store.isFetching('assos'),
	assosFetched: store.isFetched('assos'),
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
		const { dispatch, assosFetched, assosFetching } = this.props;

		dispatch(actions.config({ title: 'Mes applications' }));

		if (!assosFetched && !assosFetching) {
			dispatch(actions.assos.all());
		}

		actions.oauth.tokens.all().payload.then(({ data }) => {
			const tokens = data;
			Promise.all(
				tokens.map(async token => {
					const { data } = await actions.oauth.scopes.categories.all({
						scopes: token.scopes.join(' '),
					}).payload;

					return {
						data,
						tokenId: token.id,
					};
				})
			).then(data => {
				this.setState({
					tokens,
					categories: data,
				});
			});
		});
	}

	getAppsGrid() {
		const { tokens, categories } = this.state;
		const { assos } = this.props;

		console.log(tokens);

		if (tokens.length !== 0) {
			return (
				<div className="d-flex flex-wrap justify-content-start">
					{tokens &&
						Object.keys(categories).length > 0 &&
						tokens.map(token => {
							return (
								<ApplicationCard
									key={token.id}
									application={token}
									categories={categories.find(el => el.tokenId === token.id).data}
									asso={assos.find(asso => asso.id === token.client.asso_id)}
									revokeToken={() => this.revokeToken(token.id)}
								/>
							);
						})}
				</div>
			);
		}

		return (
			<p className="text-center p-5">Aucune application n'est autorisée à accéder à vos données.</p>
		);
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
		return this.getAppsGrid();
	}
}
export default AppsScreen;
