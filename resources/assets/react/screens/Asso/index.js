/**
 * Prépartion de l'affichage d'une association.
 *
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 * @author Natan Danous <natous.danous@hotmail.fr>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

import React from 'react';
import { connect } from 'react-redux';
import { NavLink, Link, Route, Switch } from 'react-router-dom';
import { NotificationManager } from 'react-notifications';
import { Modal, ModalHeader, ModalBody, ModalFooter, Button } from 'reactstrap';
import Select from 'react-select';
import actions from '../../redux/actions';

import Dropdown from '../../components/Dropdown';
import ArticleForm from '../../components/Article/Form';
import LoggedRoute from '../../routes/Logged';
import ConditionalRoute from '../../routes/Conditional';
import Http404 from '../../routes/Http404';

import AssoHomeScreen from './Home';
import ArticleList from './ArticleList';
import AssoMemberListScreen from './MemberList';
import AssoCalendar from './Calendar';
import AccessScreen from './Access';

@connect((store, { match: { params: { login } } }) => {
	const user = store.getData('user', false);
	const asso = store.getData(['assos', login]);

	return {
		user,
		asso,
		member: store.findData(['user', 'assos'], login, 'login', false),
		roles: store.getData(['assos', asso.id, 'roles']),
		memberPermissions: store.getData(['assos', asso.id, 'members', user.id, 'permissions']),
		fetching: store.isFetching(['assos', login]),
		fetched: store.isFetched(['assos', login]),
		failed: store.hasFailed(['assos', login]),
	};
})
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

	componentWillMount() {
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
		// On doit d'abord récupérer l'asso id:
		const { dispatch } = this.props;
		const action = actions.assos.find(login);

		dispatch(action);

		action.payload.then(() => {
			const { asso, user } = this.props;

			if (user) {
				dispatch(
					actions.definePath(['assos', asso.id, 'roles']).roles.all({ owner: `asso,${asso.id}` })
				);

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
								dispatch(actions.user.assos.all());
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
								dispatch(actions.user.assos.all());
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

	joinAsso() {
		const { asso, dispatch, roles, user } = this.props;

		this.setState(prevState => ({
			...prevState,
			role_id: undefined,
			modal: {
				show: true,
				title: 'Rejoindre une association',
				body: (
					<div>
						<p>
							Souhaitez-vous rejoindre l'association{' '}
							<span className="font-italic">{asso.name}</span> ?
						</p>
						<p>
							Pour cela, il faut que vous renseignez votre rôle et qu'un membre autorisé valide.
						</p>
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
				),
				button: {
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
							.payload.then(() => {
								dispatch(actions.user.assos.all());
								dispatch(actions.assos(asso.id).members.all());
								NotificationManager.success(
									`Vous avez demandé à rejoindre l'association: ${asso.name}`,
									"Devenir membre d'une association"
								);
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
				},
			},
		}));
	}

	leaveAsso(isWaiting) {
		const { asso, dispatch, user } = this.props;

		this.setState({
			modal: {
				show: true,
				title: 'Quitter une association',
				body: (
					<div>
						<p>
							Souhaitez-vous vraiment quitter l'association{' '}
							<span className="font-italic">{asso.name}</span> ?
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
								dispatch(actions.user.assos.all());
								dispatch(actions.assos(asso.id).members.all());
								NotificationManager.warning(
									`Vous ne faites plus partie de l'association: ${asso.name}`,
									'Quitter une association'
								);
							})
							.catch(() => {
								NotificationManager.error(
									`Une erreur a été rencontrée lorsque vous avez voulu quitter cette association: ${
										asso.name
									}`,
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

		this.setState({
			modal: {
				show: true,
				title: "Valider un membre de l'association",
				body: (
					<div>
						<p>
							Souhaitez-vous valider le poste de ce membre dans l'association{' '}
							<span className="font-italic">{asso.name}</span> ?
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
									`Vous avez validé avec succès le membre de cette association: ${asso.name}`,
									"Valider un membre d'une association"
								);

								if (user.id === member_id) {
									dispatch(actions.user.assos.all());
									dispatch(actions.user.permissions.all());
								}
							})
							.catch(() => {
								NotificationManager.error(
									`Vous n'avez pas le droit de valider le membre de cette association: ${
										asso.name
									}`,
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
								dispatch(actions.user.assos.all());
								dispatch(actions.assos(asso.id).members.all());
								NotificationManager.warning(
									`Vous avez retiré avec succès le membre de cette association: ${asso.name}`,
									"Retirer un membre d'une association"
								);
							})
							.catch(() => {
								NotificationManager.error(
									`Vous n'avez pas le droit de retirer le membre de cette association: ${
										asso.name
									}`,
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
		const { events, modal } = this.state;

		if (failed) return <Http404 />;

		if (fetching || !fetched || !asso) return <span className="loader huge active" />;

		if (member) {
			const { pivot } = member;

			this.user = {
				isFollowing: true,
				isMember: pivot.role_id !== null && pivot.validated_by !== null,
				isWaiting: pivot.role_id !== null && pivot.validated_by === null,
			};
		} else {
			this.user = {
				isFollowing: false,
				isMember: false,
				isWaiting: false,
			};
		}

		let bg = `bg-${asso.login}`;

		if (asso.parent) bg += ` bg-${asso.parent.login}`;

		let joinFromMemberList;
		if (Object.values(this.user).every(value => !value)) {
			joinFromMemberList = this.joinAsso.bind(this);
		}

		return (
			<div className="asso w-100">
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
						<Button className="font-weight-bold" outline color={modal.button.type} onClick={modal.button.onClick}>
							{modal.button.text}
						</Button>
					</ModalFooter>
				</Modal>

				<ul className={`nav nav-tabs asso ${bg}`}>
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
					<li className="nav-item">
						<NavLink className="nav-link" activeClassName="active" to={`${match.url}/events`}>
							ÉVÈNEMENTS
						</NavLink>
					</li>
					{user && (
						<li className="nav-item">
							<NavLink className="nav-link" activeClassName="active" to={`${match.url}/members`}>
								TROMBINOSCOPE
							</NavLink>
						</li>
					)}
					{this.user.isMember && (
						<li className="nav-item">
							<NavLink className="nav-link" activeClassName="active" to={`${match.url}/access`}>
								ACCES
							</NavLink>
						</li>
					)}
					{this.user.isMember && (
						<li className="nav-item dropdown">
							<Dropdown title="CRÉER">
								<Link className="dropdown-item" to={`${match.url}/article`}>
									Article
								</Link>
								<Link className="dropdown-item" to={`${match.url}/evenement`}>
									Évènement
								</Link>
							</Dropdown>
						</li>
					)}
				</ul>

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
								follow={this.followAsso.bind(this)}
								unfollow={this.unfollowAsso.bind(this)}
								join={this.joinAsso.bind(this)}
								leave={this.leaveAsso.bind(this)}
							/>
						)}
					/>
					<Route path={`${match.url}/events`} render={() => <AssoCalendar asso={asso} />} />
					<Route path={`${match.url}/articles`} render={() => <ArticleList asso={asso} />} />
					<LoggedRoute
						path={`${match.url}/members`}
						redirect={`${match.url}`}
						types={['casConfirmed', 'contributorBde']}
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
						path={`${match.url}/access`}
						redirect={`${match.url}`}
						isAllowed={() => {
							return this.user.isMember;
						}}
						render={() => <AccessScreen asso={asso} />}
					/>
					<Route
						path={`${match.url}/article`}
						render={() => (
							<ArticleForm
								post={this.postArticle.bind(this)}
								events={AssoScreen.getAllEvents(events)}
							/>
						)}
					/>
				</Switch>
			</div>
		);
	}
}

export default AssoScreen;
