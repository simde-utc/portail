import React, { Component } from 'react';
import { Route, Redirect, Switch } from 'react-router-dom';

// Screens
import ScreensHome from './screens/Home.js';
import ScreensDashboard from './screens/Dashboard';
import ScreensAssosList from './screens/AssosList';
import ScreensAsso from './screens/Asso/Asso.js';
import ScreensProfile from './screens/Profile';

// Components
import Navbar from './components/Navbar';
import Sidebar from './components/Sidebar';

class App extends Component {
	render() {
		// Fake Components
		const RouteNotFound = () => (<div><h1>404</h1></div>)

		return (
			<div className="h-100">
				<Navbar />
				<div className="d-flex w-100 h-100">
					<Sidebar />
					<Switch>
						<Route path="/" exact component={ ScreensHome } />
						<Route path="/dashboard" component={ ScreensDashboard } />
						<Route path="/assos" exact component={ ScreensAssosList } />
						<Route path="/assos/:login" component={ ScreensAsso } />
						<Route path="/profile" component={ ScreensProfile } />
						<Route component={ RouteNotFound } />
					</Switch>
				</div>
			</div>
		);
	}
}

export default App;
