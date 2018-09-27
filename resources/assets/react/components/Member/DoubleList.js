import React from 'react';

import List from './List';

class DoubleList extends React.Component {
	render() {
		return (
			<div className="container DoubleList">
				<List title="Liste des membres"
					{ ...this.props }
					members={ this.props.members.filter(member => member.pivot && !!member.pivot.validated_by) }
				/>
				<List title="Liste des membres en attente de validation"
					{ ...this.props }
					members={ this.props.members.filter(member => member.pivot && !member.pivot.validated_by) }
				/>
			</div>
		);
	}
}

export default DoubleList;
