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
	config: store.config,
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
		const { user, login, permissions, config } = this.props;
		const { collapse, loginDropdown } = this.state;

		return (
			<nav className="navbar navbar-expand-md navbar-dark bg fixed-top align-middle d-flex">
				<NavLink to="/" className="navbar-brand">
					Portail des assos
				</NavLink>
				<ul className="navbar-nav ml-auto" style={{ visibility: 'hidden' }}>
					<li className="nav-item">
						<NavLink className="nav-link d-flex" to="/search">
							<span className="fa-layers fa-fw fa-lg" style={{ fontSize: 28 }}>
								<FontAwesomeIcon icon="circle" className="icon-background2" />
								<FontAwesomeIcon icon="search" transform="shrink-8" />
							</span>
						</NavLink>
					</li>
				</ul>

				<span className="navbar-title fixed">
					{config.title}
				</span>

                <ul className="navbar-toggle navbar-nav ml-auto">
                    {user ? (
                        <li className="nav-item no-gutters pl-2 pr-2" style={{ width: 'max-content' }}>
                            <NavLink className="nav-link d-flex profilepic bg-secondary" to="/profile">
                                <img
                                    src={user.image}
                                    width="25"
                                    height="25"
                                    alt=""
                                    className="rounded-circle mr-2"
                                />
                            </NavLink>
                        </li>
                    ): (
                        <li className="nav-item no-gutters pl-2 pr-2" style={{ width: 'max-content' }}>
                            <a className="nav-link d-flex" href="/login">
    							<span className="fa-layers fa-fw fa-lg" style={{ fontSize: 28 }}>
    								<FontAwesomeIcon icon="circle" className="icon-background2" />
    								<FontAwesomeIcon icon="sign-in-alt" transform="shrink-8" />
    							</span>
    						</a>
                        </li>
                    )}
                </ul>

				<ul className="navbar-nav ml-auto">
					{user ? (
						<li className="nav-item no-gutters pl-2 pr-2" style={{ width: 'max-content' }}>
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

                            <Button onClick={() => this.toggle('collapse')}>
                                <FontAwesomeIcon icon="lock" transform="shrink-8" />
                            </Button>
						</li>
					) : (
						<li className="nav-item">
							<a
								className="nav-link"
								href="/login"
							>
								Se connecter
							</a>
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

					{user && false && (
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
			</nav>
		);
	}
}

export default Navbar;
