/**
 * Affichage de la navbar permettant de naviguer facilement
 *
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Natan Danous <natous.danous@hotmail.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

import React from 'react';
import { connect } from 'react-redux';
import { withRouter, NavLink } from 'react-router-dom';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { orderBy } from 'lodash';

@connect(store => ({
	isAuthenticated: store.isFetched('user'),
	assos: store.getData('user/assos'),
	services: store.getData('user/services'),
}))
class Sidebar extends React.Component {
	static getAssos(assos) {
		return assos.map(asso => {
			let color = `color-${asso.login}`;

			if (asso.parent) color += ` color-${asso.parent.login}`;

			return (
				<NavLink key={asso.id} className="sidebar-link" to={`/assos/${asso.login}`}>
					<FontAwesomeIcon icon={asso.pivot.role_id ? 'hands-helping' : 'thumbs-up'} />{' '}
					<span className={color}>{asso.shortname}</span>
				</NavLink>
			);
		});
	}

	static getServices(services) {
		return orderBy(services, 'name').map(service => (
			<a key={service.id} className="sidebar-link" href={service.url}>
				<FontAwesomeIcon icon="concierge-bell" /> <span>{service.shortname}</span>
			</a>
		));
	}

	render() {
		const { isAuthenticated, assos, services } = this.props;

		return (
			<div className="sidebar col-md-3 col-xl-2 d-none d-md-flex flex-column justify-content-between">
				<div className="sidebar-inner">
					<div className="sidebar-group">
						<h6 className="sidebar-header d-hover-zone">
							ACTUALITÉS{' '}
							<NavLink className="float-right d-hover fas fa-cog" to="/settings/sidebar/news" />
						</h6>
						<NavLink exact className="sidebar-link" to="/">
							<FontAwesomeIcon icon="newspaper" /> Flux
						</NavLink>
					</div>

					<div className="sidebar-group">
						<h6 className="sidebar-header d-hover-zone">
							LIENS UTC{' '}
							<NavLink className="float-right d-hover fas fa-cog" to="/settings/sidebar/utc" />
						</h6>
						<a
							className="sidebar-link"
							target="_blank"
							rel="noopener noreferrer"
							href="https://ent.utc.fr"
						>
							<FontAwesomeIcon icon="school" /> ENT UTC
						</a>
						<a
							className="sidebar-link"
							target="_blank"
							rel="noopener noreferrer"
							href="https://webmail.utc.fr"
						>
							<FontAwesomeIcon icon="paper-plane" /> Webmail
						</a>
						<a
							className="sidebar-link"
							target="_blank"
							rel="noopener noreferrer"
							href="https://moodle.utc.fr/login/index.php?authCAS=CAS"
						>
							<FontAwesomeIcon icon="book" /> Moodle
						</a>
					</div>

					<div className="sidebar-group">
						<h6 className="sidebar-header d-hover-zone">
							RACCOURCIS{' '}
							<NavLink
								className="float-right d-hover fas fa-cog"
								to="/settings/sidebar/shortcuts"
							/>
						</h6>
						<NavLink className="sidebar-link" to="/evenements">
							<FontAwesomeIcon icon="calendar-alt" /> Évènements
						</NavLink>
						<NavLink className="sidebar-link" to="/services">
							<FontAwesomeIcon icon="concierge-bell" /> Services
						</NavLink>
						<NavLink className="sidebar-link" to="/assos">
							<FontAwesomeIcon icon="hands-helping" /> Associations
						</NavLink>
						<NavLink className="sidebar-link" to="/groupes">
							<FontAwesomeIcon icon="users" /> Groupes
						</NavLink>
					</div>

					{isAuthenticated && services.length > 0 && (
						<div className="sidebar-group">
							<h6 className="sidebar-header d-hover-zone">
								MES SERVICES{' '}
								<NavLink
									className="float-right d-hover fas fa-cog"
									to="/settings/sidebar/services"
								/>
							</h6>
							{Sidebar.getServices(services)}
						</div>
					)}

					{isAuthenticated && assos.length > 0 && (
						<div className="sidebar-group">
							<h6 className="sidebar-header d-hover-zone">
								MES ASSOCIATIONS{' '}
								<NavLink className="float-right d-hover fas fa-cog" to="/settings/sidebar/assos" />
							</h6>
							{Sidebar.getAssos(assos)}
						</div>
					)}
				</div>
				<p className="sidebar-footer small">&lt;&#47;&gt; avec le sang par le SiMDE</p>
			</div>
		);
	}
}

export default withRouter(Sidebar);
