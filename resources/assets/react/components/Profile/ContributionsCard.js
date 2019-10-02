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

const ContributionsCard = ({ start, end, semesters, amount }) => {
	const style = {
		display: 'inline-block',
		textAlign: 'center',
		margin: '10px',
	};
	return (
		<Card style={style}>
			<CardHeader>
				{semesters.map((semester, index) => {
					let result = semester.name;
					result += index + 1 === semesters.length ? '' : ' - ';
					return `${result}`;
				})}
			</CardHeader>
			<CardBody>
				<CardTitle className="mb-2 text-muted">{amount}€</CardTitle>
				<CardText>
					du {start.format('Do MMMM YYYY')} au {end.format('Do MMMM YYYY')}
				</CardText>
			</CardBody>
		</Card>
	);
};

export default ContributionsCard;
