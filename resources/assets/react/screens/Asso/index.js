import React from 'react';
import { connect } from 'react-redux';
import { assosActions, articlesActions, assoMembersActions, calendarsActions, calendarEventsActions } from '../../redux/actions';
import loggedUserActions from '../../redux/custom/loggedUser/actions';
import { NavLink, Redirect, Link, Route, Switch } from 'react-router-dom';
import { findIndex } from 'lodash';

import Dropdown from './../../components/Dropdown.js';
import ArticleForm from './../../components/Article/Form.js';

import ScreensAssoHome from './Home.js';
import ArticleList from '../../components/Article/List.js';

/* TODO: Make it stateless & unconnected */
/* TODO: Add notifications for article create, copy Erode project */
@connect((store, props) => ({
	asso: store.assos.data.find(asso => asso.login == props.match.params.login),
	assoMembers: store.assoMembers.data,
	fetching: store.assos.fetching,
	fetched: store.assos.fetched,
	user: store.loggedUser.data,
	articles: store.articles
}))
class AssoScreen extends React.Component {
	constructor(props) {
		super(props);

		this.state = {
			redirect: false,
			dataRequested: false,
			articles: [],
			articlesFetched: false,
			calendars: [],
			calendarsFetched: false,
			events: {},
			eventsFetched: false,
		};
	}

	componentWillReceiveProps(props) {
		if (props.asso && !this.state.dataRequested) {
			this.setState(prevState => ({ ...prevState, dataRequested: true }));

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

	componentWillMount() {
		const login = this.props.match.params.login
		this.props.dispatch(assosActions.getOne(login));
		this.props.dispatch(assoMembersActions.setUriParams({ asso_id: login }).getAll());
		this.props.dispatch(loggedUserActions.getAssos());
	}

	getAllEvents(events) {
		var allEvents = [];

		for (var calendar_id in events)
			for (var id in events[calendar_id])
				allEvents.push(events[calendar_id][id]);

		return allEvents;
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
				isMember: pivot.role_id !== undefined,
				isWaiting: pivot.validated_by === undefined,
			};
		}
		else {
			this.user = {
				isFollowing: false,
				isMember: false,
				isWaiting: false,
			};
		}

		const tabBarBg = this.props.asso.parent ? this.props.asso.parent.login : this.props.asso.login;

		return (
			<div className="asso w-100">

				<ul className={ "nav nav-tabs asso bg-" + tabBarBg }>
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
						<NavLink className="nav-link" activeClassName="active" to={`${this.props.match.url}/trombinoscope`}>TROMBINOSCOPE</NavLink>
					</li>
					<li className="nav-item dropdown">
						<Dropdown title="CRÉER">
							<Link className="dropdown-item" to={`${this.props.match.url}/creer/article`}>Article</Link>
							<Link className="dropdown-item" to={`${this.props.match.url}/creer/evenement`}>Évènement</Link>
						</Dropdown>
					</li>
				</ul>

				<Switch>
					<Route path={`${this.props.match.url}`} exact render={ () => (
							<ScreensAssoHome asso={ this.props.asso } userIsFollowing={ this.user.isFollowing } userIsMember={ this.user.isMember } userIsWaiting={ this.user.isWaiting } />
						)} />
					<Route path={`${this.props.match.url}/articles`} render={ () => (
							<ArticleList articles={ this.state.articles } fetched={ this.state.articlesFetched } />
						)} />
					<Route path={`${this.props.match.url}/creer/article`} render={ () => (
							<ArticleForm post={ this.postArticle.bind(this) } events={ this.getAllEvents(this.state.events) } />
						)} />
				</Switch>
			</div>
		);
	}
};

export default AssoScreen;
