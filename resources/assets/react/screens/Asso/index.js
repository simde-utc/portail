import React from 'react';
import { connect } from 'react-redux';
import { assosActions, articlesActions } from '../../redux/actions';
import loggedUserActions from '../../redux/custom/loggedUser/actions';
import { NavLink, Redirect, Link, Route, Switch } from 'react-router-dom';

import Dropdown from './../../components/Dropdown.js';
import ArticleForm from './../../components/Article/Form.js';

import ScreensAssoHome from './Home.js';
import ScreensAssoArticles from './Articles.js';

/* TODO: Make it stateless & unconnected */
/* TODO: Add notifications for article create, copy Erode project */
@connect((store, props) => ({
	asso: store.assos.data.find( asso => asso.login == props.match.params.login ),
	fetching: store.assos.fetching,
	fetched: store.assos.fetched,
	user: store.loggedUser.data,
	articles: store.articles
}))
class AssoScreen extends React.Component { 
	constructor() {
		super();

		this.state = {
			redirect: false
		};
	}

	componentWillMount() {
		const login = this.props.match.params.login
		this.props.dispatch(assosActions.getOne(login));
		this.props.dispatch(loggedUserActions.getAssos());
	}

	postArticle(data) {
		data.owned_by_type = "user";
		data.owned_by_id = this.props.asso.id;
		this.props.dispatch(articlesActions.create(data));
		console.log(this.props.articles.error.response.data);
		this.props.navigate('/')
		// this.setState({redirect: true});
	}

	render() {
		// if (this.state.redirect) {
		//     return <Redirect to="/" />;
		// }

		// var createArticleButton = <span></span>;
		// if (this.props.user.assos && this.props.user.assos.find( assos => assos.id === this.props.asso.id ))

		if (this.props.fetching || !this.props.fetched || !this.props.asso)
			return (<span className="loader huge active"></span>);

		let actions = [];
		if (this.props.asso.user) {
			if (this.props.asso.user.is_follower)
				actions.push(<button className="my-1 btn btn-outline-warning">Se désabonner</button>)
			else
				actions.push(<button className="my-1 btn btn-success">S'abonner</button>)
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
							<ScreensAssoHome asso={ this.props.asso } />
						)} />
					<Route path={`${this.props.match.url}/articles`} render={ () => ( 
							<ScreensAssoArticles />
						)} />
					<Route path={`${this.props.match.url}/creer/article`} render={ () => ( 
							<ArticleForm post={this.postArticle.bind(this)} />
						)} />
				</Switch>
			</div>
		);
	}
};

export default AssoScreen;
