import React from 'react';

class AssoHomeScreen extends React.Component {
	render() {
		const asso = this.props.asso;
		return (
			<div className="container">
				{ (asso) ? (
					<div className="row">
						<div className="col-md-2 mt-5">
							<button className="btn btn-sm btn-secondary mr-2">Suivre</button>
							<button className="btn btn-sm btn-secondary">Rejoindre</button>
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
