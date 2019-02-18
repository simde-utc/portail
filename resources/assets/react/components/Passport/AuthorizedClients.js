import React, { Component } from 'react';
import { connect } from 'react-redux';

import actions from '../../redux/actions';

@connect(store => ({
	tokens: store.isFetched(['oauth', 'tokens']),
}))
class AuthorizedClients extends Component {
	componentDidMount() {
		const { dispatch } = this.props;

		dispatch(actions.oauth.tokens.all());
	}

	render() {
		const { tokens } = this.props;

		if (tokens.length) {
			return (
				<div className="card drop-shadow mb-4">
					<div className="card-body">
						<div className="row">
							<div className="col-6">
								<h5>Applications autorisées</h5>
							</div>
						</div>

						<div className="row mt-3 mb-0">
							{tokens.map(token => (
								<div key={token.id}>
									<div className="col-sm-3 mb-2">
										<b>{token.client.name}</b>
									</div>

									<div className="col-sm-6 mb-2">
										<span>{token.scopes.join(', ')}</span>
									</div>

									<div className="col-sm-3 text-md-right">
										<a className="btn btn-primary btn-sm" onClick="revoke(token)">
											Révoquer
										</a>
									</div>
								</div>
							))}
						</div>
					</div>
				</div>
			);
		}
		return <div />;
	}
}

export default AuthorizedClients;
