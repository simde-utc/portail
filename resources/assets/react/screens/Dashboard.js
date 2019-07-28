/**
 * Token management.
 *
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 * @author Natan Danous <natous.danous@hotmail.fr>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

import React from 'react';

import AuthorizedClients from '../components/Passport/AuthorizedClients';
import Clients from '../components/Passport/Clients';
import PersonalAccessTokens from '../components/Passport/PersonalAccessTokens';

const ScreensDashboard = () => (
	<div className="container">
		<h1 className="title">Dashboard</h1>
		<AuthorizedClients />
		<Clients />
		<PersonalAccessTokens />
	</div>
);

export default ScreensDashboard;
