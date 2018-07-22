import React, { Component } from 'react';
import { Route, Redirect, Switch } from 'react-router-dom';

// Components
import Navbar from './components/Navbar';
import Sidebar from './components/Sidebar';

// Screens
import HomeScreen from './screens/Home.js';
import DashboardScreen from './screens/Dashboard';
import AssosListScreen from './screens/AssosList';
import AssoDetailScreen from './screens/AssoDetail';
import ProfileScreen from './screens/Profile';

class App extends Component {
	render() {
		// Fake Components
		const RouteNotFound = () => (<div><h1>404</h1></div>)

		return (
			<div className="h-100">
				<Navbar />
				<div className="d-flex w-100 h-100">
					<Sidebar />
					<main className="col loader-container">
						<Switch>
							<Route path="/" exact component={ HomeScreen } />
							<Route path="/home" exact render={ () => (<Redirect to="/" />) } />
							<Route path="/dashboard" component={ DashboardScreen } />
							<Route path="/assos" exact component={ AssosListScreen } />
							<Route path="/assos/:login" component={ AssoDetailScreen } />
							<Route path="/profile" component={ ProfileScreen } />
							<Route component={ RouteNotFound } />
						</Switch>
					</main>
				</div>
			</div>
		);
	}
}

export default App;
