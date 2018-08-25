import React from 'react';
import { connect } from 'react-redux';
import loggedUserActions from '../redux/custom/loggedUser/actions';
import { NavLink } from 'react-router-dom';

@connect((store, props) => ({
    user: store.loggedUser.data
}))
class Sidebar extends React.Component { 
	componentWillMount() {
        this.props.dispatch(loggedUserActions.getAssos());
        // this.props.dispatch(loggedUserActions.getGroups());
    }

	render() {
		let assos = [];
		if (this.props.user.assos) {
			let data = this.props.user.assos;

			for (let i = 0; i < data.length; i++) {
				assos.push(
					<NavLink className="sidebar-link" to={ "/assos/" + data[i].login }>
						{ data[i].shortname }
					</NavLink>
				);
			}
		}

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
						{ assos }
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
