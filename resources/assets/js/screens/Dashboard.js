import React, { Component } from 'react';

import AuthorizedClients from './../components/Passport/AuthorizedClients.js';
import Clients from './../components/Passport/Clients.js';

class ScreensDashboard extends Component {
    render() {
        return (
            <div className="container">
                <div className="row justify-content-center">
                    <div className="col-md-8">
                        <h4 className="my-4"><b>Dashboard</b></h4>

                        <AuthorizedClients />
                        <Clients />
                    </div>
                </div>
            </div>
        );
    }
}