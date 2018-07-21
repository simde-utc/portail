import React, { Component } from 'react';
import { Link } from 'react-router-dom';


class UserInfo extends Component {
	componentWillMount() {
		const { missing, info, details } = this.props;
		// Check for missing informations
		if (!info)		missing('info');
		if (!details)	missing('details');
	}

	render() {
		const { info, details } = this.props;
		// Si des informations sont manquantes, chargement
		if (!info || !details)
			return <div>loading</div>

		return (
			<div>
				<h2 className="title">Mes Informations</h2>
				<code><pre>{ JSON.stringify([info, details], null, 2)}</pre></code>
				<table className="table">
					<tbody>
						<tr>
							<th>Prénom</th>
							<td>{ info.firstname }</td>
						</tr>
						<tr>
							<th>Nom</th>
							<td>{ info.lastname }</td>
						</tr>
						<tr>
							<th>Email</th>
							<td>{ info.email }</td>
						</tr>
						<tr>
							<th>Type</th>
							<td>{ info.type }</td>
						</tr>
						<tr>
							<th>Email</th>
							<td>{ info.email }</td>
						</tr>
						<tr>
							<th>Email</th>
							<td>{ info.email }</td>
						</tr>
					</tbody>
				</table>
			</div>
		);
	}
}

export default UserInfo;