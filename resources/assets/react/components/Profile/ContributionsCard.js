/**
 * Generate an contribution card.
 *
 * @author Amaury Guichard <amaury.guichard@etu.utc.fr>
 *
 * @copyright Copyright (c) 2019, SiMDE-UTC
 * @license GNU GPL-3.0
 * */
import React from 'react';
import { Card, CardBody, CardHeader, CardText, CardTitle } from 'reactstrap';

const ContributionsCard = ({ start, end, semester1, semester2, amount }) => {
	const style = {
		display: 'inline-block',
		textAlign: 'center',
		margin: '10px',
	};
	return (
		<Card style={style}>
			<CardHeader>
				{semester1} {semester2 == '' ? '' : '-'} {semester2}
			</CardHeader>
			<CardBody>
				<CardTitle className="mb-2 text-muted">{amount}€</CardTitle>
				<CardText>
					du {start} au {end}
				</CardText>
			</CardBody>
		</Card>
	);
};

export default ContributionsCard;
