import React from 'react';
import { Link } from 'react-router-dom';
import { NotificationManager } from 'react-notifications';
import Copy from 'react-copy-to-clipboard';

export default class CopyToClipboard extends React.Component {
	notify() {
		NotificationManager.success((
			<span>Copie avec succès de <i>{ this.props.value }</i></span>
		), 'Copier dans le presse-papier')
	}

	render() {
		return (
			<Copy.CopyToClipboard text={ this.props.value }
				onCopy={ this.notify.bind(this) }>
				{ this.props.children }
			</Copy.CopyToClipboard>
		);
	}
}
