import React, { Component } from 'react';
import { Link } from 'react-router-dom';


class Sidebar extends Component { 
	render() {
		return (
			<div className="col-md-3 d-none d-md-block bg-white p-4">
				<h1 className="text-center">Sidebar</h1>
			</div>
		);
	}
}

export default Sidebar;
