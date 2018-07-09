import React, { Component } from 'react';

import { Route } from 'react-router-dom';

import ScreensDashboard from './screens/Dashboard';

import Navbar from './components/Navbar';


class App extends Component {
    render() {
        return (
            <div>
                <Navbar />
                <div style={{ marginTop: '66px' }}>
                	<Route exact path="/" component={ScreensDashboard} />        
                </div>
            </div>
        );
    }
}

export default App;
