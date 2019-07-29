/**
 * Dispay a dropdown menu.
 *
 * @author Alexandre Brasseur <abrasseur.pro@gmail.com>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Natan Danous <natous.danous@hotmail.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

import React from 'react';
import { Button } from 'reactstrap';
import PropTypes from 'prop-types';

class Dropdown extends React.Component {
	constructor(props) {
		super(props);
		this.state = {
			show: false,
		};
		this.closeMenu = this.closeMenu.bind(this);
	}

	show(event) {
		event.preventDefault();
		this.setState({ show: true }, () => {
			document.addEventListener('click', this.closeMenu);
		});
	}

	closeMenu(event) {
		if (!this.dropdownMenu.contains(event.target)) {
			this.setState({ show: false }, () => {
				document.removeEventListener('click', this.closeMenu);
			});
		}
	}

	render() {
		const { title, children } = this.props;
		const { show } = this.state;

		return (
			<div>
				<Button className="nav-link admin dropdown-toggle" onClick={e => this.show(e)}>
					{title}
				</Button>
				{show ? (
					<div
						className="dropdown-menu d-block"
						ref={element => {
							this.dropdownMenu = element;
						}}
					>
						{children}
					</div>
				) : null}
			</div>
		);
	}
}

Dropdown.propTypes = {
	title: PropTypes.string.isRequired,
	children: PropTypes.arrayOf(PropTypes.element).isRequired,
};

export default Dropdown;
