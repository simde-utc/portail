import React, { Component } from 'react';
import { NavLink } from 'react-router-dom';


class Sidebar extends Component { 
	render() {
		return (
			<div className="sidebar col-md-3 col-xl-2 d-none d-md-flex flex-column justify-content-between">
				<div className="sidebar-inner">
					<div className="sidebar-group">
						<h6 className="sidebar-header d-hover-zone">
							ACTUALITÉS <NavLink className="float-right d-hover fas fa-cog" to="/settings/sidebar/news" />
						</h6>
						<NavLink className="sidebar-link" to="/"><i className="fas fa-newspaper"></i>Flux</NavLink>
						<NavLink className="sidebar-link" to="/news/utc"><i className="fas fa-newspaper"></i>Actualités UTC</NavLink>
						<NavLink className="sidebar-link" to="/news/assos"><i className="fas fa-newspaper"></i>Actualités Assos</NavLink>
					</div>

					<div className="sidebar-group">
						<h6 className="sidebar-header d-hover-zone">
							RACCOURCIS <NavLink className="float-right d-hover fas fa-cog" to="/settings/sidebar/shortcuts" />
						</h6>
						<a className="sidebar-link" target="_blank" href="https://ent.utc.fr">
							<i className="fas fa-school"></i>ENT UTC</a>
						<a className="sidebar-link" target="_blank" href="https://webmail.utc.fr">
							<i className="fas fa-paper-plane"></i>Webmail</a>
						<a className="sidebar-link" target="_blank" href="https://moodle.utc.fr/login/index.php?authCAS=CAS">
							<i className="fas fa-book"></i>Moodle</a>
						<NavLink className="sidebar-link" to="/evenements"><i className="fas fa-calendar-alt"></i>Évènements</NavLink>
						<NavLink className="sidebar-link" to="/assos"><i className="fas fa-hands-helping"></i>Associations</NavLink>
						<NavLink className="sidebar-link" to="/groupes"><i className="fas fa-users"></i>Groupes</NavLink>
					</div>

					<div className="sidebar-group">
						<h6 className="sidebar-header d-hover-zone">
							MES ASSOCIATIONS <NavLink className="float-right d-hover fas fa-cog" to="/settings/sidebar/assos" />
						</h6>
						<NavLink className="sidebar-link" to="/assos/picasso"><i className="fas fa-beer"></i>Pic'Asso</NavLink>
						<NavLink className="sidebar-link" to="/assos/simde"><i className="fas fa-code"></i>SiMDE</NavLink>
					</div>

					<div className="sidebar-group">
						<h6 className="sidebar-header d-hover-zone">
							MES GROUPES <NavLink className="float-right d-hover fas fa-cog" to="/settings/sidebar/groups" />
						</h6>
						<NavLink className="sidebar-link" to="/groupes/samymauch"><i className="fas fa-sad-tear"></i>Samyest Mauch</NavLink>
						<NavLink className="sidebar-link" to="/groupes/wwbb"><i className="fas fa-skull"></i>Woolly Woolly Bang Bang</NavLink>
					</div>
				</div>
				<p className="sidebar-footer">&lt;&#47;&gt; avec le sang par le SiMDE</p>
			</div>
		);
	}
}

export default Sidebar;
