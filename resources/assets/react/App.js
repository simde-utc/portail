import React from 'react';
import { connect } from 'react-redux';
import { Route, Redirect, Switch } from 'react-router-dom';
import 'react-notifications/lib/notifications.css';
import 'react-big-calendar/lib/css/react-big-calendar.css';

// Components
import Navbar from './components/Navbar';
import Sidebar from './components/Sidebar';
import ErrorCatcher from './components/ErrorCatcher';
import RouteNotFound from './components/RouteNotFound';
import PrivateRoute from './components/PrivateRoute';
import { NotificationContainer } from 'react-notifications';

// Screens
import HomeScreen from './screens/Home';
import DashboardScreen from './screens/Dashboard';
import AssosListScreen from './screens/AssosList';
import AssoDetailScreen from './screens/Asso';
import ProfileScreen from './screens/Profile';

@connect((store, props) => ({
	isAuthenticated: store.isFetched('user'),
}))
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
							<PrivateRoute path="/profile" authorized={ this.props.user } component={ ProfileScreen } />
							<Route component={ RouteNotFound } />
						</Switch>
					</ErrorCatcher>
				</div>

				<NotificationContainer />
			</div>
		);
	}
}

export default App;
