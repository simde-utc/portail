/**
 * Création et gestion automatique des actions que l'on dispatch via redux
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

import React from 'react';

const Modal = ({ show, title, onClose, children }) => (
	<div className={`modal fade${show ? ' show' : ''}`} tabIndex="-1" role="dialog">
		<div className="modal-dialog modal-lg">
			<div className="modal-content">
				<div className="modal-header">
					{title}
					<button type="button" className="close" onClick={onClose}>
						&times;
					</button>
				</div>
				<div className="modal-body p-3">{children}</div>
			</div>
		</div>
	</div>
);

export default Modal;
