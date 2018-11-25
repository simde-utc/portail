/**
 * Assemblage, chargement et composition de l'application entière
 *
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
**/

import React from 'react';
import { Route, Redirect, Switch } from 'react-router-dom';
import 'react-notifications/lib/notifications.css';
import 'react-big-calendar/lib/css/react-big-calendar.css';

// Components
import Navbar from './components/Navbar';
import Sidebar from './components/Sidebar';
import ErrorCatcher from './routes/ErrorCatcher';
import NotFoundRoute from './routes/NotFound';
import LoggedRoute from './routes/Logged';
import { NotificationContainer } from 'react-notifications';

// Screens
import AppLoader from './AppLoader';
import HomeScreen from './screens/Home';
import DashboardScreen from './screens/Dashboard';
import AssosListScreen from './screens/AssosList';
import ServicesListScreen from './screens/ServicesList';
import AssoDetailScreen from './screens/Asso';
import ProfileScreen from './screens/Profile';


class App extends React.Component {
	render() {
		return (
			<div className="h-100">
				<AppLoader />
				<Navbar />

				<div className="d-flex w-100 h-100">
					<Sidebar />
					<ErrorCatcher>
						<Switch>
							<Route path="/" exact component={ HomeScreen } />
							<Route path="/dashboard" component={ DashboardScreen } />
							<Route path="/assos" exact component={ AssosListScreen } />
							<Route path="/assos/:login" component={ AssoDetailScreen } />
							<Route path="/services" exact component={ ServicesListScreen } />
							<LoggedRoute path="/profile" component={ ProfileScreen } />
							<Route component={ NotFoundRoute } />
						</Switch>
					</ErrorCatcher>
				</div>

				<NotificationContainer />
			</div>
		);
	}
}

export default App;
