/**
 * Affiche les demande d'accès.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019, SiMDE-UTC
 * @license GNU GPL-3.0
 */

import React from 'react';
import { ListGroup } from 'reactstrap';
import { find } from 'lodash';

import Access from './Access';

const AccessList = ({ list, members, canConfirm }) => (
	<div className="container AccessForm" style={{ overflow: 'visible' }}>
		<h1 className="title">Demandes d'accès</h1>
		<ListGroup className="container AccessList">
			{
				list.map(access => <Access key={access.id} access={access} canConfirm={ canConfirm } />)
			}
		</ListGroup>
	</div>
);

export default AccessList;
