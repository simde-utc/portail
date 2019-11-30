/**
 * Display user's personnal data.
 *
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Paco Pompeani <paco.pompeani@etu.utc.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

import React from 'react';

import Image from '../Image';

const displayTypes = {
	admin: 'Administrateur',
	member: "Membre d'une association",
	contributorBde: 'Cotisant BDE',
	cas: 'Connecté via CAS',
	password: 'Connecté via mot de passe',
};

const UserInfo = ({ info: { firstname, lastname, email, types, image } }) => {
	// If some data is missing, display loading.
	if (!firstname) return <div>loading</div>;

	return (
		<div style={{ overflowX: 'auto' }}>
			<h2 className="title">Mes Informations</h2>
			<table className="table">
				<tbody>
					<tr>
						<th>Prénom</th>
						<td>{firstname}</td>
					</tr>
					<tr>
						<th>Nom</th>
						<td>{lastname}</td>
					</tr>
					<tr>
						<th>Email</th>
						<td>{email}</td>
					</tr>
					{Object.entries(types)
						.filter(([key, value]) => value && displayTypes[key])
						.map(([key]) => (
							<tr key={key}>
								<th>Role</th>
								<td>{displayTypes[key]}</td>
							</tr>
						))}
					<tr>
						<th>Photo de profil</th>
						<td>
							<Image image={image} style={{ maxHeight: '200px', maxWidth: '70vw' }} />
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	);
};

export default UserInfo;
