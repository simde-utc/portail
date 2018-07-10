import React, { Component } from 'react';


class Navbar extends Component { 
	render() {
		return (
			<nav className="navbar navbar-expand-md navbar-dark fixed-top">
				<div className="container">
					<a className="navbar-brand" href="/">Portail des Assos</a>
					<button className="navbar-toggler">
						<span className="navbar-toggler-icon"></span>
					</button>

					<div className="collapse navbar-collapse" id="navbarSupportedContent">
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
									<a className="nav-link dropdown-toggle">
										Se connecter <span className="caret"></span>
									</a>
									<div className="dropdown-menu">
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
