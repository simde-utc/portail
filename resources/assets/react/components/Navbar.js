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
			<a href="https://assos.utc.fr" title="BDE" className="navbar-brand">
				<img src="https://bobby.nastuzzi.fr/assets/img/bde.png" width="45" alt=""></img>
				Portail des Assos
			</a>

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
						<li className="nav-item no-gutters">
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



			// <nav className="navbar navbar-expand-md navbar-dark bg fixed-top">

			// 		<div className="col-md-2">
			// 			<a className="nav-item navbar-brand" rel="BDE" href="https://assos.utc.fr" title="BDE">
			// 				<img src="https://bobby.nastuzzi.fr/assets/img/bde.png" width="45" alt=""></img>
			// 				Portail des Assos
			// 			</a>
			// 		</div>

			// 		<button className="navbar-toggler" onClick={() => this.toggle('collapse')}>
			// 			<span className="fas fa-bar"></span>
			// 		</button>


			// 	<div className={"collapse navbar-collapse" + (collapse ? ' show' : '')}>

			// 		<div className="input-group col-md-4 mr-auto">
			// 			<input className="form-control py-2" type="search" placeholder="Rechercher ..." id="example-search-input"/>
			// 			<span className="input-group-append">
			// 				<button className="btn btn-outline-secondary" type="button">
			// 					<i className="fa fa-search"></i>
			// 				</button>
			// 			</span>
			// 		</div>


			// 			{/* { isAuthenticated ? ( */}
			// 			<div className="nav-item dropdown ml-auto">
			// 				<a className="nav-link dropdown-toggle" onClick={() => this.toggle('profileDropdown')}>
			// 					{/* { user.name } */} Jea
			// 				</a>
			// 				<div className={"dropdown-menu" + (profileDropdown ? ' show' : '')}>
			// 					<NavLink className="dropdown-item" to="/dashboard">Dashboard</NavLink>
			// 					<NavLink className="dropdown-item" to="/profile">Mon profil</NavLink>
			// 					<a className="dropdown-item" href="/logout">Se déconnecter</a>
			// 				</div>
			// 			</div>
			// 			{/* ) : ''} */}

			// 			<div className="nav-item ml-auto">
			// 				<button href="#" className="fas fa-bell"></button>
			// 			</div>


			// 				<ul className="navbar-nav">
			// 				</ul>
			// 				<ul className="navbar-nav ml-auto">
			// 					{ isAuthenticated ? (
			// 						<li className="nav-item dropdown">
			// 							<a className="nav-link dropdown-toggle" onClick={() => this.toggle('loginDropdown')}>
			// 								{ user.name } <span className="caret"></span>
			// 							</a>
			// 							<div className={"dropdown-menu" + (loginDropdown ? ' show' : '')}>
			// 								<NavLink className="dropdown-item" to="/dashboard">Dashboard</NavLink>
			// 								<NavLink className="dropdown-item" to="/profile">Mon profil</NavLink>
			// 								<a className="dropdown-item" href="/logout">Se déconnecter</a>
			// 							</div>
			// 						</li>
			// 					) : (
			// 						<li className="nav-item dropdown">
			// 							<a className="nav-link dropdown-toggle" onClick={() => this.toggle('loginDropdown')}>
			// 								Se connecter <span className="caret"></span>
			// 							</a>
			// 							<div className={"dropdown-menu" + (loginDropdown ? ' show' : '')}>
			// 								{ loginMethods }
			// 								<a className="dropdown-item" href="/login">Tout voir</a>
			// 							</div>
			// 						</li>
			// 					) }
			// 				</ul>
			// 			</div>
			// </nav>

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
