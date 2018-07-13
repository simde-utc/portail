import React, { Component } from 'react';
import { Link } from 'react-router-dom';

class AssoChildrenList extends Component { 
	render() {
		const { asso, level } = this.props;
		let liClass = 'list-item'
		if (level == 1) liClass += ' col'
		if (level > 2) 	liClass += ' list-subitem'
		return(
			<li className={ liClass }>
				<Link className={ (level == 1) ? 'list-header' : 'list-link' } 
					to={ 'assos/' + asso.login }>{ asso.shortname }</Link>
				{ asso.children.length > 0 ? (
					<ul className="list-row">
						{ asso.children.map(child => (
							<AssoChildrenList key={ child.id } 
								asso={child} level={level+1} />
							)) }
					</ul>
				) : null }
			</li>

		);
	}
};

export default AssoChildrenList;
