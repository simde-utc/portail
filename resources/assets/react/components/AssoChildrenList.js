import React, { Component } from 'react';

class AssoChildrenList extends Component { 
	render() {
		const { asso, level } = this.props;
		return(
			<li>
				<span>{ asso.shortname }</span>
				{
					asso.children.length > 0 ? (
						<ul>
							{ asso.children.map(child => (<AssoChildrenList key={ child.id } asso={child} />)) }
						</ul>
					) : null
				}
			</li>

		);
	}
};

export default AssoChildrenList;
