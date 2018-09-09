import React from 'react';
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
		return (
			<div>
				<button className="nav-link admin dropdown-toggle" onClick={ (e) => this.show(e) }>
					{ this.props.title }
				</button>
				{ this.state.show ? (
					<div className="dropdown-menu d-block"
						ref={element => { this.dropdownMenu = element }}
					>
						{ this.props.children }
					</div>
				) : null }
			</div>
		);
	}
}

Dropdown.propTypes = {
	title: PropTypes.string.isRequired,
	children: PropTypes.arrayOf(PropTypes.element).isRequired,   
}

export default Dropdown;
