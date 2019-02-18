import React from 'react';
import Contact from './Contact';

class ContactList extends React.Component {
	static getContacts(contacts) {
		return contacts.map(contact => (
			<Contact
				key={contact.id}
				name={contact.name}
				value={contact.value}
				type={contact.type.type}
			/>
		));
	}

	render() {
		const { authorized, contacts, className } = this.props;

		return (
			<div className={`ContactList ${className}`}>
				{authorized &&
					(contacts.length > 0 ? (
						<div className="">{ContactList.getContacts(contacts)}</div>
					) : (
						<p>Aucun moyen de contact disponible</p>
					))}
			</div>
		);
	}
}

export default ContactList;
