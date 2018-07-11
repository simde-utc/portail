import React, { Component } from 'react';
import { Link } from 'react-router-dom';


class Navbar extends Component { 
	constructor(props) {
		super(props)
		this.state = {
			collapse: false,
			loginDropdown: false
		}
		this.toggle.bind(this);
	}

	toggle(key) {
		let newState = {};
		this.setState(prevState => {
			newState[key] = !prevState[key];
			return newState;
		});
	}

	render() {
		const { collapse, loginDropdown } = this.state;
		return (
			<nav className="navbar navbar-expand-md navbar-dark fixed-top">
				<Link className="navbar-brand" to="/">Portail des Assos</Link>
				<button className="navbar-toggler text-white" onClick={() => this.toggle('collapse')}>
					<span className="fas fa-bars"></span>
				</button>				

				<div className={"collapse navbar-collapse" + (collapse ? ' show' : '')}>
					<ul className="navbar-nav">
						<li className="nav-item">
							<Link className="nav-link" to="/dashboard">Dashboard</Link>
						</li>
					</ul>
					<ul className="navbar-nav ml-auto">
						{ this.props.isAuthenticated ? (
							<li className="nav-item dropdown">
								<a className="nav-link dropdown-toggle">
									{ this.props.name } <span className="caret"></span>
								</a>
								<div className="dropdown-menu">
									<a className="dropdown-item" href="/logout" onClick="event.preventDefault(); document.getElementById('logout-form').submit();">
										Se déconnecter
									</a>

										<form id="logout-form" action="/logout" method="POST" style="display: none;">
											csrf
										</form>
									</div>
								</li>
							) : (
								<li className="nav-item dropdown">
									<a className="nav-link dropdown-toggle" onClick={() => this.toggle('loginDropdown')}>
										Se connecter <span className="caret"></span>
									</a>
									<div className={"dropdown-menu" + (loginDropdown ? ' show' : '')}>
										<a className="dropdown-item" href="/login">Tout voir</a>

								{/*
									@foreach (config('auth.services') as $name => $provider)
										<a className="dropdown-item" href="{{ route('login.show', ['provider' => $name, 'redirect' => $redirect ?? url()->previous()]) }}">
											{{ $provider['name'] }}
										</a>
									@endforeach
										<a className="dropdown-item" href="{{ route('login', ['see' => 'all', 'redirect' => $redirect ?? url()->previous()]) }}">
											Tout voir
										</a>
								*/}
								</div>
							</li>
						)}
					</ul>
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
