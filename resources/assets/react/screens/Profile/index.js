/**
 * Display of a user profile.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Corentin Mercier <corentin@cmercier.fr>
 *
 * @copyright Copyright (c) 2019, SiMDE-UTC
 * @license GNU GPL-3.0
 */

import React from 'react';
import { NavLink, Route, Switch } from 'react-router-dom';
import { connect } from 'react-redux';

import AssociativeCareerScreen from './AssociativeCareer';

import actions from '../../redux/actions';

@connect(store => ({
	user: store.getData('user', false),
	permissions: store.getData('user/permissions'),
}))
class ScreenProfile extends React.Component {
	componentWillMount() {
		const { dispatch } = this.props;

		dispatch(actions.config({ title: 'Mon profil' }));
	}

	render() {
		const { user, permissions, match } = this.props;

		return (
			<div className="nav-container w-100">
				<ul className="nav nav-tabs">
					<li className="nav-item">
						<NavLink className="nav-link" activeClassName="active" exact to={match.url}>
							MES INFORMATIONS
						</NavLink>
					</li>
					<li className="nav-item">
						<NavLink className="nav-link" activeClassName="active" to={`${match.url}/career`}>
							MON PARCOURS
						</NavLink>
					</li>
					<li className="nav-item">
						<NavLink className="nav-link" activeClassName="active" to={`${match.url}/apps`}>
							MES APPLICATIONS
						</NavLink>
					</li>
					<li className="nav-item">
						<NavLink
							className="nav-link"
							activeClassName="active"
							to={`${match.url}/contributions`}
						>
							MES COTISATIONS
						</NavLink>
					</li>
					{user && permissions.length ? (
						<li className="nav-item">
							<a className="nav-link admin" href="/admin">
								INTERFACE ADMIN
							</a>
						</li>
					) : null}
					<li className="nav-item">
						<a className="nav-link admin" href="/logout">
							ME DECONNECTER
						</a>
					</li>
				</ul>
				<Switch>
					<Route
						path={`${match.url}/career`}
						exact
						render={() => <AssociativeCareerScreen user={user} />}
					/>
				</Switch>
			</div>
		);
	}
}

export default ScreenProfile;
