import React from 'react';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'

import CopyToClipboard from '../CopyToClipboard';

class Contact extends React.Component {
	getIcon(type) {
		switch (type) {
			case 'email':
				return 'envelope';

			case 'other':
				return 'asterix';

			case 'url':
				return 'globe';

			default:
				return type;
		}
	}

	render() {
		return (
			<div className={ this.props.className }>
				<FontAwesomeIcon className="mr-2" icon={ this.getIcon(this.props.type) } />
				<span className="font-weight-bold mr-2">{ this.props.name }:</span>
					<CopyToClipboard value={ this.props.value }>
						<span>{ this.props.value }</span>
	        </CopyToClipboard>
			</div>
		)
	}
}

export default Contact;
