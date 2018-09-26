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
			loginDropdown: false
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
		const { collapse, loginDropdown } = this.state;
		const { user } = this.props;
		const loginMethods = Object.entries(this.props.login).filter(([key, loginMethod]) => {
			return loginMethod.login_url
		}).map(([key, loginMethod]) => (
			<a key={ key } className="dropdown-item" href={ loginMethod.login_url } title={ loginMethod.description }>
				{ loginMethod.name }
			</a>
		))
		return (
			<nav className="navbar navbar-expand-md navbar-dark fixed-top">
				<div className="container-fluid">
					<NavLink className="navbar-brand" to="/">Portail des Assos</NavLink>
					<button className="navbar-toggler text-white" onClick={() => this.toggle('collapse')}>
						<span className="fas fa-bars"></span>
					</button>

					<div className={"collapse navbar-collapse" + (collapse ? ' show' : '')}>
						<ul className="navbar-nav">
						</ul>
						<ul className="navbar-nav ml-auto">
							{ this.props.user ? (
								<li className="nav-item dropdown">
									<a className="nav-link dropdown-toggle" onClick={() => this.toggle('loginDropdown')}>
										{ user.name } <span className="caret"></span>
									</a>
									<div className={"dropdown-menu" + (loginDropdown ? ' show' : '')}>
										<NavLink className="dropdown-item" to="/dashboard">Dashboard</NavLink>
										<NavLink className="dropdown-item" to="/profile">Mon profil</NavLink>
										<a className="dropdown-item" href="/logout">Se déconnecter</a>
									</div>
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
							) }
						</ul>
					</div>
				</div>
			</nav>
		);
	}
}

export default Navbar;

/*
// SEMANTIC
import { Menu, Container, Dropdown, Image } from 'semantic-ui-react'
			<Menu fixed='top' borderless>
				<Container>
					<Menu.Item as='a' header>
						<Image size='mini' src='/logo.png' />
						Portail des Assos
					</Menu.Item>
					<Menu.Item as='a'>Home</Menu.Item>
					<Menu.Item position='right'>
						<Dropdown item simple text='Se connecter'>
							<Dropdown.Menu>
								<Dropdown.Item>CAS / UTC</Dropdown.Item>
								<Dropdown.Item>Mot de Passe</Dropdown.Item>
							</Dropdown.Menu>
						</Dropdown>
					</Menu.Item>
				</Container>
			</Menu>
*/
