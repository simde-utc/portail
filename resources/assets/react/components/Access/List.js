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

import Access from './Access';

const AccessList = ({ list, ...props }) => (
	<div className="container AccessForm" style={{ overflow: 'visible' }}>
		<ListGroup className="container AccessList">
			{list.map(access => (
				<Access key={access.id} access={access} {...props} />
			))}
		</ListGroup>
	</div>
);

export default AccessList;
