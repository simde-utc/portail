/**
 * Affichage du profile d'une personne.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019, SiMDE-UTC
 * @license GNU GPL-3.0
 */

import React from 'react';
import { NavLink } from 'react-router-dom';
import { connect } from 'react-redux';

import actions from '../redux/actions';

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
						<NavLink className="nav-link" activeClassName="active" to={`${match.url}/assos`}>
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

				<div className="container pr-3 pl-3">
					Le profil est en cours de développement. Il sera possible de consulter et modifier:
					<ul>
						<li>Vos informations personnelles</li>
						<li>Votre parcours associatif et étudiant</li>
						<li>Les applications/sites associatifs qui ont accès à vos données (et lequelles)</li>
						<li>Vos cotisations</li>
					</ul>
					{
						'Seul le bouton de déconnexion fonctionne. Nous vous invitons à glisser la onglets jusque cliquer sur "Me déconnecter" pour vous déconnecter :)'
					}
				</div>
			</div>
		);
	}
}

export default ScreenProfile;
