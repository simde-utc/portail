/**
 * Side bar display to navigate easily.
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

import actions from '../redux/actions';

@connect(store => ({
	config: store.config,
	user: store.getData('user'),
	permissions: store.getData('user/permissions'),
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

	closeSidebar(event) {
		if (
			['A', 'SPAN'].includes(event.target.tagName) ||
			event.target.classList.contains('sidebar-overlay')
		) {
			const { dispatch } = this.props;

			dispatch(actions.config({ openSidebar: false }));
		}
	}

	render() {
		const { isAuthenticated, config, user, permissions, assos, services } = this.props;

		return (
			<div
				className={config.openSidebar ? 'sidebar-container sidebar-active' : 'sidebar-container'}
				onClick={this.closeSidebar.bind(this)}
			>
				<div className="sidebar-overlay" />

				<div className="sidebar">
					<div className="sidebar-inner">
						<div className="sidebar-group sidebar-title">
							<NavLink to="/" className="sidebar-header d-hover-zone">
								Portail des assos
							</NavLink>
						</div>

						<div className="sidebar-group">
							<h6 className="sidebar-header d-hover-zone">
								ACTUALITÉS{' '}
								<NavLink
									className="float-right d-hover fas fa-cog"
									style={{ display: 'none' }}
									to="/settings/sidebar/news"
								/>
							</h6>
							<NavLink exact className="sidebar-link" to="/">
								<FontAwesomeIcon icon="newspaper" /> Flux
							</NavLink>
						</div>

						<div className="sidebar-group">
							<h6 className="sidebar-header d-hover-zone">
								LIENS UTILES{' '}
								<NavLink
									className="float-right d-hover fas fa-cog"
									style={{ display: 'none' }}
									to="/settings/sidebar/utc"
								/>
							</h6>
							{isAuthenticated && !user.types.contributorBde && (
								<a
									className="sidebar-link"
									target="_blank"
									rel="noopener noreferrer"
									href="https://assos.utc.fr/bde/bdecotiz"
								>
									<FontAwesomeIcon icon="money-bill" /> Cotiser au BDE-UTC
								</a>
							)}
							<a
								className="sidebar-link"
								target="_blank"
								rel="noopener noreferrer"
								href="https://ent.utc.fr"
							>
								<FontAwesomeIcon icon="school" /> Ent UTC
							</a>
							<a
								className="sidebar-link"
								target="_blank"
								rel="noopener noreferrer"
								href="https://webmail.utc.fr"
							>
								<FontAwesomeIcon icon="paper-plane" /> Webmail UTC
							</a>
							<a
								className="sidebar-link"
								target="_blank"
								rel="noopener noreferrer"
								href="https://moodle.utc.fr/login/index.php?authCAS=CAS"
							>
								<FontAwesomeIcon icon="book" /> Moodle
							</a>

							{user && permissions.length ? (
								<a className="sidebar-link" href="/admin">
									<FontAwesomeIcon icon="screwdriver" /> Interface admin
								</a>
							) : null}

							<a
								className="sidebar-link"
								target="_blank"
								rel="noopener noreferrer"
								href="https://github.com/simde-utc/portail/issues"
							>
								<FontAwesomeIcon icon="bug" /> Signaler un bug
							</a>
						</div>

						<div className="sidebar-group">
							<h6 className="sidebar-header d-hover-zone">
								RACCOURCIS{' '}
								<NavLink
									className="float-right d-hover fas fa-cog"
									style={{ display: 'none' }}
									to="/settings/sidebar/shortcuts"
								/>
							</h6>
							{isAuthenticated && user.types.contributorBde && (
								<NavLink className="sidebar-link" to="/bookings">
									<FontAwesomeIcon icon="person-booth" /> Réservations
								</NavLink>
							)}
							<NavLink className="sidebar-link" to="/services">
								<FontAwesomeIcon icon="concierge-bell" /> Services
							</NavLink>
							<NavLink className="sidebar-link" to="/assos">
								<FontAwesomeIcon icon="hands-helping" /> Associations
							</NavLink>
							<NavLink className="sidebar-link" to="/partners">
								<FontAwesomeIcon icon="handshake" /> Partenaires
							</NavLink>
							<NavLink className="sidebar-link" to="/groupes" style={{ display: 'none' }}>
								<FontAwesomeIcon icon="users" /> Groupes
							</NavLink>
						</div>

						{isAuthenticated && services.length > 0 && (
							<div className="sidebar-group">
								<h6 className="sidebar-header d-hover-zone">
									MES SERVICES FAVORIS{' '}
									<NavLink
										className="float-right d-hover fas fa-cog"
										style={{ display: 'none' }}
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
									<NavLink
										className="float-right d-hover fas fa-cog"
										to="/settings/sidebar/assos"
										style={{ display: 'none' }}
									/>
								</h6>
								{Sidebar.getAssos(assos)}
							</div>
						)}
					</div>

					<p className="sidebar-footer small pb-2">&lt;&#47;&gt; avec le sang par le SiMDE</p>
				</div>
			</div>
		);
	}
}

export default withRouter(Sidebar);
