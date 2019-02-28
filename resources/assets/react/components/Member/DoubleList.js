import React from 'react';
import { Button } from 'reactstrap';

import List from './List';

const DoubleList = props => {
	const { join, isCurrentSemester, members } = props;
	const acceptedMembers = members.filter(member => member.pivot && member.pivot.validated_by);
	const notAcceptedMembers = members.filter(member => member.pivot && !member.pivot.validated_by);

	return (
		<div className="container DoubleList">
			<List title="Liste des membres" {...props} members={acceptedMembers} />
			{notAcceptedMembers.length > 0 && (
				<List
					title="Liste des membres en attente de validation"
					{...props}
					members={notAcceptedMembers}
				/>
			)}
			{join && isCurrentSemester && (
				<div className="d-flex justify-content-center col-md-12">
					<Button className="m-1 btn btn-m font-style-bold" color="primary" outline onClick={join}>
						Rejoindre l'association
					</Button>
				</div>
			)}
		</div>
	);
};

export default DoubleList;
