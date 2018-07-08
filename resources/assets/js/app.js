import React, { Component } from 'react';

import { Route } from 'react-router-dom';

import Navbar from './components/Navbar.js';
import AuthorizedClients from './components/Passport/AuthorizedClients.js';
import Clients from './components/Passport/Clients.js';

class App extends Component {
    render() {
        return (
            <div>
                <Navbar />
                <Route exact path="/" component={ScreensDashboard} />        
            </div>
        );
    }
}

export default App;
