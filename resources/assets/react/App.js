import React, { Component } from 'react';
import { Route, Switch } from 'react-router-dom';

import ScreensDashboard from './screens/Dashboard';
import Navbar from './components/Navbar';
import Sidebar from './components/Sidebar';

class App extends Component {
	render() {
		const routes = [
		]
		const RouteNotFound = () => (<div><h1>404</h1></div>)
		const Home = () => (<div><h1>Home</h1></div>)

		return (
			<div className="h-100">
				<Navbar />
				<div className="row h-100">
					<Sidebar />
					<div className="col p-4">
						<Switch>
							<Route path="/" exact component={ Home } />
							<Route path="/dashboard" export component={ ScreensDashboard } />        
							<Route component={ RouteNotFound } />
						</Switch>
					</div>
				</div>
			</div>
		);
	}
}

export default App;
