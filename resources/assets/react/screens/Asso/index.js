import React from 'react';
import { connect } from 'react-redux';
import actions from '../../redux/actions';
import { NavLink, Redirect, Link, Route, Switch } from 'react-router-dom';
import { findIndex } from 'lodash';
import { NotificationManager } from 'react-notifications';
import { Modal, ModalHeader, ModalBody, ModalFooter, Button } from 'reactstrap';
import Select from 'react-select';

import Dropdown from './../../components/Dropdown';
import ArticleForm from './../../components/Article/Form';
import LoggedRoute from './../../routes/Logged';
import Http404 from './../../routes/Http404';

import AssoHomeScreen from './Home';
import ArticleList from './ArticleList';
import AssoMemberListScreen from './MemberList';

import Calendar from '../../components/Calendar/index';

@connect((store, props) => {
	const login = props.match.params.login;
	const asso = store.getData(['assos', login]);

	return {
		user: store.getData('user', false),
		asso: asso,
		member: store.findData(['user', 'assos'], login, 'login', false),
		roles: store.getData(['assos', asso.id, 'roles']),
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
			}
		};
	}

	componentWillReceiveProps(props) {
		if (this.props.match.params.login !== props.match.params.login) {
			this.loadAssosData(props.match.params.login);
		}
	}

	componentWillMount() {
		this.loadAssosData(this.props.match.params.login);
	}

	loadAssosData(login) {
		// On doit d'abord récupérer l'asso id:
		var action = actions.assos.find(login);

		this.props.dispatch(action);

		action.payload.then(() => {
			this.props.dispatch(actions.definePath(['assos', this.props.asso.id, 'roles']).roles.all({ owner: 'asso,' + this.props.asso.id }));
		});
	}

	getAllEvents(events) {
		var allEvents = [];

		for (var calendar_id in events)
			for (var id in events[calendar_id])
				allEvents.push(events[calendar_id][id]);

		return allEvents;
	}

	followAsso() {
		this.setState(prevState => ({
			...prevState,
			modal: {
				show: true,
				title: 'Suivre une association',
				body:	<p>Souhaitez-vous vraiment suivre l'association <span className="font-italic">{ this.props.asso.name }</span> ?</p>,
				button: {
					type: 'success',
					text: 'Suivre',
					onClick: () => {
						actions.user.assos.create({}, {
							asso_id: this.props.asso.id,
						}).payload.then(() => {
							this.props.dispatch(actions.user.assos.all())
							this.props.dispatch(actions.assos(this.props.asso.id).members.all());
							NotificationManager.success('Vous suivez maintenant l\'association: ' + this.props.asso.name, 'Suivre une association')
						}).catch(() => {
							NotificationManager.error('Vous n\'avez pas le droit de suivre cette association: ' + this.props.asso.name, 'Suivre une association')
						}).finally(() => {
							this.setState(prevState => ({ ...prevState, modal: { ...prevState.modal, show: false } }));
						});
					}
				}
			}
		}));
	}

	unfollowAsso() {
		this.setState(prevState => ({
			...prevState,
			modal: {
				show: true,
				title: 'Ne plus suivre une association',
				body:	<p>Souhaitez-vous vraiment ne plus suivre l'association <span className="font-italic">{ this.props.asso.name }</span> ?</p>,
				button: {
					type: 'danger',
					text: 'Ne plus suivre',
					onClick: () => {
						actions.user.assos.remove(
							this.props.asso.id
						).payload.then(() => {
							this.props.dispatch(actions.user.assos.all())
							this.props.dispatch(actions.assos(this.props.asso.id).members.all());
							NotificationManager.warning('Vous ne suivez plus l\'association: ' + this.props.asso.name, 'Suivre une association')
						}).finally(() => {
							this.setState(prevState => ({ ...prevState, modal: { ...prevState.modal, show: false }}));
						});
					}
				}
			}
		}));
	}

	joinAsso() {
		this.setState(prevState => ({
			...prevState,
			role_id: undefined,
			modal: {
				show: true,
				title: 'Rejoindre une association',
				body: (
					<div>
						<p>Souhaitez-vous rejoindre l'association <span className="font-italic">{ this.props.asso.name }</span> ?</p>
						<p>Pour cela, il faut que vous renseignez votre rôle et qu'un membre autorisé valide.</p>
						<Select
							onChange={(role) => { this.setState(prevState => ({ ...prevState, role_id: role.value })); }}
							name="role_id"
							placeholder="Rôle dans cette association"
							options={ this.props.roles.map(role => ({
								value: role.id,
								label: role.name + ' - ' + role.description,
							})) }
						/>
					</div>
				),
				button: {
					type: 'success',
					text: 'Rejoindre l\'association',
					onClick: () => {
						if (!this.state.role_id)
							return;

						actions.assos(this.props.asso.id).members.create({}, {
							user_id: this.props.user.id,
							role_id: this.state.role_id
						}).payload.then(() => {
							this.props.dispatch(actions.user.assos.all())
							this.props.dispatch(actions.assos(this.props.asso.id).members.all());
							NotificationManager.success('Vous avez demandé à rejoindre l\'association: ' + this.props.asso.name, 'Devenir membre d\'une association')
						}).catch(() => {
							NotificationManager.error('Vous ne pouvez pas devenir membre de cette association: ' + this.props.asso.name, 'Devenir membre d\'une association')
						}).finally(() => {
							this.setState(prevState => ({ ...prevState, modal: { ...prevState.modal, show: false }}));
						});
					}
				}
			}
		}));
	}

	leaveAsso(isWaiting) {
		this.setState(prevState => ({
			...prevState,
			modal: {
				show: true,
				title: 'Quitter une association',
				body: (
					<div>
						<p>Souhaitez-vous vraiment quitter l'association <span className="font-italic">{ this.props.asso.name }</span> ?</p>
						<p>{ isWaiting ? 'Votre demande est encore en attente de validation.' : 'En faisant ça, vous perdrez votre rôle dans cette association et un email sera envoyé pour notifier l\'association de ce changement' }</p>
					</div>
				),
				button: {
					type: 'danger',
					text: 'Quitter l\'association',
					onClick: () => {
						actions.assos(this.props.asso.id).members.remove(
							this.props.user.id
						).payload.then(() => {
							this.props.dispatch(actions.user.assos.all())
							this.props.dispatch(actions.assos(this.props.asso.id).members.all());
							NotificationManager.warning('Vous ne faites plus partie de l\'association: ' + this.props.asso.name, 'Quitter une association')
						}).catch(() => {
							NotificationManager.error('Une erreur a été rencontrée lorsque vous avez voulu quitter cette association: ' + this.props.asso.name, 'Quitter une association')
						}).finally(() => {
							this.setState(prevState => ({ ...prevState, modal: { ...prevState.modal, show: false }}));
						});
					}
				}
			}
		}));
	}

	validateMember(member_id) {
		this.setState(prevState => ({
			...prevState,
			modal: {
				show: true,
				title: 'Valider un membre de l\'association',
				body: (
					<div>
						<p>Souhaitez-vous valider le poste de ce membre dans l'association <span className="font-italic">{ this.props.asso.name }</span> ?</p>
					</div>
				),
				button: {
					type: 'success',
					text: 'Valider le membre',
					onClick: () => {
						actions.assos(this.props.asso.id).members.update(
							member_id
						).payload.then(() => {
							this.props.dispatch(actions.user.assos.all())
							this.props.dispatch(actions.assos(this.props.asso.id).members.all());
							NotificationManager.success('Vous avez validé avec succès le membre de cette association: ' + this.props.asso.name, 'Valider un membre d\'une association')
						}).catch(() => {
							NotificationManager.error('Vous n\'avez pas le droit de valider le membre de cette association: ' + this.props.asso.name, 'Valider un membre d\'une association')
						}).finally(() => {
							this.setState(prevState => ({ ...prevState, modal: { ...prevState.modal, show: false }}));
						});
					}
				}
			}
		}));
	}

	leaveMember(member_id) {
		this.setState(prevState => ({
			...prevState,
			modal: {
				show: true,
				title: 'Retirer le membre de l\'association',
				body: (
					<div>
						<p>Souhaitez-vous vraiment retirer ce membre de l'association <span className="font-italic">{ this.props.asso.name }</span> ?</p>
					</div>
				),
				button: {
					type: 'danger',
					text: 'Retirer le membre',
					onClick: () => {
						actions.assos(this.props.asso.id).members.remove(
							member_id
						).payload.then(() => {
							this.props.dispatch(actions.user.assos.all())
							this.props.dispatch(actions.assos(this.props.asso.id).members.all());
							NotificationManager.warning('Vous avez retiré avec succès le membre de cette association: ' + this.props.asso.name, 'Retirer un membre d\'une association')
						}).catch(() => {
							NotificationManager.error('Vous n\'avez pas le droit de retirer le membre de cette association: ' + this.props.asso.name, 'Retirer un membre d\'une association')
						}).finally(() => {
							this.setState(prevState => ({ ...prevState, modal: { ...prevState.modal, show: false }}));
						});
					}
				}
			}
		}));
	}

	postArticle(data) {
		data.owned_by_type = "asso";
		data.owned_by_id = this.props.asso.id;
		this.props.dispatch(articlesActions.create(data));
	}

	render() {
		if (this.props.failed)
			return <Http404 />;

		if (this.props.fetching || !this.props.fetched || !this.props.asso)
			return (<span className="loader huge active"></span>);

		if (this.props.member) {
			let pivot = this.props.member.pivot;

			this.user = {
				isFollowing: true,
				isMember: pivot.role_id !== null,
				isWaiting: pivot.validated_by === null,
			};
		}
		else {
			this.user = {
				isFollowing: false,
				isMember: false,
				isWaiting: false,
			};
		}

		var bg = 'bg-' + this.props.asso.login;

		if (this.props.asso.parent)
			bg += ' bg-' + this.props.asso.parent.login;

		return (
			<div className="asso w-100">
				<Modal isOpen={ this.state.modal.show }>
					<ModalHeader>{ this.state.modal.title }</ModalHeader>
					<ModalBody>
						{ this.state.modal.body }
					</ModalBody>
					<ModalFooter>
						<Button outline onClick={() => { this.setState(prevState => ({ ...prevState, modal: { ...prevState.modal, show: false }})); } }>Annuler</Button>
						<Button outline color={ this.state.modal.button.type } onClick={ this.state.modal.button.onClick }>{ this.state.modal.button.text }</Button>
					</ModalFooter>
        		</Modal>

				<ul className={ "nav nav-tabs asso " + bg }>
					<li className="nav-item">
						<NavLink className="nav-link" activeClassName="active" exact to={`${this.props.match.url}`}>DESCRIPTION</NavLink>
					</li>
					<li className="nav-item">
						<NavLink className="nav-link" activeClassName="active" to={`${this.props.match.url}/articles`}>ARTICLES</NavLink>
					</li>
					<li className="nav-item">
						<NavLink className="nav-link" activeClassName="active" to={`${this.props.match.url}/evenements`}>ÉVÈNEMENTS</NavLink>
					</li>
					{ this.props.user && (
						<li className="nav-item">
							<NavLink className="nav-link" activeClassName="active" to={`${this.props.match.url}/members`}>TROMBINOSCOPE</NavLink>
						</li>
					)}
					<li className="nav-item dropdown">
						<Dropdown title="CRÉER">
							<Link className="dropdown-item" to={`${this.props.match.url}/article`}>Article</Link>
							<Link className="dropdown-item" to={`${this.props.match.url}/evenement`}>Évènement</Link>
						</Dropdown>
					</li>
				</ul>

				<Switch>
					<Route path={`${this.props.match.url}`} exact render={ () => (
						<AssoHomeScreen asso={ this.props.asso } contacts={ this.props.contacts } userIsFollowing={ this.user.isFollowing } userIsMember={ this.user.isMember } userIsWaiting={ this.user.isWaiting }
							follow={ this.followAsso.bind(this) } unfollow={ this.unfollowAsso.bind(this) } join={ this.joinAsso.bind(this) } leave={ this.leaveAsso.bind(this) } />
					)} />
					<Route path={`${this.props.match.url}/evenements`} render={ () => (
						<Calendar events={ this.getAllEvents(this.state.events) } fetched={ this.state.articlesFetched } />
					)} />
					<Route path={`${this.props.match.url}/articles`} render={ () => (
						<ArticleList asso={ this.props.asso } />
					)} />
					<LoggedRoute path={`${this.props.match.url}/members`} redirect={`${this.props.match.url}`} types={[ 'casConfirmed', 'contributorBde' ]} render={ () => (
						<AssoMemberListScreen asso={ this.props.asso } isMember={ this.user.isMember } leaveMember={(id) => { this.leaveMember(id) }} validateMember={(id) => { this.validateMember(id) }}/>
					)} />
					<Route path={`${this.props.match.url}/article`} render={ () => (
						<ArticleForm post={ this.postArticle.bind(this) } events={ this.getAllEvents(this.state.events) } />
					)} />
				</Switch>
			</div>
		);
	}
};

export default AssoScreen;
