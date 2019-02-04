import React from 'react';

import List from './List';

class DoubleList extends React.Component {
	render() {
		var notAcceptedMembers = this.props.members.filter(member => member.pivot && !member.pivot.validated_by);

		return (
			<div className="container DoubleList">
				<List title="Liste des membres"
					{ ...this.props }
					members={ this.props.members.filter(member => member.pivot && !!member.pivot.validated_by) }
				/>
				{ notAcceptedMembers.length > 0 && (
					<List title="Liste des membres en attente de validation"
						{ ...this.props }
						members={ notAcceptedMembers }
					/>
				)}
			</div>
		);
	}
}

export default DoubleList;
