/**
 * Generate an application card.
 *
 * @author Corentin Mercier <corentin@cmercier.fr>
 *
 * @copyright Copyright (c) 2019, SiMDE-UTC
 * @license GNU GPL-3.0
 * */
import React from 'react';
import { Card, CardBody, CardHeader, CardTitle, Button } from 'reactstrap';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';

const ApplicationCard = ({ application, categories, revokeToken }) => {
	const style = {
		display: 'block',
		textAlign: 'left',
		margin: '10px',
		minWidth: '44%',
	};
	return (
		<Card style={style}>
			<CardHeader>{application.client.name}</CardHeader>
			<CardBody>
				<CardTitle className="mb-2 text-muted">{application.client.name}</CardTitle>
				L'application peut:
				{categories &&
					Object.keys(categories).map(key => {
						return (
							<div key={categories[key].description}>
								<div className="d-flex align-items-center">
									<FontAwesomeIcon
										icon={categories[key].icon}
										className="m-2"
										style={{ height: '1rem' }}
									/>
									<p className="m-2">{categories[key].description}</p>
								</div>
								<ul style={{ testAlign: 'left' }}>
									{categories[key].scopes &&
										categories[key].scopes.map(desc => {
											return <li key={desc}>{desc}</li>;
										})}
								</ul>
								<p>
									Vous avez accepté la{' '}
									<a
										href={application.client.policy_url}
										style={{ fontStyle: 'italic', color: 'grey' }}
									>
										politique de confidentialité
									</a>{' '}
									de {application.client.name}
								</p>
							</div>
						);
					})}
				<div className="text-right">
					<Button color="danger" onClick={revokeToken}>
						Révoquer les droits
					</Button>
				</div>
			</CardBody>
		</Card>
	);
};

export default ApplicationCard;
