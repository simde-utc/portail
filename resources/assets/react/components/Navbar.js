/**
 * Affichage de la bar de navigation
 *
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Natan Danous <natous.danous@hotmail.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

import React from 'react';
import { Button } from 'reactstrap';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { NavLink } from 'react-router-dom';
import { connect } from 'react-redux';

@connect(store => ({
	user: store.getData('user', false),
	permissions: store.getData('user/permissions'),
	login: store.getData('login', []),
}))
class Navbar extends React.Component {
	constructor(props) {
		super(props);
		this.state = {
			collapse: false,
			loginDropdown: false,
			profileDropdown: false,
		};
		this.toggle.bind(this);
	}

	toggle(key) {
		return this.setState(prevState => ({ [key]: !prevState[key] }));
	}

	render() {
		const { user, login, permissions } = this.props;
		const { collapse, loginDropdown } = this.state;
		const loginMethods = Object.entries(login)
			.filter(([_, loginMethod]) => {
				return loginMethod.login_url;
			})
			.map(([key, loginMethod]) => (
				<a
					key={key}
					className="dropdown-item"
					href={loginMethod.login_url}
					title={loginMethod.description}
				>
					{loginMethod.name}
				</a>
			));

		return (
			<nav className="navbar navbar-expand-md navbar-dark bg fixed-top align-middle">
				<NavLink to="/" className="navbar-brand">
					Portail des associations
				</NavLink>

				<Button className="navbar-toggler" onClick={() => this.toggle('collapse')}>
					<span className="fas fa-bars" />
				</Button>

				<div className={`collapse navbar-collapse${collapse ? ' show' : ''}`}>
					<div className="input-group col-md-6">
						<input
							className="form-control py-2"
							type="search"
							placeholder="Rechercher ..."
							id="example-search-input"
						/>
						<span className="input-group-append">
							<Button className="btn btn-outline-secondary">
								<FontAwesomeIcon icon="search" />
							</Button>
						</span>
					</div>

					<ul className="navbar-nav ml-auto">
						{user ? (
							<li className="nav-item no-gutters pl-2 pr-2">
								<NavLink className="nav-link d-flex profilepic bg-secondary" to="/profile">
									<img
										src={user.image}
										width="25"
										height="25"
										alt=""
										className="rounded-circle mr-2"
									/>
									{user.firstname}
								</NavLink>
							</li>
						) : (
							<li className="nav-item dropdown">
								<a
									className="nav-link dropdown-toggle"
									onClick={() => this.toggle('loginDropdown')}
								>
									Se connecter <span className="caret" />
								</a>
								<div className={`dropdown-menu${loginDropdown ? ' show' : ''}`}>
									{loginMethods}
									<a className="dropdown-item" href="/login">
										Tout voir
									</a>
								</div>
							</li>
						)}

						{user && permissions.length && (
							<li className="nav-item">
								<a className="nav-link d-flex" href="/admin">
									<span className="fa-layers fa-fw fa-lg" style={{ fontSize: 28 }}>
										<FontAwesomeIcon icon="circle" className="icon-background2" />
										<FontAwesomeIcon icon="screwdriver" transform="shrink-8" />
									</span>
								</a>
							</li>
						)}

						{user && (
							<li className="nav-item">
								<NavLink className="nav-link d-flex" to="/notifications">
									<span className="fa-layers fa-fw fa-lg" style={{ fontSize: 28 }}>
										<FontAwesomeIcon icon="circle" className="icon-background2" />
										<FontAwesomeIcon icon="bell" transform="shrink-8" />
									</span>
								</NavLink>
							</li>
						)}

						{user && (
							<li className="nav-item">
								<a className="nav-link d-flex" href="/logout">
									<span className="fa-layers fa-fw fa-lg" style={{ fontSize: 28 }}>
										<FontAwesomeIcon icon="circle" className="icon-background2" />
										<FontAwesomeIcon icon="lock" transform="shrink-8" />
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
