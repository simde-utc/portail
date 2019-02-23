import React from 'react';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';

import CopyToClipboard from '../CopyToClipboard';

class Contact extends React.Component {
	static getIcon(type) {
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
		const { className, type, name, value } = this.props;

		return (
			<div className={className}>
				<FontAwesomeIcon className="mr-2" icon={Contact.getIcon(type)} />
				<span className="font-weight-bold mr-2">{name}:</span>
				<CopyToClipboard value={value}>
					<span>{value}</span>
				</CopyToClipboard>
			</div>
		);
	}
}

export default Contact;
