import React, { Component } from 'react';
import { connect } from 'react-redux';
import { fetchAssos } from '../actions/assos';

@connect(store => {
	return {
		assos: store.assos.assos,
		fetching: store.assos.fetching
	}
})
class AssosListScreen extends Component {

	componentWillMount() {
		this.props.dispatch(fetchAssos())
	}

	render() {
		return (
			<div className="container">
				<h1>Liste des associations</h1>
				<span className={"loader large" + (this.props.fetching ? ' active' : '') }></span>

				<ul className="list-group">
					{ this.props.assos.map(asso => (
						<li key={ asso.login } className="list-group-item">
							{ asso.shortname }
						</li>
					))}
					</ul>
			</div>
		);
	}
}

export default AssosListScreen;