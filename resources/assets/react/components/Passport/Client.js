import React, { Component } from 'react';
	
class Client extends Component { 
	render() {
		const { client, viewClient, editClient } = this.props;
		return (
			<div className="row mt-3 mb-0">
				<div className="col-sm-3">
					<h6 className="mb-2">{ client.name }</h6> 
					<button className="btn btn-light btn-sm mb-1 mr-1" onClick={ () => viewClient(client) }>Voir</button>
					<button className="btn btn-light btn-sm mb-1" onClick={ () => editClient(client) }>Modifier</button>
				</div>
				<table className="col-sm-9">
					<tbody>
						<tr>
							<th>ID Client</th>
							<td>{ client.id }</td>
						</tr>
						<tr>
							<th>ID Asso</th>
							<td>{ client.asso_id }</td>
						</tr>
						<tr>
							<th>Secret</th>
							<td><code>{ client.secret }</code></td>
						</tr>
					</tbody>
				</table>
			</div>
		);
	}
};

export default Client;
