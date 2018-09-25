import React from 'react';
import { connect } from 'react-redux';

import { Card, CardBody, CardTitle, CardSubtitle, CardFooter, Button } from 'reactstrap';
import AspectRatio from 'react-aspect-ratio';
import { findIndex } from 'lodash';

import Contact from './Contact';

class ContactList extends React.Component {
	getContacts(contacts) {
		return contacts.map(contact => (
			<Contact key={ contact.id } name={ contact.name } value={ contact.value } type={ contact.type.type }/>
		));
	}

	render() {
		return (
			<div className={ "ContactList " + this.props.className }>
				{ this.props.contacts.length > 0 ? (
					<div className="">
						{ this.getContacts(this.props.contacts) }
					</div>
				) : (
					<p>Aucun moyen de contact disponible</p>
				)}
			</div>
		);
	}
}

export default ContactList;
