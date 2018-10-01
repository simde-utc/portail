/**
 * Affichage de la navbar permettant de naviguer facilement
 *
 * @author Alexandre Brasseur <alexandre.brasseur@etu.utc.fr>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license AGPL-3.0
**/

import React from 'react';
import { connect } from 'react-redux';
import { withRouter, NavLink } from 'react-router-dom';
import { orderBy } from 'lodash';

@connect((store, props) => ({
  isAuthenticated: store.isFetched('user'), // Si user === false, alors on est pas co
  assos: store.getData('user/assos'),
  services: store.getData('user/services'),
}))
class Sidebar extends React.Component {
    getAssos(assos) {
        return assos.map(asso => {
            let color = 'color-' + asso.login;

            if (asso.parent)
                color += ' color-' + asso.parent.login;

            return (
                <NavLink key={ asso.id } className="sidebar-link" to={ "/assos/" + asso.login }>
                    <i className={ asso.pivot.role_id ? 'fas fa-hands-helping' : 'fas fa-thumbs-up' }></i>
                    <span className={ color }>{ asso.shortname }</span>
                </NavLink>
            )
        });
    }

    getServices(services) {
        return orderBy(services, 'name').map(service => (
            <a key={ service.id } className="sidebar-link" href={ service.url }>
                <i className="fas fa-concierge-bell"></i>
                <span>{ service.shortname }</span>
            </a>
        ));
    }

	render() {
		return (
			<div className="sidebar col-md-3 col-xl-2 d-none d-md-flex flex-column justify-content-between">
				<div className="sidebar-inner">
					<div className="sidebar-group">
						<h6 className="sidebar-header d-hover-zone">
							ACTUALITÉS <NavLink className="float-right d-hover fas fa-cog" to="/settings/sidebar/news" />
						</h6>
						<NavLink exact className="sidebar-link" to="/"><i className="fas fa-newspaper"></i>Flux</NavLink>
					</div>

					<div className="sidebar-group">
						<h6 className="sidebar-header d-hover-zone">
							LIENS UTC <NavLink className="float-right d-hover fas fa-cog" to="/settings/sidebar/utc" />
						</h6>
						<a className="sidebar-link" target="_blank" href="https://ent.utc.fr">
							<i className="fas fa-school"></i>ENT UTC</a>
						<a className="sidebar-link" target="_blank" href="https://webmail.utc.fr">
							<i className="fas fa-paper-plane"></i>Webmail</a>
						<a className="sidebar-link" target="_blank" href="https://moodle.utc.fr/login/index.php?authCAS=CAS">
							<i className="fas fa-book"></i>Moodle</a>
					</div>

					<div className="sidebar-group">
						<h6 className="sidebar-header d-hover-zone">
							RACCOURCIS <NavLink className="float-right d-hover fas fa-cog" to="/settings/sidebar/shortcuts" />
						</h6>
						<NavLink className="sidebar-link" to="/evenements"><i className="fas fa-calendar-alt"></i>Évènements</NavLink>
                        <NavLink className="sidebar-link" to="/services"><i className="fas fa-concierge-bell"></i>Services</NavLink>
						<NavLink className="sidebar-link" to="/assos"><i className="fas fa-hands-helping"></i>Associations</NavLink>
						<NavLink className="sidebar-link" to="/groupes"><i className="fas fa-users"></i>Groupes</NavLink>
					</div>

                    { this.props.isAuthenticated && this.props.services.length > 0 && (
                        <div className="sidebar-group">
                            <h6 className="sidebar-header d-hover-zone">
                                MES SERVICES <NavLink className="float-right d-hover fas fa-cog" to="/settings/sidebar/services" />
                            </h6>
                            { this.getServices(this.props.services) }
                        </div>
                    )}

                    { this.props.isAuthenticated && this.props.assos.length > 0 && (
                        <div className="sidebar-group">
                            <h6 className="sidebar-header d-hover-zone">
                                MES ASSOCIATIONS <NavLink className="float-right d-hover fas fa-cog" to="/settings/sidebar/assos" />
                            </h6>
                            { this.getAssos(this.props.assos) }
                        </div>
                    )}
                </div>
				<p className="sidebar-footer small">&lt;&#47;&gt; avec le sang par le SiMDE</p>
			</div>
		);
	}
}

export default withRouter(Sidebar);
