import React, { Component } from 'react';

class AuthorizedClients extends Component { 
	constructor(props) {
		super(props);
		this.state = {
			tokens: []
		} 
	} 

	componentDidMount() {
		axios.get('/oauth/tokens').then(response => {
			this.setState({ tokens: response.data });
		});
	}

	render() {
		if (this.state.tokens.length > 0)
			return(
				<div class="card drop-shadow mb-4">
					<div class="card-body">
						<div class="row">
							<div class="col-6">
								<h5>Applications autorisées</h5>
							</div>
						</div>

						<div class="row mt-3 mb-0" v-for="token in tokens">
							<div class="col-sm-3 mb-2">
								<b>token.client.name</b>
							</div>

							<div class="col-sm-6 mb-2">
								<span v-if="token.scopes.length > 0">
									token.scopes.join(', ')
								</span>
							</div>

							<div class="col-sm-3 text-md-right">
								<a class="btn btn-primary btn-sm" onClick="revoke(token)">
									Révoquer
								</a>
							</div>
						</div>
					</div>
				</div>
			);
		else
			return null;
	}
}

export default AuthorizedClients;