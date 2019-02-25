import React from 'react';
import AspectRatio from 'react-aspect-ratio';

import { Card, CardBody, CardTitle, CardSubtitle, CardFooter } from 'reactstrap';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';

import Image from '../Image';

const Member = ({ image, title, description, footer }) => (
	<Card className="m-2 p-0" style={{ width: 225 }}>
		<AspectRatio ratio="1" style={{ height: 200 }} className="d-flex justify-content-center mt-2">
			<Image
				image={image}
				className="img-thumbnail"
				style={{ height: '100%' }}
				unloader={
					<div className="d-flex justify-content-end align-items-end">
						<FontAwesomeIcon size="10x" icon="user-alt" />
					</div>
				}
			/>
		</AspectRatio>
		<CardBody>
			<CardTitle>{title}</CardTitle>
			<CardSubtitle>{description}</CardSubtitle>
		</CardBody>
		{footer && <CardFooter>{footer}</CardFooter>}
	</Card>
);

export default Member;
