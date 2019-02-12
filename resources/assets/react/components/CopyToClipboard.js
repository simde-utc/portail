/**
 * Ajout d'une fonctionnalité de copie automatique.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

import React from 'react';
import { NotificationManager } from 'react-notifications';
import Copy from 'react-copy-to-clipboard';

export default class CopyToClipboard extends React.Component {
	notify() {
		const { value } = this.props;

		NotificationManager.success(
			<span>
				Copie avec succès de <i>{value}</i>
			</span>,
			'Copier dans le presse-papier'
		);
	}

	render() {
		const { value, children } = this.props;

		return (
			<Copy.CopyToClipboard text={value} onCopy={this.notify.bind(this)}>
				{children}
			</Copy.CopyToClipboard>
		);
	}
}
