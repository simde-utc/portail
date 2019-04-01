/**
 * Affichage des demandes d'accès.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019, SiMDE-UTC
 * @license GNU GPL-3.0
 */

import React from 'react';
import { Button } from 'reactstrap';
import { connect } from 'react-redux';
import { NotificationManager } from 'react-notifications';
import { find } from 'lodash';

import AccessForm from '../../components/Access/Form';
import AccessList from '../../components/Access/List';

import actions from '../../redux/actions';

@connect((store, props) => {
	const user = store.getData('user');

	return {
		user,
		config: store.config,
		fetched: store.isFetched(['assos', props.asso.id, 'access']),
		fetching: store.isFetching(['assos', props.asso.id, 'access']),
		memberAccess: store.getData(['assos', props.asso.id, 'access']),
		access: store.getData(['access']),
		accessFetched: store.isFetched(['access']),
		permissions: store.getData(['assos', props.asso.id, 'members', user.id, 'permissions']),
	};
})
class AccessScreen extends React.Component {
	constructor(props) {
		super(props);

		this.state = {
			openModal: false,
		};

		const { asso, dispatch } = props;

		if (asso.id) {
			this.loadAssosData(asso.id);
		}

		dispatch(actions.config({ title: `${asso.shortname} - Accès` }));
	}

	componentDidUpdate({ asso }) {
		const {
			asso: { id, shortname },
			accessFetched,
			dispatch,
		} = this.props;

		if (asso.id !== id) {
			this.loadAssosData(id);
		}

		if (!accessFetched) {
			dispatch(actions.access.all());
		}

		dispatch(actions.config({ title: `${shortname} - Accès` }));
	}

	loadAssosData(id) {
		const { dispatch } = this.props;

		dispatch(actions.assos(id).access.all());
	}

	sendDemand(data) {
		const { asso, permissions, dispatch } = this.props;

		actions
			.assos(asso.id)
			.access.create(data)
			.payload.then(({ data: { id: access_id } }) => {
				dispatch(actions.assos(asso.id).access.all());
				NotificationManager.success(
					"La demande d'accès a été envoyée. En attente de la confirmation d'un responsable de l'association",
					"Demande d'accès"
				);

				this.setState({ openModal: false });

				// L'utilisateur peut confirmer sa propre demande.
				if (find(permissions, permission => permission.type === 'access')) {
					actions
						.assos(asso.id)
						.access(access_id)
						.update()
						.payload.then(() => {
							dispatch(actions.assos(asso.id).access.all());
							NotificationManager.success(
								"La demande d'accès a été automatiquement confirmée. En attente de validation de l'accès",
								"Demande d'accès"
							);
						})
						.catch(() => {
							NotificationManager.error(
								"La demande d'accès n'a pas pu être automatiquement confirmée",
								"Demande d'accès"
							);
						});
				}
			})
			.catch(() => {
				NotificationManager.error(
					"La demande d'accès n'a pas pu être envoyée. Il se peut qu'une demande soit déjà en cours",
					"Demande d'accès"
				);
			});
	}

	confirm(acces) {
		const { asso, dispatch } = this.props;

		actions
			.assos(asso.id)
			.access(acces.id)
			.update()
			.payload.then(() => {
				dispatch(actions.assos(asso.id).access.all());
				NotificationManager.success(
					"La demande d'accès a été confirmée. En attente de validation de l'accès",
					"Demande d'accès"
				);
			})
			.catch(() => {
				NotificationManager.error(
					"La demande d'accès n'a pas pu être confirmée",
					"Demande d'accès"
				);
			});
	}

	cancel(acces) {
		const { asso, dispatch } = this.props;

		actions
			.assos(asso.id)
			.access(acces.id)
			.delete()
			.payload.then(() => {
				dispatch(actions.assos(asso.id).access.all());
				NotificationManager.success("La demande d'accès a été annulée", "Demande d'accès");
			})
			.catch(() => {
				NotificationManager.error("La demande d'accès n'a pas pu être annulée", "Demande d'accès");
			});
	}

	render() {
		const { user, members, memberAccess, access, permissions, fetched } = this.props;
		const { openModal } = this.state;
		const userAccessDemand = find(memberAccess, memberAccess => memberAccess.member.id === user.id);
		const userCanConfirm = find(permissions, permission => permission.type === 'access');

		return (
			<div className="container">
                <div className="top-right-button">
                    {fetched && !userAccessDemand && (
                        <Button color="primary" outline onClick={() => this.setState({ openModal: true })}>
                        Réaliser une demande
                        </Button>
                    )}
                </div>
				<AccessForm
					access={access}
					post={this.sendDemand.bind(this)}
					opened={openModal}
					closeModal={() => this.setState({ openModal: false })}
				/>
				{fetched && (
					<AccessList
						list={memberAccess}
						members={members}
						canConfirm={userCanConfirm}
						confirm={this.confirm.bind(this)}
						cancel={this.cancel.bind(this)}
					/>
				)}
			</div>
		);
	}
}

export default AccessScreen;
