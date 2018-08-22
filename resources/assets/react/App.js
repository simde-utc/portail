import React from 'react';
import { Route, Redirect, Switch } from 'react-router-dom';

// Components
import Navbar from './components/Navbar';
import Sidebar from './components/Sidebar';
import RouteNotFound from './components/RouteNotFound';

// Screens
import HomeScreen from './screens/Home.js';
import DashboardScreen from './screens/Dashboard';
import AssosListScreen from './screens/AssosList';
import AssoDetailScreen from './screens/Asso/Asso.js';
import ProfileScreen from './screens/Profile';

class App extends React.Component {
	render() {
		return (
			<div className="h-100">
				<Navbar />
				<div className="d-flex w-100 h-100">
					<Sidebar />
					<Switch>
						<Route path="/" exact component={ HomeScreen } />
						<Route path="/dashboard" component={ DashboardScreen } />
						<Route path="/assos" exact component={ AssosListScreen } />
						<Route path="/assos/:login" component={ AssoDetailScreen } />
						<Route path="/profile" component={ ProfileScreen } />
						<Route component={ RouteNotFound } />
					</Switch>
				</div>
			</div>
		);
	}
}

export default App;
