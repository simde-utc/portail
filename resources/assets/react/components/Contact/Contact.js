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

			case 'facebook':
			case 'twitter':
			case 'linkedin':
			case 'snapchat':
			case 'instagram':
				return ['fab', type];

			default:
				return type;
		}
	}

	static castValue(type, value) {
		switch (type) {
			case 'email':
				return (
					<a href={`mailto:${value}`} target="_blank" rel="noopener noreferrer">
						{value}
					</a>
				);

			case 'url':
			case 'facebook':
			case 'twitter':
			case 'linkedin':
			case 'snapchat':
			case 'instagram':
				return (
					<a href={value} target="_blank" rel="noopener noreferrer">
						{value}
					</a>
				);

			case 'other':
			default:
				return (
					<CopyToClipboard value={value}>
						<span>{value}</span>
					</CopyToClipboard>
				);
		}
	}

	render() {
		const { className, type, name, value } = this.props;

		return (
			<div className={className}>
				<CopyToClipboard value={value}>
					<span>
						<FontAwesomeIcon className="mr-2" icon={Contact.getIcon(type)} />
						<span className="font-weight-bold mr-2">{name}:</span>
					</span>
				</CopyToClipboard>
				{Contact.castValue(type, value)}
			</div>
		);
	}
}

export default Contact;
