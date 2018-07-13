import React, { Component } from 'react';
import { connect } from 'react-redux';
import { fetchAssos } from '../actions/assos';

@connect(store => {
	return {
		assos: store.assos.assos
	}
})
class AssosListScreen extends Component {

	componentWillMount() {
		this.props.dispatch(fetchAssos())
	}

	render() {
		console.log(this.props.assos)
		
		return (
			<div className="container">
				<h1>Liste des associations</h1>
				<ul className="list-group">
				{
					this.props.assos.map(asso => (
						<li key={ asso.login } className="list-group-item">
							{ asso.shortname }
						</li>
					))
				}
				</ul>
			</div>
		);
	}
}

export default AssosListScreen;