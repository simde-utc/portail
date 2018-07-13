import React, { Component } from 'react';
import { Route, Switch } from 'react-router-dom';

// Components
import Navbar from './components/Navbar';
import Sidebar from './components/Sidebar';

// Screens
import HomeScreen from './screens/Home.js';
import DashboardScreen from './screens/Dashboard';
import AssosListScreen from './screens/AssosList';

class App extends Component {
	render() {
		// Fake Components
		const RouteNotFound = () => (<div><h1>404</h1></div>)

		return (
			<div className="h-100">
				<Navbar />
				<div className="d-flex w-100 h-100">
					<Sidebar />
					<main className="col p-4">
						<Switch>
							<Route exact path="/" component={ HomeScreen } />
							<Route path="/dashboard" export component={ DashboardScreen } />        
							<Route path="/assos" export component={ AssosListScreen } />        
							<Route component={ RouteNotFound } />
						</Switch>
					</main>
				</div>
			</div>
		);
	}
}

export default App;
