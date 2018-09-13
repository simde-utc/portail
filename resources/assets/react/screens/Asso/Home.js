import React from 'react';

class AssoHomeScreen extends React.Component {
	render() {
		const asso = this.props.asso;
		return (
			<div className="container">
				{ (asso) ? (
					<div className="row">
						<div className="col-md-2 mt-5 px-1 d-flex flex-md-column">
							<button className="m-1 btn btn-sm btn-secondary" disabled={ this.props.userIsMember }>
							{ this.props.userIsFollowing && !this.props.userIsMember ? 'Ne plus suivre' : 'Suivre'}
							</button>
							<button className="m-1 btn btn-sm btn-secondary" disabled={ this.props.userIsWaiting }>
							{ this.props.userIsMember ? (this.props.userIsWaiting ? 'En attente..' : 'Se retirer') : 'Rejoindre'}
							</button>
						</div>
						<div className="col-md-8">
							<h1 className="title mb-2">{ asso.shortname }</h1>
							<span className="d-block text-muted mb-4">{ asso.name }</span>
							<span>{ asso.type && asso.type.description }</span>
							<p className="my-3">{ asso.description }</p>
						</div>
						<div className="col-md-2"></div>
					</div>
				) : null }
			</div>
		);
	}
}

export default AssoHomeScreen;
