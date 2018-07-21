import React, { Component } from 'react';
import { NavLink, Route, Switch } from 'react-router-dom';
import { connect } from 'react-redux';
import loggedUserActions from '../redux/custom/loggedUser/actions';

// Profile Components
import UserInfo from '../components/profile/UserInfo';
import AssociativeCarreer from '../components/profile/AssociativeCarreer';

@connect(store => ({
	user: store.loggedUser.data,
}))
class ProfileScreen extends Component {
	componentWillMount() {
		this.props.dispatch(loggedUserActions.getInfo())
	}

	load(name) {
		let action = null;
		switch (name) {
			case 'info':
				action = loggedUserActions.getInfo();
			case 'details':
				action = loggedUserActions.getDetails();
		}
		if (action != null)
			this.props.dispatch(action)
	}

	render() {
		const { match, user } = this.props;
		return (
			<div className="container">
				<h1 className="title">Mon profil</h1>
				<ul className="nav nav-tabs">
					<li className="nav-item">
						<NavLink className="nav-link" activeClassName="active" exact to={`${match.url}`}>Informations</NavLink>
					</li>
					<li className="nav-item">
						<NavLink className="nav-link" activeClassName="active" to={`${match.url}/parcours_associatif`}>Parcours Associatif</NavLink>
					</li>
				</ul>
				<div className="container">
					<Switch>
						<Route path={`${match.url}`} exact render={
							() => <UserInfo info={ user.info } details={ user.details } missing={this.load.bind(this)} />
						} />
						<Route path={`${match.url}/parcours_associatif`} render={
							() => <AssociativeCarreer />
						} />
					</Switch>
				</div>
			</div>
		);
	}
}

export default ProfileScreen;