/**
 * Association display preparation.
 *
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 * @author Natan Danous <natous.danous@hotmail.fr>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Corentin Mercier <corentin@cmercier.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

import React from 'react';
import { connect } from 'react-redux';
import { NavLink, Route, Switch } from 'react-router-dom';
import { NotificationManager } from 'react-notifications';
import { Modal, ModalHeader, ModalBody, ModalFooter, Button } from 'reactstrap';
import Select from 'react-select';
import actions from '../../redux/actions';

import ConditionalRoute from '../../routes/Conditional';
import Http404 from '../../routes/Http404';

import AssoHomeScreen from './Home';
import ArticleList from './ArticleList';
import AssoMemberListScreen from './MemberList';
import AssoCalendar from './Calendar';
import AccessScreen from './Access';

@connect(
	(
		store,
		{
			match: {
				params: { login },
			},
		}
	) => {
		const user = store.getData('user', false);
		const asso = store.getData(['assos', login]);

		return {
			user,
			asso,
			config: store.config,
			isNotConnected: store.hasFailed('user'),
			member: store.findData(['user', 'assos'], login, 'login', false),
			roles: store.getData(['assos', asso.id, 'roles']),
			memberPermissions: store.getData(['assos', asso.id, 'members', user.id, 'permissions']),
			fetching: store.isFetching(['assos', login]),
			fetched: store.isFetched(['assos', login]),
			failed: store.hasFailed(['assos', login]),
		};
	}
)
class AssoScreen extends React.Component {
	constructor(props) {
		super(props);

		this.state = {
			modal: {
				show: false,
				title: '',
				body: '',
				button: {
					type: '',
					text: '',
					onClick: () => {},
				},
			},
		};
	}

	componentDidMount() {
		const {
			match: {
				params: { login },
			},
		} = this.props;

		this.loadAssosData(login);
	}

	componentDidUpdate({ match: { params } }) {
		const {
			match: {
				params: { login },
			},
		} = this.props;

		if (params.login !== login) {
			this.loadAssosData(login);
		}
	}

	static getAllEvents(events) {
		const allEvents = [];

		for (const calendar_id in events) {
			for (const id in events[calendar_id]) {
				allEvents.push(events[calendar_id][id]);
			}
		}

		return allEvents;
	}

	loadAssosData(login) {
		// First, we need to find the assos id.
		const { dispatch } = this.props;
		const action = actions.assos.find(login);

		dispatch(action);

		action.payload.then(() => {
			const { asso, user, isNotConnected } = this.props;

			if (!isNotConnected) {
				dispatch(
					actions.definePath(['assos', asso.id, 'roles']).roles.all({ owner: `asso,${asso.id}` })
				);
			}

			if (user) {
				dispatch(
					actions
						.assos(asso.id)
						.members(user.id)
						.permissions.all()
				);
			}
		});
	}

	followAsso() {
		const { asso, dispatch } = this.props;

		this.setState({
			modal: {
				show: true,
				title: 'Suivre une association',
				body: (
					<p>
						Souhaitez-vous vraiment suivre l'association{' '}
						<span className="font-italic">{asso.name}</span> ?
					</p>
				),
				button: {
					type: 'success',
					text: 'Suivre',
					onClick: () => {
						actions.user.assos
							.create(
								{},
								{
									asso_id: asso.id,
								}
							)
							.payload.then(() => {
								dispatch(actions.user.assos.all({ only: 'joined,joining,followed' }));
								dispatch(actions.assos(asso.id).members.all());
								NotificationManager.success(
									`Vous suivez maintenant l'association: ${asso.name}`,
									'Suivre une association'
								);
							})
							.catch(() => {
								NotificationManager.error(
									`Vous n'avez pas le droit de suivre cette association: ${asso.name}`,
									'Suivre une association'
								);
							})
							.finally(() => {
								this.setState(({ modal }) => ({
									modal: { ...modal, show: false },
								}));
							});
					},
				},
			},
		});
	}

	unfollowAsso() {
		const { asso, dispatch } = this.props;

		this.setState({
			modal: {
				show: true,
				title: 'Ne plus suivre une association',
				body: (
					<p>
						Souhaitez-vous vraiment ne plus suivre l'association{' '}
						<span className="font-italic">{asso.name}</span> ?
					</p>
				),
				button: {
					type: 'danger',
					text: 'Ne plus suivre',
					onClick: () => {
						actions.user.assos
							.remove(asso.id)
							.payload.then(() => {
								dispatch(actions.user.assos.all({ only: 'joined,joining,followed' }));
								dispatch(actions.assos(asso.id).members.all());
								NotificationManager.warning(
									`Vous ne suivez plus l'association: ${asso.name}`,
									'Suivre une association'
								);
							})
							.finally(() => {
								this.setState(({ modal }) => ({
									modal: { ...modal, show: false },
								}));
							});
					},
				},
			},
		});
	}

	joinAsso(isContributorBde) {
		const { asso, dispatch, roles, user } = this.props;

		const modal = {
			show: true,
			title: 'Rejoindre une association',
		};

		if (!isContributorBde) {
			modal.body = (
				<div>
					<p>
						Souhaitez-vous rejoindre l'association <span className="font-italic">{asso.name}</span>?
					</p>
					<p>Pour cela, il faut que vous cotisiez au BDE-UTC.</p>
				</div>
			);
			modal.button = {
				type: 'success',
				text: 'Cotiser au BDE-UTC',
				onClick: () => {
					window.open('https://assos.utc.fr/bde/bdecotiz/', '_blank');
				},
			};
		} else {
			modal.body = (
				<div>
					<p>
						Souhaitez-vous rejoindre l'association <span className="font-italic">{asso.name}</span>?
					</p>
					<p>Pour cela, il faut que vous renseignez votre rôle et qu'un membre autorisé valide.</p>
					<Select
						onChange={role => {
							this.setState({ role_id: role.value });
						}}
						name="role_id"
						placeholder="Rôle dans cette association"
						options={roles.map(role => ({
							value: role.id,
							label: `${role.name} - ${role.description}`,
						}))}
					/>
				</div>
			);
			modal.button = {
				type: 'success',
				text: "Rejoindre l'association",
				onClick: () => {
					const { role_id } = this.state;

					if (!role_id) return;

					actions
						.assos(asso.id)
						.members.create(
							{},
							{
								user_id: user.id,
								role_id,
							}
						)
						.payload.then(({ data: { id: member_id } }) => {
							const { user, asso } = this.props;

							dispatch(actions.user.assos.all({ only: 'joined,joining,followed' }));
							dispatch(actions.assos(asso.id).members.all());
							NotificationManager.success(
								`Vous avez demandé à rejoindre l'association: ${asso.name}`,
								"Devenir membre d'une association"
							);

							actions
								.assos(asso.id)
								.members.update(member_id)
								.payload.then(() => {
									dispatch(actions.assos(asso.id).members.all());
									NotificationManager.success(
										`Vous avez automatiquement été validé dans l'association: ${asso.name}`,
										"Valider un membre d'une association"
									);

									if (user.id === member_id) {
										dispatch(actions.user.assos.all({ only: 'joined,joining,followed' }));
										dispatch(actions.user.permissions.all());
									}
								})
								.catch(() => {});
						})
						.catch(() => {
							NotificationManager.error(
								`Vous ne pouvez pas devenir membre de cette association: ${asso.name}`,
								"Devenir membre d'une association"
							);
						})
						.finally(() => {
							this.setState(({ modal }) => ({
								modal: { ...modal, show: false },
							}));
						});
				},
			};
		}
		this.setState(prevState => ({
			...prevState,
			role_id: undefined,
			modal,
		}));
	}

	leaveAsso(isWaiting) {
		const { asso, dispatch, user } = this.props;
		const { name } = asso;

		this.setState({
			modal: {
				show: true,
				title: 'Quitter une association',
				body: (
					<div>
						<p>
							Souhaitez-vous vraiment quitter l'association{' '}
							<span className="font-italic">{name}</span> ?
						</p>
						<p>
							{isWaiting
								? 'Votre demande est encore en attente de validation.'
								: "En faisant ça, vous perdrez votre rôle dans cette association et un email sera envoyé pour notifier l'association de ce changement"}
						</p>
					</div>
				),
				button: {
					type: 'danger',
					text: "Quitter l'association",
					onClick: () => {
						actions
							.assos(asso.id)
							.members.remove(user.id)
							.payload.then(() => {
								dispatch(actions.user.assos.all({ only: 'joined,joining,followed' }));
								dispatch(actions.assos(asso.id).members.all());
								NotificationManager.warning(
									`Vous ne faites plus partie de l'association: ${name}`,
									'Quitter une association'
								);
							})
							.catch(() => {
								NotificationManager.error(
									`Une erreur a été rencontrée lorsque vous avez voulu quitter cette association: ${name}`,
									'Quitter une association'
								);
							})
							.finally(() => {
								this.setState(({ modal }) => ({
									modal: { ...modal, show: false },
								}));
							});
					},
				},
			},
		});
	}

	validateMember(member_id) {
		const { asso, dispatch } = this.props;
		const { name } = asso;

		this.setState({
			modal: {
				show: true,
				title: "Valider un membre de l'association",
				body: (
					<div>
						<p>
							Souhaitez-vous valider le poste de ce membre dans l'association{' '}
							<span className="font-italic">{name}</span> ?
						</p>
					</div>
				),
				button: {
					type: 'success',
					text: 'Valider le membre',
					onClick: () => {
						const { user, asso } = this.props;

						actions
							.assos(asso.id)
							.members.update(member_id)
							.payload.then(() => {
								dispatch(actions.assos(asso.id).members.all());
								NotificationManager.success(
									`Vous avez validé avec succès le membre de cette association: ${name}`,
									"Valider un membre d'une association"
								);

								if (user.id === member_id) {
									dispatch(actions.user.assos.all({ only: 'joined,joining,followed' }));
									dispatch(actions.user.permissions.all());
								}
							})
							.catch(() => {
								NotificationManager.error(
									`Vous n'avez pas le droit de valider le membre de cette association: ${name}`,
									"Valider un membre d'une association"
								);
							})
							.finally(() => {
								this.setState(({ modal }) => ({
									modal: { ...modal, show: false },
								}));
							});
					},
				},
			},
		});
	}

	leaveMember(member_id) {
		const { asso, dispatch } = this.props;
		const { name } = asso;

		this.setState({
			modal: {
				show: true,
				title: "Retirer le membre de l'association",
				body: (
					<div>
						<p>
							Souhaitez-vous vraiment retirer ce membre de l'association{' '}
							<span className="font-italic">{asso.name}</span> ?
						</p>
					</div>
				),
				button: {
					type: 'danger',
					text: 'Retirer le membre',
					onClick: () => {
						actions
							.assos(asso.id)
							.members.remove(member_id)
							.payload.then(() => {
								dispatch(actions.user.assos.all({ only: 'joined,joining,followed' }));
								dispatch(actions.assos(asso.id).members.all());
								NotificationManager.warning(
									`Vous avez retiré avec succès le membre de cette association: ${name}`,
									"Retirer un membre d'une association"
								);
							})
							.catch(() => {
								NotificationManager.error(
									`Vous n'avez pas le droit de retirer le membre de cette association: ${name}`,
									"Retirer un membre d'une association"
								);
							})
							.finally(() => {
								this.setState(({ modal }) => ({
									modal: { ...modal, show: false },
								}));
							});
					},
				},
			},
		});
	}

	postArticle(_data) {
		const data = _data;
		const { asso, dispatch } = this.props;

		data.owned_by_type = 'asso';
		data.owned_by_id = asso.id;

		dispatch(actions.articles.create(data));
	}

	render() {
		const { fetching, fetched, failed, user, asso, member, contacts, match } = this.props;
		const { modal } = this.state;

		if (failed) return <Http404 />;

		if (fetching || !fetched || !asso) return <span className="loader huge active" />;

		if (member) {
			const { pivot } = member;

			this.user = {
				isFollowing: pivot.role_id === null,
				isMember: pivot.role_id !== null && pivot.validated_by_id !== null,
				isWaiting: pivot.role_id !== null && pivot.validated_by_id === null,
			};
		} else {
			this.user = {
				isFollowing: false,
				isMember: false,
				isWaiting: false,
			};
		}

		let joinFromMemberList;
		if (Object.values(this.user).every(value => !value)) {
			joinFromMemberList = this.joinAsso.bind(this);
		}

		return (
			<div className="nav-container w-100">
				<Modal isOpen={modal.show}>
					<ModalHeader>{modal.title}</ModalHeader>
					<ModalBody>{modal.body}</ModalBody>
					<ModalFooter>
						<Button
							outline
							className="font-weight-bold"
							onClick={() => {
								this.setState(prevState => ({
									...prevState,
									modal: { ...prevState.modal, show: false },
								}));
							}}
						>
							Annuler
						</Button>
						<Button
							className="font-weight-bold"
							outline
							color={modal.button.type}
							onClick={modal.button.onClick}
						>
							{modal.button.text}
						</Button>
					</ModalFooter>
				</Modal>

				<ul className="nav nav-tabs asso">
					<li className="nav-item">
						<NavLink className="nav-link" activeClassName="active" exact to={`${match.url}`}>
							DESCRIPTION
						</NavLink>
					</li>
					<li className="nav-item">
						<NavLink className="nav-link" activeClassName="active" to={`${match.url}/articles`}>
							ARTICLES
						</NavLink>
					</li>
					{asso.in_cemetery_at == null && (
						<li className="nav-item">
							<NavLink className="nav-link" activeClassName="active" to={`${match.url}/events`}>
								ÉVÈNEMENTS
							</NavLink>
						</li>
					)}
					{user &&
						(user.types.casComfirmed || user.types.contributorBde) &&
						asso.in_cemetery_at == null && (
							<li className="nav-item">
								<NavLink className="nav-link" activeClassName="active" to={`${match.url}/members`}>
									TROMBINOSCOPE
								</NavLink>
							</li>
						)}
					{this.user.isMember && asso.in_cemetery_at == null && (
						<li className="nav-item">
							<NavLink className="nav-link" activeClassName="active" to={`${match.url}/access`}>
								ACCES
							</NavLink>
						</li>
					)}
				</ul>

				{asso && asso.parent && asso.in_cemetery_at != null && (
					<div className="bg-warning m-2 p-3">
						Attention l'association {asso.name} est morte, pour la reprendre envoyer un mail à son
						pole à l'adresse suivante : {asso.parent.login}@assos.utc.fr
					</div>
				)}

				<Switch>
					<Route
						path={`${match.url}`}
						exact
						render={() => (
							<AssoHomeScreen
								asso={asso}
								contacts={contacts}
								userIsFollowing={this.user.isFollowing}
								userIsMember={this.user.isMember}
								userIsWaiting={this.user.isWaiting}
								userIsContributorBde={user ? user.types.contributorBde : false}
								follow={this.followAsso.bind(this)}
								unfollow={this.unfollowAsso.bind(this)}
								join={this.joinAsso.bind(this)}
								leave={this.leaveAsso.bind(this)}
							/>
						)}
					/>
					<Route exact path={`${match.url}/events`} render={() => <AssoCalendar asso={asso} />} />
					<Route exact path={`${match.url}/articles`} render={() => <ArticleList asso={asso} />} />
					<ConditionalRoute
						path={`${match.url}/members`}
						redirect={`${match.url}`}
						isAllowed={() => {
							return user.types.contributorBde && asso.in_cemetery_at == null;
						}}
						render={() => (
							<AssoMemberListScreen
								asso={asso}
								isMember={this.user.isMember}
								isWaiting={this.user.isWaiting}
								leaveMember={id => {
									this.leaveMember(id);
								}}
								join={joinFromMemberList}
								validateMember={id => {
									this.validateMember(id);
								}}
							/>
						)}
					/>
					<ConditionalRoute
						exact
						path={`${match.url}/access`}
						redirect={`${match.url}`}
						isAllowed={() => {
							return this.user.isMember && asso.in_cemetery_at == null;
						}}
						render={() => <AccessScreen asso={asso} />}
					/>
				</Switch>
			</div>
		);
	}
}

export default AssoScreen;
