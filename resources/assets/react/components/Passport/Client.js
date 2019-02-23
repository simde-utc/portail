import React from 'react';
import { Button } from 'reactstrap';

const Client = ({ client, viewClient, editClient }) => (
	<div className="row mt-3 mb-0">
		<div className="col-sm-3">
			<h6 className="mb-2">{client.name}</h6>
			<Button className="btn btn-light btn-sm mb-1 mr-1" onClick={() => viewClient(client)}>
				Voir
			</Button>
			<Button className="btn btn-light btn-sm mb-1" onClick={() => editClient(client)}>
				Modifier
			</Button>
		</div>
		<table className="col-sm-9">
			<tbody>
				<tr>
					<th>ID Client</th>
					<td>{client.id}</td>
				</tr>
				<tr>
					<th>ID Asso</th>
					<td>{client.asso_id}</td>
				</tr>
				<tr>
					<th>Secret</th>
					<td>
						<code>{client.secret}</code>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
);

export default Client;
