/**
 * Liste les services
 *
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Natan Danous <natous.danous@hotmail.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

import React from 'react';
// import { NavLink, Route, Switch } from 'react-router-dom';
import { connect } from 'react-redux';
import actions from '../redux/actions';

// Profile Components
// import UserInfo from '../components/Profile/UserInfo';
// import AssociativeCarreer from '../components/Profile/AssociativeCarreer';

@connect(store => ({
	user: store.getData('user', false),
}))
class ScreensProfile extends React.Component {
	componentWillMount() {
		const { user, dispatch } = this.props;

		dispatch(actions.config({ title: `Profil - ${user.name}` }));
	}

	load(name) {
		const { dispatch } = this.props;

		switch (name) {
			case 'info':
				dispatch(actions.user.details.get());
				break;

			case 'details':
				dispatch(actions.user.details.get());
				break;

			default:
				break;
		}
	}

	render() {
		return <div />;

		// return (
		// 	<div className="container">
		// 		<h1 className="title">Mon profil</h1>
		// 		<ul className="nav nav-tabs">
		// 			<li className="nav-item">
		// 				<NavLink className="nav-link" activeClassName="active" exact to={`${match.url}`}>
		// 					Informations
		// 				</NavLink>
		// 			</li>
		// 			<li className="nav-item">
		// 				<NavLink
		// 					className="nav-link"
		// 					activeClassName="active"
		// 					to={`${match.url}/parcours_associatif`}
		// 				>
		// 					Parcours Associatif
		// 				</NavLink>
		// 			</li>
		// 		</ul>
		// 		<div className="container">
		// 			<Switch>
		// 				<Route
		// 					path={`${match.url}`}
		// 					exact
		// 					render={() => (
		// 						<UserInfo info={user.info} details={user.details} missing={this.load.bind(this)} />
		// 					)}
		// 				/>
		// 				<Route
		// 					path={`${match.url}/parcours_associatif`}
		// 					render={() => <AssociativeCarreer />}
		// 				/>
		// 			</Switch>
		// 		</div>
		// 	</div>
		// );
	}
}

export default ScreensProfile;
