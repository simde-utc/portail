import React from 'react';
import { connect } from 'react-redux';
import { assosActions, rolesActions, contactsActions, articlesActions, assoMembersActions, calendarsActions, calendarEventsActions } from '../../redux/actions';
import loggedUserActions from '../../redux/custom/loggedUser/actions';
import { NavLink, Redirect, Link, Route, Switch } from 'react-router-dom';
import { findIndex } from 'lodash';
import { NotificationContainer, NotificationManager } from 'react-notifications';
import { Modal, ModalHeader, ModalBody, ModalFooter, Button } from 'reactstrap';
import Select from 'react-select';

import Dropdown from './../../components/Dropdown.js';
import ArticleForm from './../../components/Article/Form.js';

import ScreensAssoHome from './Home.js';
import ArticleList from '../../components/Article/List.js';

import Calendar from '../../components/Calendar/index.js';

/* TODO: Make it stateless & unconnected */
/* TODO: Add notifications for article create, copy Erode project */
@connect((store, props) => ({
	asso: store.assos.data.find(asso => asso.login == props.match.params.login),
	members: store.assoMembers.data,
	fetching: store.assos.fetching,
	fetched: store.assos.fetched,
	user: store.loggedUser.data
}))
class AssoScreen extends React.Component {
	constructor(props) {
		super(props);

		this.state = {
			redirect: false,
			dataRequested: false,
			roles: [],
			rolesFetched: false,
			contacts: [],
			contactsFetched: false,
			articles: [],
			articlesFetched: false,
			calendars: [],
			calendarsFetched: false,
			events: {},
			eventsFetched: false,
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
		if (props.asso) {
			if (!this.state.dataRequested) {
				this.setState(prevState => ({ ...prevState, dataRequested: true }));

				rolesActions.getAll({ owner: 'asso,' + props.asso.id }).payload.then(res => {
					this.setState(prevState => ({ ...prevState, rolesFetched: true, roles: res.data }));
				});

				contactsActions.setUriParams({ resource: 'assos', resource_id: props.asso.id }).getAll().payload.then(res => {
					this.setState(prevState => ({ ...prevState, contactsFetched: true, contacts: res.data }));
				});

				articlesActions.getAll({ owner: 'asso,' + props.asso.id }).payload.then(res => {
					this.setState(prevState => ({ ...prevState, articlesFetched: true, articles: res.data }));
				});

				calendarsActions.getAll({ owner: 'asso,' + props.asso.id }).payload.then(res => {
					var calendars = res.data
					this.setState(prevState => ({ ...prevState, calendarsFetched: true, calendars: calendars }));

					calendars.forEach(calendar => {
						calendarEventsActions.setUriParams({ calendar_id: calendar.id }).getAll().payload.then(res => {
							this.setState(prevState => {
								prevState.events[calendar.id] = res.data;

								if (Object.keys(prevState.events).length === prevState.calendars.length)
								prevState.eventsFetched = true;

								return prevState;
							});
						}).catch(res => {
							prevState.events[calendar.id] = [];

							if (Object.keys(prevState.events).length === prevState.calendars.length)
							prevState.eventsFetched = true;

							return prevState;
						});
					})
				}).catch(res => {
					this.setState(prevState => ({ ...prevState, calendarsFetched: true }));
				});
			}
		}
		else {
			this.setState(prevState => ({ ...prevState, dataRequested: false }));
			this.componentWillMount();
		}
	}

	componentWillMount() {
		const login = this.props.match.params.login
		this.props.dispatch(assosActions.getOne(login));
		this.props.dispatch(assoMembersActions.setUriParams({ asso_id: login }).getAll());
	}

	getAllEvents(events) {
		var allEvents = [];

		for (var calendar_id in events)
			for (var id in events[calendar_id])
				allEvents.push(events[calendar_id][id]);

		return allEvents;
	}

	getAllMembers(members) {
		return members.filter(member => member.pivot.validated_by)
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
						assoMembersActions.setUriParams({ asso_id: this.props.asso.id }).create({
							user_id: this.props.user.info.id,
						}).payload.then(() => {
							this.props.dispatch(loggedUserActions.getAssos())
							NotificationManager.success('Vous suivez maintenant l\'association: ' + this.props.asso.name, 'Suivre une association')
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
						assoMembersActions.setUriParams({ asso_id: this.props.asso.id }).remove(
							this.props.user.info.id
						).payload.then(() => {
							this.props.dispatch(loggedUserActions.getAssos())
							NotificationManager.warning('Vous ne suivez plus l\'association: ' + this.props.asso.name, 'Suivre une association')
						}).finally(() => {
							this.setState(prevState => ({ ...prevState, modal: { ...prevState.modal, show: false }}));
						});
					}
				}
			}
		}));
	}

	joinAsso(role_id) {
		this.setState(prevState => ({
			...prevState,
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
							options={ this.state.roles.map(role => ({
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
						assoMembersActions.setUriParams({ asso_id: this.props.asso.id }).create({
							user_id: this.props.user.info.id,
							role_id: this.state.role_id
						}).payload.then(() => {
							this.props.dispatch(loggedUserActions.getAssos())
							NotificationManager.success('Vous avez demandé à rejoindre l\'association: ' + this.props.asso.name, 'Devenir membre d\'une association')
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
						assoMembersActions.setUriParams({ asso_id: this.props.asso.id }).remove(
							this.props.user.info.id
						).payload.then(() => {
							this.props.dispatch(loggedUserActions.getAssos())
							NotificationManager.warning('Vous ne faites plus partie de l\'association: ' + this.props.asso.name, 'Devenir membre d\'une association')
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
		this.setState(prev => ({ ...prev, redirect: true }));
	}

	render() {
		if (this.state.redirect)
			return <Redirect to="/" />;

		// var createArticleButton = <span></span>;
		// if (this.props.user.assos && this.props.user.assos.find( assos => assos.id === this.props.asso.id ))

		if (this.props.fetching || !this.props.fetched || !this.props.asso)
			return (<span className="loader huge active"></span>);

		let index = findIndex(this.props.user.assos, ['id', this.props.asso.id])
		if (index >= 0) {
			let pivot = this.props.user.assos[index].pivot;

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
					<li className="nav-item">
						<NavLink className="nav-link" activeClassName="active" to={`${this.props.match.url}/members`}>TROMBINOSCOPE</NavLink>
					</li>
					<li className="nav-item dropdown">
						<Dropdown title="CRÉER">
							<Link className="dropdown-item" to={`${this.props.match.url}/article`}>Article</Link>
							<Link className="dropdown-item" to={`${this.props.match.url}/evenement`}>Évènement</Link>
						</Dropdown>
					</li>
				</ul>

				<Switch>
					<Route path={`${this.props.match.url}`} exact render={ () => (
							<ScreensAssoHome asso={ this.props.asso } userIsFollowing={ this.user.isFollowing } userIsMember={ this.user.isMember } userIsWaiting={ this.user.isWaiting }
								follow={ this.followAsso.bind(this) } unfollow={ this.unfollowAsso.bind(this) } join={ this.joinAsso.bind(this) } leave={ this.leaveAsso.bind(this) } />
						)} />
					<Route path={`${this.props.match.url}/evenements`} render={ () => (
							<Calendar events={ this.getAllEvents(this.state.events) } fetched={ this.state.articlesFetched } />
						)} />
					<Route path={`${this.props.match.url}/articles`} render={ () => (
							<ArticleList articles={ this.state.articles } fetched={ this.state.articlesFetched } />
						)} />
					<Route path={`${this.props.match.url}/members`} render={ () => (
							<MemberList members={ this.getAllMembers(this.props.members) } roles={ this.state.roles } fetched={ this.state.membersFetched && this.state.rolesFetched } />
						)} />
					<Route path={`${this.props.match.url}/article`} render={ () => (
							<ArticleForm post={ this.postArticle.bind(this) } events={ this.getAllEvents(this.state.events) } />
						)} />
				</Switch>

				<NotificationContainer />
			</div>
		);
	}
};

export default AssoScreen;
