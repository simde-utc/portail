import React from 'react';
import { Route, Redirect, Switch } from 'react-router-dom';

// Components
import Navbar from './components/Navbar';
import Sidebar from './components/Sidebar';
import RouteNotFound from './components/RouteNotFound';
import ErrorCatcher from './components/ErrorCatcher';
import PrivateRoute from './components/PrivateRoute';

// Screens
import HomeScreen from './screens/Home';
import DashboardScreen from './screens/Dashboard';
import AssosListScreen from './screens/AssosList';
import AssoDetailScreen from './screens/Asso/Asso';
import ProfileScreen from './screens/Profile';

class App extends React.Component {
	render() {
		return (
			<div className="h-100">
				<Navbar />
				<div className="d-flex w-100 h-100">
					<Sidebar />
					<ErrorCatcher>
						<Switch>
							<Route path="/" exact component={ HomeScreen } />
							<Route path="/dashboard" component={ DashboardScreen } />
							<Route path="/assos" exact component={ AssosListScreen } />
							<Route path="/assos/:login" component={ AssoDetailScreen } />
							<PrivateRoute path="/profile" component={ ProfileScreen } />
							<Route component={ RouteNotFound } />
						</Switch>
					</ErrorCatcher>
				</div>
			</div>
		);
	}
}

export default App;
