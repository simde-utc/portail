import React, { Component } from 'react';
import { Container, Header } from 'semantic-ui-react';

import AuthorizedClients from './../components/Passport/AuthorizedClients.js';
import Clients from './../components/Passport/Clients.js';
import PersonalAccessTokens from './../components/Passport/PersonalAccessTokens.js';

class ScreensDashboard extends Component {
	render() {
		return (
			<Container>
				<div className="row justify-content-center">
					<div className="col-md-8">
						<Header as='h1'>Dashboard</Header>

						<AuthorizedClients />
						<Clients />
					</div>
				</div>
			</Container>
		);
	}
}

export default ScreensDashboard;