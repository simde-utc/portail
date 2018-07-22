import React, { Component } from 'react';
import { Link } from 'react-router-dom';


class Sidebar extends Component { 
	render() {
		return (
			<div className="sidebar col-md-3 col-xl-2 d-none d-md-flex flex-column justify-content-between">
				<div className="sidebar-inner">
					<div className="sidebar-group">
						<h6 className="sidebar-header d-hover-zone">
							ACTUALITÉS <Link className="float-right d-hover fas fa-cog" to="/settings/sidebar/news" />
						</h6>
						<Link className="sidebar-link" to="/"><i className="fas fa-newspaper"></i>Flux</Link>
						<Link className="sidebar-link" to="/"><i className="fas fa-newspaper"></i>Actualités UTC</Link>
						<Link className="sidebar-link" to="/"><i className="fas fa-newspaper"></i>Actualités Assos</Link>
					</div>

					<div className="sidebar-group">
						<h6 className="sidebar-header d-hover-zone">
							RACCOURCIS <Link className="float-right d-hover fas fa-cog" to="/settings/sidebar/shortcuts" />
						</h6>
						<Link className="sidebar-link" to="/"><i className="fas fa-school"></i>ENT UTC</Link>
						<Link className="sidebar-link" to="/"><i className="fas fa-paper-plane"></i>Webmail</Link>
						<Link className="sidebar-link" to="/"><i className="fas fa-book"></i>Moodle</Link>
						<Link className="sidebar-link" to="/evenements"><i className="fas fa-calendar-alt"></i>Évènements</Link>
						<Link className="sidebar-link" to="/assos"><i className="fas fa-hands-helping"></i>Associations</Link>
						<Link className="sidebar-link" to="/groupes"><i className="fas fa-users"></i>Groupes</Link>
					</div>

					<div className="sidebar-group">
						<h6 className="sidebar-header d-hover-zone">
							MES ASSOCIATIONS <Link className="float-right d-hover fas fa-cog" to="/settings/sidebar/assos" />
						</h6>
						<Link className="sidebar-link" to="/"><i className="fas fa-beer"></i>Pic'Asso</Link>
						<Link className="sidebar-link" to="/"><i className="fas fa-code"></i>SiMDE</Link>
					</div>

					<div className="sidebar-group">
						<h6 className="sidebar-header d-hover-zone">
							MES GROUPES <Link className="float-right d-hover fas fa-cog" to="/settings/sidebar/groups" />
						</h6>
						<Link className="sidebar-link" to="/"><i className="fas fa-sad-tear"></i>Samyest Mauch</Link>
						<Link className="sidebar-link" to="/"><i className="fas fa-skull"></i>Woolly Woolly Bang Bang</Link>
					</div>
				</div>
				<p className="sidebar-footer">&lt;&#47;&gt; avec le sang par le SiMDE</p>
			</div>
		);
	}
}

export default Sidebar;
