import React, { Component } from 'react';
import { connect } from 'react-redux';
import { assosActions } from '../redux/actions';
import loggedUserActions from '../redux/custom/loggedUser/actions';
import { NavLink, Route, Switch } from 'react-router-dom';

/* TODO: Make it stateless & unconnected */
@connect((store, props) => ({
	asso: store.assos.data.find( asso => asso.login == props.match.params.login ),
	fetching: store.assos.fetching,
	fetched: store.assos.fetched,
	user: store.loggedUser.data,
}))
class ScreensAssoDetail extends Component { 
	componentWillMount() {
		const login = this.props.match.params.login
		this.props.dispatch(assosActions.getOne(login));
		this.props.dispatch(loggedUserActions.getAssos());
	}

	render() {
		console.log(this.props.user);

		// var createArticleButton = <span></span>;
		// if (this.props.user.assos && this.props.user.assos.find( assos => assos.id === this.props.asso.id ))

		if (this.props.fetching || !this.props.fetched)
			return (<span className="loader huge active"></span>);

		let actions = [];
		if (this.props.asso.user) {
			if (this.props.asso.user.is_follower)
				actions.push(<button key="subscription" 
					className="my-1 btn btn-outline-warning">Se désabonner</button>)
			else
				actions.push(<button key="subscription" 
					className="my-1 btn btn-success">S'abonner</button>)
		}

		return (
			<div className="container">
				<h1 className="title mb-2">{ this.props.asso.shortname }</h1>
				<span className="d-block text-muted mb-4">{ this.props.asso.name }</span>

				{ actions.length > 0 && <div className="my-1 d-flex">{ actions }</div> }

				<span>{ this.props.asso.type.description }</span>
				<p className="my-3">{ this.props.asso.description }</p>

				<ul className="nav nav-tabs">
					<li className="nav-item">
						<NavLink className="nav-link" activeClassName="active" exact to={`${this.props.match.url}`}>Informations</NavLink>
					</li>
					<li className="nav-item">
						<NavLink className="nav-link" activeClassName="active" to={`${this.props.match.url}/parcours_associatif`}>Parcours Associatif</NavLink>
					</li>
				</ul>
				<div className="container">
					<Switch>
						<Route path={`${this.props.match.url}`} exact render={
							() => <div></div>
						} />
						<Route path={`${this.props.match.url}/parcours_associatif`} render={
							() => <div></div>
						} />
					</Switch>
				</div>
			</div>
		);
	}
};

export default ScreensAssoDetail;
