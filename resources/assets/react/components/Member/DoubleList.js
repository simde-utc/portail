import React from 'react';

import List from './List';

class DoubleList extends React.Component {
	render() {
		return (
			<div className="container DoubleList">
				<List title="Liste des membres"
					members={ this.props.members.filter(member => member.pivot && !!member.pivot.validated_by) }
					roles={ this.props.roles }
					isMember={ this.props.isMember }
					fetched={ this.props.fetched } />
				<List title="Liste des membres en attente de validation"
					members={ this.props.members.filter(member => member.pivot && !member.pivot.validated_by) }
					roles={ this.props.roles }
					isMember={ this.props.isMember }
					fetched={ this.props.fetched } />
			</div>
		);
	}
}

export default DoubleList;
