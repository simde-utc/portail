import React from 'react';

import List from './List';

const DoubleList = props => {
	const { members } = props;
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
		</div>
	);
};

export default DoubleList;
