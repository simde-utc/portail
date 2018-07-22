import React, { Component } from 'react';
import { Link } from 'react-router-dom';
import { connect } from 'react-redux';
import loggedUserActions from '../redux/custom/loggedUser/actions';

@connect(store => ({
	user: store.loggedUser.data.info
}))
class Navbar extends Component {
	constructor(props) {
		super(props)
		this.state = {
			loginMethods: {},
			collapse: false,
			loginDropdown: false
		};
		this.toggle.bind(this);
	}

	componentWillMount() {
		// Get User Info
		this.props.dispatch(loggedUserActions.getInfo())
		// Get Login Methods
		axios.get('/api/v1/login').then(response => this.setState({ loginMethods: response.data}))
	}

	toggle(key) {
		return this.setState(prevState => ({ [key]: !prevState[key] }));
	}

	render() {
		const { collapse, loginDropdown } = this.state;
		const { user } = this.props;
		const loginMethods = Object.entries(this.state.loginMethods).map(([key, loginMethod]) => (
			<a key={ key } className="dropdown-item" href={ loginMethod.url } title={ loginMethod.description }>
				{ loginMethod.name }
			</a>
		))
		const isAuthenticated = Boolean(user);
		return (
			<nav className="navbar navbar-expand-md navbar-dark fixed-top">
				<div className="container-fluid">
					<Link className="navbar-brand" to="/">Portail des Assos</Link>
					<button className="navbar-toggler text-white" onClick={() => this.toggle('collapse')}>
						<span className="fas fa-bars"></span>
					</button>

					<div className={"collapse navbar-collapse" + (collapse ? ' show' : '')}>
						<ul className="navbar-nav">
						</ul>
						<ul className="navbar-nav ml-auto">
							{ isAuthenticated ? (
								<li className="nav-item dropdown">
									<a className="nav-link dropdown-toggle" onClick={() => this.toggle('loginDropdown')}>
										{ user.name } <span className="caret"></span>
									</a>
									<div className={"dropdown-menu" + (loginDropdown ? ' show' : '')}>
										<Link className="dropdown-item" to="/dashboard">Dashboard</Link>
										<Link className="dropdown-item" to="/profile">Mon profil</Link>
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
