import React from 'react';
import { NavLink } from 'react-router-dom';
import { connect } from 'react-redux';
import loggedUserActions from '../redux/custom/loggedUser/actions';

@connect(store => ({
	user: store.loggedUser.data.info
}))
class Navbar extends React.Component {
	constructor(props) {
		super(props)
		this.state = {
			loginMethods: {},
			collapse: false,
			loginDropdown: false,
			profileDropdown: false
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
		const { collapse, loginDropdown, profileDropdown } = this.state;
		const { user } = this.props;
		const loginMethods = Object.entries(this.state.loginMethods).filter(([key, loginMethod]) => {
			return loginMethod.login_url
		}).map(([key, loginMethod]) => (
			<a key={ key } className="dropdown-item" href={ loginMethod.login_url } title={ loginMethod.description }>
				{ loginMethod.name }
			</a>
		))
		const isAuthenticated = Boolean(user);
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

					<li className="nav-item no-gutters">
						<a href="#" class="nav-link profilepic bg-secondary " >
						<img src="https://scontent.fmad3-1.fna.fbcdn.net/v/t1.0-1/p160x160/23658429_1517909794962317_254345776605206375_n.jpg?_nc_cat=0&oh=dfba6aba2a80ab8c6de195dfa71b38b3&oe=5C3A5268"
						 width="25" height="25" alt="" class="rounded-circle"></img>
						Jean
						</a>
					</li>

					<li className="nav-item"><a href="#" className="nav-link">	
						<span class="fa-stack fa-sm notification">
							<i class="fa fa-circle fa-stack-2x icon-background2"></i>
							<i class="fa fa-bell fa-stack-1x"><span className="num">23</span></i>
						</span>
					</a></li>
					
					<li className="nav-item"><a href="#" className="nav-link">	
						<span class="fa-stack fa-sm">
							<i class="fa fa-circle fa-stack-2x icon-background2"></i>
							<i class="fa fa-question fa-stack-1x"></i>
						</span>
					</a></li>
					
					<li className="nav-item"><a href="#" className="nav-link">	
						<span class="fa-stack fa-sm">
							<i class="fa fa-circle fa-stack-2x icon-background2"></i>
							<i class="fa fa-cog fa-stack-1x"></i>
						</span>
					</a></li>
					

					<li className="nav-item"><a href="#" className="nav-link">	
						<span class="fa-stack fa-sm">
							<i class="fa fa-circle fa-stack-2x icon-background2"></i>
							<i class="fa fa-lock fa-stack-1x"></i>
						</span>
					</a></li>
					
					{ isAuthenticated ? (
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
