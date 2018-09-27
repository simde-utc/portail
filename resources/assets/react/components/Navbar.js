import React from 'react';
import { NavLink } from 'react-router-dom';
import { connect } from 'react-redux';
import actions from '../redux/actions.js';

@connect(store => ({
	user: store.getData('user', false),
	login: store.getData('login', []),
}))
class Navbar extends React.Component {
	constructor(props) {
		super(props)
		this.state = {
			collapse: false,
			loginDropdown: false,
			profileDropdown: false
		};
		this.toggle.bind(this);
	}

	componentWillMount() {
		// Get User Info
		this.props.dispatch(actions.user.get())
		// Get Login Methods
		this.props.dispatch(actions.login.get())
	}

	toggle(key) {
		return this.setState(prevState => ({ [key]: !prevState[key] }));
	}

	render() {
		const { collapse, loginDropdown, profileDropdown } = this.state;
		const { user } = this.props;
		const loginMethods = Object.entries(this.props.login).filter(([key, loginMethod]) => {
			return loginMethod.login_url
		}).map(([key, loginMethod]) => (
			<a key={ key } className="dropdown-item" href={ loginMethod.login_url } title={ loginMethod.description }>
				{ loginMethod.name }
			</a>
		))
		return (
			<nav className="navbar navbar-expand-md navbar-dark bg fixed-top align-middle">
				<NavLink to="/" className="navbar-brand">
					Portail des Assos
				</NavLink>

				<button className="navbar-toggler" onClick={() => this.toggle('collapse')}>
					<span className="fas fa-bars"></span>
				</button>

				<div className={"collapse navbar-collapse" + (collapse ? ' show' : '')}>
			 		<div className="input-group col-md-6">
						<input className="form-control py-2" type="search" placeholder="Rechercher ..." id="example-search-input"/>
						<span className="input-group-append">
							<button className="btn btn-outline-secondary" type="button">
								<i className="fa fa-search"></i>
							</button>
						</span>
					</div>

					<ul className="navbar-nav ml-auto">
						{ this.props.user ? (
							<li className="nav-item no-gutters pl-2 pr-2">
								<NavLink className="nav-link profilepic bg-secondary" to="/profile">
									<img src={ this.props.user.image }
									 width="25" height="25" alt="" className="rounded-circle mr-2" />
								 	{ this.props.user.firstname }
								</NavLink>
							</li>
						) : (
							<li className="nav-item dropdown">
								<a className="nav-link dropdown-toggle" onClick={() => this.toggle('loginDropdown')}>
									Se connecter <span className="caret"></span>
								</a>
								<div className={"dropdown-menu" + (loginDropdown ? ' show' : '')}>
									{ loginMethods }
									<a className="dropdown-item" href="/login">Tout voir</a>
								</div>
							</li>
						)}


						{ this.props.user && (
							<li className="nav-item">
								<NavLink className="nav-link" to="/notifications">
									<span className="fa-stack fa-sm">
										<i className="fa fa-circle fa-stack-2x icon-background2"></i>
										<i className="fa fa-bell fa-stack-1x"></i>
									</span>
								</NavLink>
							</li>
						)}

						<li className="nav-item">
							<NavLink className="nav-link" to="/help">
								<span className="fa-stack fa-sm">
									<i className="fa fa-circle fa-stack-2x icon-background2"></i>
									<i className="fa fa-question fa-stack-1x"></i>
								</span>
							</NavLink>
						</li>

						{ this.props.user && (
							<li className="nav-item">
								<a className="nav-link" href="/logout">
									<span className="fa-stack fa-sm">
										<i className="fa fa-circle fa-stack-2x icon-background2"></i>
										<i className="fa fa-lock fa-stack-1x"></i>
									</span>
								</a>
							</li>
						)}
					</ul>
				</div>
			</nav>
		);
	}
}

export default Navbar;
