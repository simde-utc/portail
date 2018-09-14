import React from 'react';
import { connect } from 'react-redux';
import loggedUserActions from '../redux/custom/loggedUser/actions';
import { withRouter, NavLink } from 'react-router-dom';

@connect((store, props) => ({
    user: store.loggedUser.data
}))
class Sidebar extends React.Component {
	componentWillMount() {
        this.props.dispatch(loggedUserActions.getAssos());
        // this.props.dispatch(loggedUserActions.getGroups());
  }

  getAssos(assos) {
    return (assos || []).map(asso => {
      let color = 'color-' + asso.login;

      if (asso.parent)
        color += ' color-' + asso.parent.login;

      return (
        <NavLink key={ asso.id } className="sidebar-link" to={ "/assos/" + asso.login }>
          <i className={ asso.pivot.role_id ? 'fas fa-hands-helping' : 'fas fa-thumbs-up' }></i>
          <span className={ color }>{ asso.shortname }</span>
        </NavLink>
    )});
  }

	render() {
		// TODO: Groups to do (fetch and display like assos).
		// <div className="sidebar-group">
		// 	<h6 className="sidebar-header d-hover-zone">
		// 		MES GROUPES <NavLink className="float-right d-hover fas fa-cog" to="/settings/sidebar/groups" />
		// 	</h6>
		// </div>

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
						{ this.getAssos(this.props.user.assos) }
					</div>
				</div>
				<p className="sidebar-footer small">&lt;&#47;&gt; avec le sang par le SiMDE</p>
			</div>
		);
	}
}

export default withRouter(Sidebar);
