import React, { Component } from 'react';

import Navbar from './Navbar.js';
import AuthorizedClients from './Passport/AuthorizedClients.js';
import Clients from './Passport/Clients.js';

class App extends Component {
    render() {
        return (
            <div>
                <Navbar />
                <div className="container">
                    <div className="row justify-content-center">
                        <div className="col-md-8">
                            <h4 className="my-4"><b>Dashboard</b></h4>

                            <AuthorizedClients />
                            <Clients />
                        </div>
                    </div>
                </div>
            </div>
        );
    }
}

export default App;
